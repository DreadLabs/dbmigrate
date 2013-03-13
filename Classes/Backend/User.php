<?php
class Tx_Dbmigrate_Backend_User implements t3lib_Singleton {
	public function enable() {
		$this->setUserConfiguraton('dbmigrate:logging:enabled', TRUE);
	}

	public function disable() {
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