<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// -- styles & icons
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['name'] = 'dbmigrate';
$GLOBALS['TBE_STYLES']['skins']['dbmigrate']['stylesheetDirectories']['structure'] = 'EXT:' . ($_EXTKEY) . '/Resources/Public/Css/';
//$GLOBALS['TBE_STYLES']['stylesheets']['dbmigrate'] = t3lib_extMgm::siteRelPath('dbmigrate') . 'Resources/Public/Css/dbmigrate.css';

$dbmigrateIcons = array(
	'database' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/database.png',
	'table-sys_refindex' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/table-sys_refindex.png',
	'table-tx_rtehtmlarea_acronym' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/table-tx_rtehtmlarea_acronym.png',
	'table-tx_scheduler_task' => t3lib_extMgm::extRelPath('dbmigrate') . 'Resources/Public/Images/table-tx_scheduler_task.gif',
);

t3lib_SpriteManager::addSingleIcons($dbmigrateIcons, 'dbmigrate');
?>