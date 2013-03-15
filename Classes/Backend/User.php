<?php
class Tx_Dbmigrate_Backend_User implements t3lib_Singleton {

	public function isLoggingEnabled() {
		return TRUE === $this->getUserConfiguration('dbmigrate:logging:enabled', FALSE);
	}

	public function isLoggingDisabled() {
		return FALSE === $this->getUserConfiguration('dbmigrate:logging:enabled', FALSE);
	}

	public function setUserConfiguration($key, $value) {
		$GLOBALS['BE_USER']->uc[$key] = $value;

		$GLOBALS['BE_USER']->overrideUC();

		$GLOBALS['BE_USER']->writeUC();//$GLOBALS['BE_USER']->uc);
	}

	public function getUserConfiguration($key, $default) {
		$userConfig = $GLOBALS['BE_USER']->uc;

		if (TRUE === isset($userConfig[$key])) {
			$configurationValue = $userConfig[$key];
		} else {
			$configurationValue = $default;
		}

		return $configurationValue;
	}

	public function getUserName() {
		return $GLOBALS['BE_USER']->user['username'];
	}

	public function getRealName() {
		return $GLOBALS['BE_USER']->user['realName'];
	}
}
?>