<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// this hook flags the _correct_ entry point of TCE main
// processDatamap_beforeStart

// this hook flags the entry point of TCE main, right before the actual transaction starts...
// processDatamap_postProcessFieldArray

// this hook flags the exit point of TCE main, right after the actual transaction ends...
// processDatamap_afterDatabaseOperations

// this hook flags the _correct_ exit point of TCE main
// processDatamap_afterAllOperations

//$tceMainProcessor = 'EXT:dbmigrate/Classes/Database/Database/TCEMainProcessor.php:Tx_Dbmigrate_Database_TCEMainProcessor'
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = $tceMainProcessor;

$preProcessor = 'EXT:dbmigrate/Classes/Database/QueryPreProcessor.php:Tx_Dbmigrate_Database_QueryPreProcessor';
$postProcessor = 'EXT:dbmigrate/Classes/Database/QueryPostProcessor.php:Tx_Dbmigrate_Database_QueryPostProcessor';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $preProcessor;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $postProcessor;
?>