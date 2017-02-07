<?php
class NotesController extends ControllerBase {
    public function __construct() {
        if (!Auth::UserAuthorized(1)) {
            Router::Error(401);
        }
    }
    public function index() {
        $this->View->Scripts[] = URL_JS . 'note.js';

        $UserID = Auth::GetCurrentUserID();
        $Groups = NoteGroup::LoadAll(array(
            'match' => array('OwnerID' => $UserID),
            'sort' => array('Category', 'ASC')
        ));
        // Html::dump_debug($Groups);
        $this->View->AddData('Groups', $Groups);

    }
    public function group($id = null) {
        if (isset($id)) {
            $Group = NoteGroup::LoadById($id);
            $Tags = $Group->GetTags();
            $Notes = $Group->GetNotes();
            // Html::dump_debug($Notes);
            $this->View->Scripts[] = URL_JS . 'note.js';
            $this->View->AddData('Group',$Group);
            $this->View->AddData('TagSoup', $Tags);
            $this->View->AddData('Notes', $Notes);
        } else {
            Router::Route('/notes/index');
            die();
        }
    }
}
