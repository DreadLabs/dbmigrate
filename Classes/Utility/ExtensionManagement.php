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

	public static function addTCEMainEntryPoints($_EXTKEY) {
		$hookClass = t3lib_extMgm::extPath($_EXTKEY, 'Classes/Database/TceMainTransactionHandler.php:Tx_Dbmigrate_Database_TceMainTransactionHandler');

		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = $hookClass;
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = $hookClass;
	}

	public static function addQueryProcessors() {
#		$preProcessor = 'EXT:dbmigrate/Classes/Database/QueryPreProcessor.php:Tx_Dbmigrate_Database_QueryPreProcessor';
		$postProcessor = 'EXT:dbmigrate/Classes/Database/QueryPostProcessor.php:Tx_Dbmigrate_Database_QueryPostProcessor';

#		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $preProcessor;
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $postProcessor;
	}
}
?>