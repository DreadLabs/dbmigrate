<?php
require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

class Tx_Dbmigrate_Task_RepositoryManager_Action_Review extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

	protected static $changesPath = 'Resources/Public/Migrations/';

	protected static $changeOptionTemplate = '<option value="%changeName%">%changeName% (%changeSize%)</option>';

	public function checkAccess() {
		return TRUE;
	}

	public function getOptions() {
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.review.change.label'),
			'field' => '<select name="change" size="10">' . $this->getChanges() . '</select>',
		);
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.review.textfieldwidth.label'),
			'field' => '<input name="width" size="2" value="80" />',
		);
		$this->options[] = array(
			'label' => $this->getTranslation('task.action.review.textfieldheight.label'),
			'field' => '<input name="height" size="2" value="20" />',
		);
	}

	protected function getChanges() {
		$options = array();

		$changes = t3lib_div::getFilesInDir(t3lib_extMgm::extPath('dbmigrate', self::$changesPath), 'sql', FALSE, 1, '');

		foreach ($changes as $change) {
			$filePath = t3lib_extMgm::extPath('dbmigrate', self::$changesPath . '/' . $change);
			$fileInformation = stat($filePath);
			$fileSizeInBytes = $fileInformation['size'];
			$fileSizeInMegaBytes = $fileSizeInBytes / 1024 / 1024;

			$replacePairs = array(
				'%changeName%' => $change,
				'%changeSize%' => round($fileSizeInMegaBytes, 1) . 'MB',
			);

			$options[] = strtr(self::$changeOptionTemplate, $replacePairs);
		}

		return implode(LF, $options);
	}

	public function process() {
		$content = '';

		try {
			$changePath = t3lib_extMgm::extPath('dbmigrate', self::$changesPath . '/' . t3lib_div::_GP('change'));
			$fh = @fopen($changePath, 'r');

			if (FALSE === $fh) {
				throw new Exception(sprintf('The selected file %s could not been opened. Check directory permissions!', t3lib_div::_GP('change')), 1363976580);
			}

			while (FALSE === feof($fh)) {
				$content .= fread($fh, 8192);
			}

			@fclose ($fh);
		} catch (Exception $e) {
			$content .= $e->getMessage();
		}

		return '<textarea cols="' . t3lib_div::_GP('width') . '" rows="' . t3lib_div::_GP('height') . '">' . htmlspecialchars($content) . '</textarea>';
	}
}
?>