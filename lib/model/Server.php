<?php
class Server extends ModelBase {
	public $Type;
	public $Name;
	public $Address;
	public $ConnectionString;
	public $NeedsResolution;
	
	public $Handler;
	
	public static function LoadByID($id) {
		$Server = parent::LoadByID($id);
		if (!isset($Server)) {
			$Server = new Server();
		}
		$Server->GetHandler();
		return $Server;
	}
	
	public function ResolvedAddress() {
		return ($this->NeedsResolution ? Net::GetServerIP($this->Address) : $this->Address);
	}
	
	private function GetHandler() {
		switch ($this->Type) {
			case 'ts':
				$this->Handler = new HandlerTeamspeak($this);
				break;
			case 'cod':
				$this->Handler = new HandlerCallOfDuty($this);
				break;
			case 'mc':
				$this->Handler = new HandlerMinecraft($this);
				break;
			default:
				$this->Handler = new HandlerBase($this);
				break;
		}
	}
}