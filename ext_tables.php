<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// -- styles & icons
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['name'] = 'dbmigrate';
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['stylesheetDirectories']['structure'] = 'EXT:' . ($_EXTKEY) . '/Resources/Public/Css/';
//$GLOBALS['TBE_STYLES']['stylesheets']['dbmigrate'] = t3lib_extMgm::siteRelPath('dbmigrate') . 'Resources/Public/Css/dbmigrate.css';

$dbmigrateIcons = array(
	'database' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/database.png',
	'database-commit' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/database-commit.png',
	'database-pull' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/database-pull.png',
	'database-review' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/database-review.png',
	'table-sys_refindex' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/table-sys_refindex.png',
	'table-tx_rtehtmlarea_acronym' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/table-tx_rtehtmlarea_acronym.png',
	'table-tx_scheduler_task' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dbmigrate') . 'Resources/Public/Images/table-tx_scheduler_task.gif',
);

\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($dbmigrateIcons, 'dbmigrate');

if (TYPO3_MODE == 'BE') {
	// register update signal for automatically open the commit dialog
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['updateSignalHook']['tx_dbmigrate::commitWizard'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dbmigrate') . 'Classes/Backend/UpdateSignal.php:DreadLabs\\Dbmigrate\\Backend\\UpdateSignal->commitWizard';
}
?>