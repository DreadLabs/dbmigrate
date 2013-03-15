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

	public function toggleTable() {
		$this->injectUser();

		$tableName = t3lib_div::_GP('table');

		$currentTables = $this->user->getUserConfiguration('dbmigrate:logging:tables', array());

		if (FALSE === isset($currentTables[$tableName])) {
			$currentTables[$tableName] = TRUE;
		} else {
			unset($currentTables[$tableName]);
		}

		$this->user->setUserConfiguration('dbmigrate:logging:tables', $currentTables);
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

	public function isTableActive($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->injectUser();

		$ajaxObject->setContentFormat('json');

		$newIcon = '';

		$tableName = t3lib_div::_GP('table');
		$icon = t3lib_div::_GP('icon');

		$currentTables = $this->user->getUserConfiguration('dbmigrate:logging:tables', array());

		if (TRUE === isset($currentTables[$tableName])) {
			$newIcon = t3lib_iconWorks::getSpriteIcon($icon, array(), array('status-overlay-hidden' => array()));
		} else {
			$newIcon = t3lib_iconWorks::getSpriteIcon($icon);
		}

		$result = array(
			'icon' => $newIcon
		);

		$ajaxObject->setContent($result);
	}
}
?>