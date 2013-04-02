<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractCommand.php');

class Tx_Dbmigrate_Task_RepositoryManager_Command_Git_Init extends Tx_Dbmigrate_Task_RepositoryManager_AbstractCommand {

	protected $commandTemplate = 'git init %targetPath% 2>&1';

	protected $errorPreface = 'The repository initialization failed. Please see the following output for further details:';
}
?>
