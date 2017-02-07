<?php
abstract class ModelBase {
	public $ID;

	public static function Name() {
		return get_called_class();
	}

	protected static function GetDB() {
		return Database::getInstance()->con;
	}

	public static function LoadByID($id) {
		$db = static::GetDB();
		$query = sprintf('SELECT * FROM %s WHERE ID = :ID;', static::Name());
		$stm = $db->prepare($query);
		$params = array(':ID' => $id);
		if ($stm->execute ($params) && $stm->rowCount() != 0) {
			return $stm->fetchObject (static::Name());
		} else {
			return null;
		}
	}
	// $options['match'] = array( $key => $value [, ... ] );
	// $options['sort'] = array( $key, $order = 'DESC' );
	// $options['limit'] = array( $min [, $max] );
	public static function LoadAll($options = array()) {
		$db = static::GetDB();
		$params = array('table' => static::Name());
		if (isset($options['match']) && count($options['match'] > 0)) {
			$params['match'] = 'WHERE';
			$first = true;
			foreach ($options['match'] as $key => $value) {
				$params['match'] .= ($first ? '' : ' AND');
				if ($value === null) {
					//  If value is NULL
					$params['match'] .=  sprintf(' %s IS NULL', $key);
				} else {
					$params['match'] .= sprintf(' %s=\'%s\'', $key, $value);
				}
				$first = false;
			}
		}
		if (isset($options['sort'])) {
			$params['sort'] = 'ORDER BY ' . $options['sort'][0] . (isset($options['sort'][1]) ? ' ' . $options['sort'][1] : ' DESC');
		}
		if (isset($options['limit'])) {
			$params['limit'] = 'LIMIT ' . $options['limit'][0] . (isset($options['limit'][1]) ? ',' . $options['limit'][1] : '');
		}
		$query = sprintf('SELECT * FROM %s', implode(' ', $params));
		$stm = $db->prepare($query);
		if ($stm->execute()) {
			return $stm->fetchAll (PDO::FETCH_CLASS, static::Name());
		} else {
			return null;
		}
	}

	public function LoadChild($table, $column, $value) {
		$db = static::GetDB();
		$query = sprintf("SELECT * FROM %s WHERE $column = :$column;", $table);
		$stm = $db->prepare($query);
		$params = array(":$column" => $value);
		if ($stm->execute ($params) && $stm->rowCount() != 0) {
			return $stm->fetchObject ($table);
		} else {
			return null;
		}
	}

	public function LoadCollectionByKey($table, $key) {
		if (isset($this->ID)) {
			$db = self::GetDB();
			$query = sprintf('SELECT * FROM %s WHERE %s = :ID;', $table, $key);
			$stm = $db->prepare($query);
			$params = array(':ID' => $this->ID);

			if ($stm->execute ($params)) {
				return $stm->fetchAll (PDO::FETCH_CLASS, $table);
			} else {
				return null;
			}
		} else {
			return null;
			//die('Tried to load a collection of an unloaded object');
		}
	}
	protected function GetFields() {
		$db = self::GetDB();
		$query = sprintf('DESCRIBE %s', $this->Name());
		$stm = $db->prepare($query);

		if ($stm->execute ()) {
			return $stm->fetchAll(PDO::FETCH_COLUMN, 0);
		} else {
			return null;
		}
	}
	protected function GetFieldsPlaceholders($field) {
		return ':' . $field;
	}
	public function Save() {
		$fields = $this->GetFields();
		//  Remove ID col
		array_shift($fields);
		$placeholders = array_map('self::GetFieldsPlaceholders', $fields);
		if (isset($this->ID)) {
			$this->Update($fields, $placeholders);
		} else {
			$this->Insert($fields, $placeholders);
		}
	}
	protected function Insert($fields, $placeholders) {
		$db = self::GetDB();
		$params = array();
		for ($i = 0; $i < count($fields); $i++) {
			$params[$placeholders[$i]] = $this->$fields[$i];
		}
		// Append `` to fields for special case column names
		array_walk($fields, function(&$value, $key) { $value = "`$value`"; });
		$query = sprintf('INSERT INTO %s (%s) VALUES (%s);', $this->Name(), implode(',', $fields), implode(',', $placeholders));
		$stm = $db->prepare($query);
		if ($stm->execute($params)) {
			$this->GetID();
			return $stm->rowCount();
		} else {
			var_dump($stm->errorInfo());
			die('Insert failed');
		}
	}
	protected function Update($fields, $placeholders) {
		$db = self::GetDB();
		for ($i = 0; $i < count($fields); $i++) {
			$kvp[$i] = sprintf('`%s`=%s', $fields[$i], $placeholders[$i]);
			$params[$placeholders[$i]] = $this->$fields[$i];
		}
		$query = sprintf('UPDATE %s SET %s WHERE ID=:ID;', $this->Name(), implode(',', $kvp));
		$params[':ID'] = $this->ID;
		$stm = $db->prepare($query);
		if ($stm->execute($params)) {
			return $stm->rowCount();
		} else {
			die('Update failed');
		}
	}
	public function Delete() {
		$db = self::GetDB();
		$query = sprintf('DELETE FROM %s WHERE ID=:ID;', $this->Name());
		$params[':ID'] = $this->ID;
		$stm = $db->prepare($query);
		if ($stm->execute($params)) {
			return $stm->rowCount();
		} else {
			die('Delete failed');
		}
	}
	protected function GetID() {
		$db = self::GetDB();
		$stm = $db->prepare('SELECT LAST_INSERT_ID();');
		if ($stm->execute()) {
			$this->ID = $stm->fetchColumn();
		} else {
			die('Could not get last ID');
		}
	}
}
