<?php
interface Tx_Dbmigrate_Task_RepositoryManager_Action {
	public function checkAccess();

	public function getName();

	public function getOptions();

	public function renderForm();

	public function process();
}
?>