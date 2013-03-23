<?php
class Tx_Dbmigrate_Controller_Toolbar implements t3lib_Singleton {

	/**
	 *
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $user = NULL;

	public function injectUser(Tx_Dbmigrate_Backend_User $user = NULL) {
		if (TRUE !== is_null($user)) {
			$this->user = $user;
		} else {
			$this->user = t3lib_div::makeInstance('Tx_Dbmigrate_Backend_User');
		}
	}
}
?>