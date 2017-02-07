<?php
class MetaProperty {
    public $Name;
    public $Type;
    public $Key;
    public $IsReference;

    public $MetaInfo;

    public function __construct($name, $type, $key, $ref) {
        $this->Name = $name;
        $this->Type = $type;
        $this->Key = $key;
        $this->IsReference = !!($ref);
        if ($this->IsReference) {
            $this->MetaInfo = new MetaInfo($ref, false);
        }
    }
    public function GetInputName($class, $id) {
        return sprintf(Editor::INPUT_ID_PATTERN,$class,$id,$this->Name);
    }
}
