<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

Tx_Dbmigrate_Utility_ExtensionManagement::loadConfiguration();

if (TYPO3_MODE == 'BE') {
	Tx_Dbmigrate_Utility_ExtensionManagement::addAjaxControllers();

	Tx_Dbmigrate_Utility_ExtensionManagement::addToolbarItem($_EXTKEY);

	Tx_Dbmigrate_Utility_ExtensionManagement::addTCEMainEntryPoints($_EXTKEY);

	Tx_Dbmigrate_Utility_ExtensionManagement::addQueryProcessors();
}
?>