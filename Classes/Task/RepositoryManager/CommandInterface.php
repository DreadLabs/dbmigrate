<?php
interface Tx_Dbmigrate_Task_RepositoryManager_CommandInterface {
	public function setArguments(array $arguments);

	public function execute();
}
?>