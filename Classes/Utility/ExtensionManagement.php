<?php
class Tx_Dbmigrate_Utility_ExtensionManagement implements t3lib_Singleton {
	protected static $ajaxControllers = array(
		'tx_dbmigrate::enable_logging' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->enableLogging',
		'tx_dbmigrate::disable_logging' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->disableLogging',
		'tx_dbmigrate::toggle_table' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->toggleTable',
		'tx_dbmigrate::is_logging_enabled' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->isLoggingEnabled',
		'tx_dbmigrate::is_logging_disabled' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->isLoggingDisabled',
		'tx_dbmigrate::is_table_active' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->isTableActive',
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