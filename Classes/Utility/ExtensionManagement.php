<?php
class Tx_Dbmigrate_Utility_ExtensionManagement implements t3lib_Singleton {
	protected static $shippedConfiguration = 'Configuration/Global/config.php';

	protected static $instanceConfiguration = 'dbmigrate_config.php';

	protected static $ajaxControllers = array(
#		'tx_dbmigrate::is_logging_disabled' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->isLoggingDisabled',
	);

	public static function loadConfiguration() {
		$defaultConfiguration = t3lib_extMgm::extPath('dbmigrate', self::$shippedConfiguration);
		include_once($defaultConfiguration);

		$instanceConfiguration = PATH_typo3conf . self::$instanceConfiguration;

		if (TRUE === @file_exists($instanceConfiguration)) {
			include_once($instanceConfiguration);
		}
	}

	public static function addAjaxControllers() {
		foreach (self::$ajaxControllers as $ajaxId => $controllerReference) {
			$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX'][$ajaxId] = $controllerReference;
		}
	}

	public static function addToolbarItem($_EXTKEY) {
		$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath($_EXTKEY, 'Classes/Backend/Toolbar.php');
	}

	public static function addTCEMainHooks($_EXTKEY) {
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