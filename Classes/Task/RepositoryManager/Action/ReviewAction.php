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

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dbmigrate', 'vendor/jdorn/sql-formatter/lib/SqlFormatter.php');

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \DreadLabs\Dbmigrate\Domain\Model\Change;

/**
 * Review.php
 *
 * Task center task action for reviewing a selected, uncommitted migration/change.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

class ReviewAction extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractAction {

	protected static $changeOptionTemplate = '<option value="%changeName%">%changeName% (%changeSize%)</option>';

	protected static $processOutputTemplate = '<textarea cols="%width%" rows="%height%">%content%</textarea>';

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

		$changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
		$changes = $changeRepository->findAll();

		foreach ($changes as $change) {
			$replacePairs = array(
				'%changeName%' => $change->getName(),
				'%changeSize%' => $change->getSize(),
			);

			$options[] = strtr(self::$changeOptionTemplate, $replacePairs);
		}

		return implode(LF, $options);
	}

	public function process() {
		$content = '';

		try {
			$changeRepository = GeneralUtility::makeInstance('DreadLabs\\Dbmigrate\\Domain\\Repository\\ChangeRepository');
			$change = $changeRepository->findOneByName(GeneralUtility::_GP('change'));

			$this->raiseExceptionUnless($change instanceof Change, 'The selected change is invalid/not existing!');

			$content = $change->getContent();

			$content = \SqlFormatter::format($content, FALSE);

			$replacePairs = array(
				'%width%' => GeneralUtility::_GP('width'),
				'%height%' => GeneralUtility::_GP('height'),
				'%content%' => htmlspecialchars($content),
			);

			$content = strtr(self::$processOutputTemplate, $replacePairs);
		} catch (\Exception $e) {
			$content .= $e->getMessage();
		}

		return $content;
	}
}
?>