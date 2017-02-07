<?php
class NoteNode extends ModelBase {
    public $GroupID;
    public $Value;
    public $ParentID;

    public $Children;

    private function GetChildren() {
        $this->Children = NoteNode::LoadCollectionByKey($this->Name(), 'ParentID');
    }
    public function __construct() {
        $this->GetChildren();
    }
}
