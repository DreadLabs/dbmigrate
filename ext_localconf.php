<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

if (TYPO3_MODE == 'BE') {
	Tx_Dbmigrate_Utility_ExtensionManagement::addAjaxControllers();

	Tx_Dbmigrate_Utility_ExtensionManagement::addToolbarItem($_EXTKEY);

	Tx_Dbmigrate_Utility_ExtensionManagement::addTCEMainEntryPoints($_EXTKEY);

// 	$preProcessor = 'EXT:dbmigrate/Classes/Database/QueryPreProcessor.php:Tx_Dbmigrate_Database_QueryPreProcessor';
	$postProcessor = 'EXT:dbmigrate/Classes/Database/QueryPostProcessor.php:Tx_Dbmigrate_Database_QueryPostProcessor';

// 	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $preProcessor;
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $postProcessor;
}
?>