<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/ActionInterface.php');

abstract class Tx_Dbmigrate_Task_RepositoryManager_AbstractAction implements Tx_Dbmigrate_Task_RepositoryManager_Action {
	protected $options = array();

	public function getName() {
		$parts = explode('_', get_class($this));

		return strtolower(array_pop($parts));
	}

	public function getOptions() {
		$name = $this->getName();
		$url = 'mod.php?M=user_task&SET[function]=sys_action.Tx_Dbmigrate_Task_RepositoryManager&select=' . $name . '&submit=' . $name;

		$optionsForm = '<form action="' . $url . '" method="post">';

		$optionsForm .= implode(LF, $this->options);

		$optionsForm .= '<input type="submit" name="execute" value="Execute" />';

		$optionsForm .= '</form>';

		return $optionsForm;
	}

	protected function executeCommand($command, $errorPreface = '') {
		$lastLine = t3lib_utility_Command::exec($command, $output, $exitCode);

		if (0 !== $exitCode) {
			$this->handleErrorCommand($command, $output, $lastLine, $errorPreface);
		}
	}

	protected function handleErrorCommand($command, $output, $lastLine, $prefaceMessage = '') {
		$msg = '';

		if ('' !== $prefaceMessage) {
			$msg .= $prefaceMessage;
		}

		$msg .= '<pre>' . $command . '</pre>';
		$msg .= '<pre>' . $lastLine . '</pre>';
		$msg .= '<pre>' . implode(LF, $output) . '</pre>';

		throw new Exception($msg);
	}
}
?>