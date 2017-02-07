<?php
class MetaCollection {
    public $ClassName;
    public $MetaInfo;
    public $KeyField;

    public function __construct($table,$key) {
        $this->ClassName = $table;
        $this->KeyField = $key;
        $this->MetaInfo = new MetaInfo($table, false);
    }

    public function GetList($id) {
        $class = $this->ClassName;
        return $class::LoadAll(array('match'=>array($this->KeyField => $id)));
    }

}
