<?php
class MetaInfo {
    public $ClassName;
    public $Properties;

    public $Collections;

    public function __construct($model, $recursive = true) {

        if (class_exists($model)) {
            $this->ClassName = $model;
            $db = Database::getInstance()->con;
            $fields = $this->GetFields($db);
            $references = $this->GetReferences($db);
            $this->Properties = array();
            foreach ($fields as $field) {
                // Skip sensetive fields >>
                if ($field['Field'] === 'Password') {
                    continue;
                }
                // Get Property Meta info
                $ref = $this->GetFieldReference($field['Field'], $references);
                // Skip self-recursion
                if (!$recursive && !!$ref && $ref == $this->ClassName) {
                    continue;
                }

                array_push($this->Properties, new MetaProperty(
                                                    $field['Field'],
                                                    $field['Type'],
                                                    $field['Key'],
                                                    $ref
                                                ));
            }
            $collections = $this->GetCollections($db);
            if ($recursive) {
                $this->Collections = array();
                foreach ($collections as $col) {
                    array_push($this->Collections, new MetaCollection(
                                                        $col['TABLE_NAME'],
                                                        $col['COLUMN_NAME']
                                                    ));
                }
            }
        }
    }

    private function GetFields($db) {
        $query = sprintf('DESCRIBE %s;', $this->ClassName);
		$stm = $db->prepare($query);

		if ($stm->execute ()) {
			return $stm->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return null;
		}
    }

    private function GetReferences($db) {
		$query = sprintf('SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                            WHERE
                                TABLE_NAME = \'%s\'
                            AND REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_SCHEMA = \'los\';', $this->ClassName);
  		$stm = $db->prepare($query);
		if ($stm->execute ()) {
			return $stm->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return null;
		}
    }
    private function GetCollections($db) {
        $query = sprintf('SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                            WHERE
                                REFERENCED_TABLE_NAME = \'%s\' AND TABLE_SCHEMA = \'los\';', $this->ClassName);
        $stm = $db->prepare($query);
        if ($stm->execute ()) {
			return $stm->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return null;
		}
    }

    private function GetFieldReference($field, $references) {
        $matches = array();
        foreach ($references as $ref) {
            if ($ref['COLUMN_NAME'] === $field) {
                array_push($matches, $ref);
            }
        }
        return (count($matches)=== 1 ? $matches[0]['REFERENCED_TABLE_NAME'] : false);
    }
    public function GetInstance($id) {
        $class = $this->ClassName;
        return $class::LoadByID($id);
    }
    public function GetList() {
        $class = $this->ClassName;
        return $class::LoadAll();
    }
}
