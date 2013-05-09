<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager\Action;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class CommitWizardAction extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractAction {

	protected static $wizardItemOptionFieldTemplate = '<label class="tx-dbmigrate-repositorymanager-action-commitwizard-wizarditem">%field%<h3 class="uppercase"><span></span>%label%</h3></label>';

	protected static $wizardItemFieldTemplate = '<input class="tx-dbmigrate-repositorymanager-action-commitwizard-option tx-dbmigrate-repositorymanager-action-commitwizard-option-%option%" name="option" type="radio" title="%title%" value="%option%" />';

	protected static $wizardOptions = array('spellcheck', 'edit', 'add', 'delete', 'move');

	public function checkAccess() {
		return TRUE;
	}

	public function getOptions() {
		foreach (self::$wizardOptions as $wizardOption) {
			$this->options[] = array(
				'template' => self::$wizardItemOptionFieldTemplate,
				'label' => $this->getTranslation('task.action.commitwizard.field.' . $wizardOption . '.label'),
				'field' => strtr(self::$wizardItemFieldTemplate, array(
						'%option%' => $wizardOption,
						'%title%' => $this->getTranslation('task.action.commitwizard.field.' . $wizardOption . '.title'),
					)
				),
			);
		}

		$this->options[] = array(
			'label' => $this->getTranslation('task.action.commitwizard.field.description.label'),
			'field' => '<textarea name="description" cols="60" rows="5"></textarea>',
		);
	}

	public function process() {
		return 'Wizard finished! ' . GeneralUtility::_GP('option');
	}
}
?>