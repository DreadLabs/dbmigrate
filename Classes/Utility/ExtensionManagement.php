<?php
class Tx_Dbmigrate_Utility_ExtensionManagement implements t3lib_Singleton {
	protected static $ajaxControllers = array(
		'tx_dbmigrate::enable_logging' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->enableLogging',
		'tx_dbmigrate::disable_logging' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->disableLogging',
		'tx_dbmigrate::toggle_table' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->toggleTable',
		'tx_dbmigrate::is_logging_enabled' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->isLoggingEnabled',
		'tx_dbmigrate::is_logging_disabled' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->isLoggingDisabled',
		'tx_dbmigrate::is_table_active' => 'EXT:dbmigrate/Classes/Backend/User.php:Tx_Dbmigrate_Backend_User->isTableActive',
	);

	public static function addAjaxControllers() {
		foreach (self::$ajaxControllers as $ajaxId => $controllerReference) {
			$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX'][$ajaxId] = $controllerReference;
		}
	}

	public static function addToolbarItem($_EXTKEY) {
		$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath($_EXTKEY, 'Classes/Backend/Toolbar.php');
	}
}
?>