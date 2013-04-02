<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractCommand.php');

class Tx_Dbmigrate_Task_RepositoryManager_Command_Git_RemoteAdd extends Tx_Dbmigrate_Task_RepositoryManager_AbstractCommand {

	protected $commandTemplate = 'cd %targetPath% && git remote add %remoteName% %remotePath% 2>&1';

	protected $errorPreface = 'The addition of the remote repository failed. Please see the following output for further details:';
}
?>
