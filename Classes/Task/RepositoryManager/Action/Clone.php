<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

class Tx_Dbmigrate_Task_RepositoryManager_Action_Clone extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	protected static $command = 'git clone %repository% %targetPath% 2>&1';

	protected static $targetPath = 'Resources/Public/Migrations/';

	public function checkAccess() {
		return $GLOBALS['BE_USER']->isAdmin();
	}

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.clone.field.repository.label'),
			'field' => '<input name="repository" value="" size="60" />',
		);
	}

	public function process() {
		try {
			$content = $this->cloneRepository();
		} catch (Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}

	protected function cloneRepository() {
		if (TRUE === file_exists(t3lib_extMgm::extPath('dbmigrate', self::$targetPath . '.git'))) {
			throw new Exception('The repository is already cloned!');
		}

		$replacePairs = array(
			'%repository%' => escapeshellcmd(t3lib_div::_GP('repository')),
			'%targetPath%' => escapeshellcmd(t3lib_extMgm::extPath('dbmigrate', self::$targetPath)),
		);

		$command = strtr(self::$command, $replacePairs);

		$this->executeCommand($command, 'Clonging the repository failed. Please see the following output for further details:');
	}
}
?>