<?php
class Tx_Dbmigrate_Backend_User implements t3lib_Singleton {

	public function getSessionData($key, $default) {
		$sessionData = $GLOBALS['BE_USER']->getSessionData($key);

		if (TRUE === isset($sessionData)) {
			$value = $sessionData;
		} else {
			$value = $default;
		}

		return $value;
	}

	public function setSessionData($key, $value) {
		$GLOBALS['BE_USER']->setAndSaveSessionData($key, $value);
	}

	public function getUserName() {
		return $GLOBALS['BE_USER']->user['username'];
	}

	public function getRealName() {
		return $GLOBALS['BE_USER']->user['realName'];
	}

	public function getChangeId() {
		return $this->getSessionData('dbmigrate:change:id', NULL);
	}

	public function setChangeId($changeId) {
		$this->setSessionData('dbmigrate:change:id', $changeId);
	}

	public function getChangeType() {
		return $this->getSessionData('dbmigrate:change:type', NULL);
	}

	public function setChangeType($changeType) {
		$this->setSessionData('dbmigrate:change:type', $changeType);
	}

	public function setChange($changeType, $changeId) {
		$this->setChangeType($changeType);
		$this->setChangeId($changeId);
	}
}
?>