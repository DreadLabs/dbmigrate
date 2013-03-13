<?php
class Tx_Dbmigrate_Backend_User implements t3lib_Singleton {

	public function enableLogging($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->setUserConfiguraton('dbmigrate:logging:enabled', TRUE);
	}

	public function disableLogging($ajaxParams, TYPO3AJAX $ajaxObject) {
		$this->setUserConfiguraton('dbmigrate:logging:enabled', FALSE);
	}

	public function toggleTable() {
		$tableName = t3lib_div::_GP('table');

		$currentTables = $this->getUserConfiguration('dbmigrate:logging:tables', array());

		if (FALSE === isset($currentTables[$tableName])) {
			$currentTables[$tableName] = TRUE;
		} else {
			unset($currentTables[$tableName]);
		}

		$this->setUserConfiguraton('dbmigrate:logging:tables', $currentTables);
	}

	public function isLoggingEnabled($ajaxParams, TYPO3AJAX $ajaxObject) {
		$ajaxObject->setContentFormat('json');

		$result = array(
			'status' => TRUE === $this->getUserConfiguration('dbmigrate:logging:enabled', FALSE)
		);

		$ajaxObject->setContent($result);
	}

	public function isLoggingDisabled($ajaxParams, TYPO3AJAX $ajaxObject) {
		$ajaxObject->setContentFormat('json');

		$result = array(
			'status' => FALSE === $this->getUserConfiguration('dbmigrate:logging:enabled', FALSE)
		);

		$ajaxObject->setContent($result);
	}

	public function isTableActive($ajaxParams, TYPO3AJAX $ajaxObject) {
		$ajaxObject->setContentFormat('json');

		$newIcon = '';

		$tableName = t3lib_div::_GP('table');
		$icon = t3lib_div::_GP('icon');

		$currentTables = $this->getUserConfiguration('dbmigrate:logging:tables', array());

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

	protected function setUserConfiguraton($key, $value) {
		$GLOBALS['BE_USER']->uc[$key] = $value;

		$GLOBALS['BE_USER']->overrideUC();

		$GLOBALS['BE_USER']->writeUC();//$GLOBALS['BE_USER']->uc);
	}

	protected function getUserConfiguration($key, $default) {
		$userConfig = $GLOBALS['BE_USER']->uc;

		if (TRUE === isset($userConfig[$key])) {
			$configurationValue = $userConfig[$key];
		} else {
			$configurationValue = $default;
		}

		return $configurationValue;
	}
}
?>