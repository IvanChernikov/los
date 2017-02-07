<?php
class NoteGroup extends ModelBase {
    public $OwnerID;
    public $Title;
    public $Category;

    public function GetTags() {
        $params = array(
            'match' => array(
                'Group'
            ));
    }
    public function GetNotes() {
        $params = array(
            'match' => array(
                'GroupID' => $this->ID,
                'ParentID' => null
            )
        );
        return NoteNode::LoadAll($params);
    }
}
