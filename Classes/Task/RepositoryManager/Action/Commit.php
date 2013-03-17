<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

class Tx_Dbmigrate_Task_RepositoryManager_Action_Commit extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	protected static $changesPath = 'Resources/Public/Migrations/';

	protected static $changeOptionTemplate = '<option value="%changeName%">%changeName%</option>';

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.subject.label'),
			'field' => '<input name="subject" value="" size="60" />',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.description.label'),
			'field' => '<textarea name="description" cols="60" rows="5"></textarea>',
		);

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commit.field.change.label'),
			'field' => '<select name="change" multiple="multiple" size="10">' . $this->getChanges() . '</select>',
		);
	}

	public function getChanges() {
		$options = array();

		$changes = t3lib_div::getFilesInDir(t3lib_extMgm::extPath('dbmigrate', self::$changesPath), 'sql', FALSE, 1, '');

		foreach ($changes as $change) {
			$replacePairs = array(
				'%changeName%' => $change,
			);

			$options[] = strtr(self::$changeOptionTemplate, $replacePairs);
		}

		return implode(LF, $options);
	}

	public function process() {
		try {
			$content = 'will commit...';
		} catch (Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}
}
?>