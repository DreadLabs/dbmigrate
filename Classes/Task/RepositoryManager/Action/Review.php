<?php
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

/**
 * Review.php
 *
 * Task center task action for reviewing a selected, uncommitted migration/change.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

require_once t3lib_extMgm::extPath('dbmigrate', 'Classes/Task/RepositoryManager/AbstractAction.php');

/**
 * Task center task action for reviewing a selected, uncommitted migration/change.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Task_RepositoryManager_Action_Review extends Tx_Dbmigrate_Task_RepositoryManager_AbstractAction {

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

		$changeRepository = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Repository_ChangeRepository');
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
			// @TODO: implement Tx_Dbmigrate_Domain_Repository_ChangeRepository::findOneByName()
			$changePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '/' . t3lib_div::_GP('change'));

			// @TODO: implement Tx_Dbmigrate_Domain_Model_Change::getContent()
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