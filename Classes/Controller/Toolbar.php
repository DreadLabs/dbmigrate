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

	public function enableLogging($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->injectUser();

		$this->user->setUserConfiguration('dbmigrate:logging:enabled', TRUE);
	}

	public function disableLogging($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->injectUser();

		$this->user->setUserConfiguration('dbmigrate:logging:enabled', FALSE);
	}

	public function isLoggingEnabled($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->injectUser();

		$ajaxObject->setContentFormat('json');

		$result = array(
			'status' => TRUE === $this->user->isLoggingEnabled()
		);

		$ajaxObject->setContent($result);
	}

	public function isLoggingDisabled($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->injectUser();

		$ajaxObject->setContentFormat('json');

		$result = array(
			'status' => FALSE === $this->user->isLoggingEnabled()
		);

		$ajaxObject->setContent($result);
	}
}
?>