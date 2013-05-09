<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager\Action;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke (tommy@van-tomas.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \DreadLabs\Dbmigrate\Domain\Repository\ChangeRepository;

/**
 * Task center task action for cloning a migration/change repository into a secondary T3 instance.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class CloneAction extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractAction {

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
		$this->raiseExceptionUnless(
			file_exists(ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation . '.git')),
			'The repository is already cloned!'
		);

		/* @var $command \DreadLabs\Dbmigrate\Task\RepositoryManager\Command\Git\Clone */
		$command = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Task\\RepositoryManager\\Command\\Git\\CloneCommand');
		$command->setArguments(array(
			'%repository%' => escapeshellcmd(GeneralUtility::_GP('repository')),
			'%targetPath%' => escapeshellcmd(ExtensionManagementUtility::extPath('dbmigrate', ChangeRepository::$storageLocation)),
		));
		$command->execute();
	}
}
?>