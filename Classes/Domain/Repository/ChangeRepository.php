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
 * ChangeRepository.php
 *
 * Depecits all repository-like logic for the domain model "change".
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * Depicts all repository-like logic for the domain model "change".
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Tx_Dbmigrate_Domain_Repository_ChangeRepository {

	public static $storageLocation = 'Resources/Public/Migrations/';

	/**
	 * 
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $user = NULL;

	/**
	 * 
	 * @var array
	 */
	protected $userChanges = array();

	public function injectUser(Tx_Dbmigrate_Backend_User $user) {
		$this->user = $user;
	}

	public function add($content) {
		$filePath = $this->getActiveChangeStorageLocationOfUser();
		$fileHandle = @fopen($filePath, 'a');

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $fileHandle,
			'The file %s could not be opened for writing.',
			$filePath
		);

		$numberOfWrittenBytes = @fwrite($fileHandle, $content . ';' . chr(10));

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $numberOfWrittenBytes,
			'The query could not be written to the given file %s.',
			$filePath
		);

		$closingState = @fclose($fileHandle);

		$this->raiseExceptionUnlessFileOperationWasSuccessful(
			FALSE !== $closingState,
			'The file %s could not be closed.',
			$filePath
		);
	}

	protected function getActiveChangeStorageLocationOfUser() {
		if (FALSE === $this->user->hasActiveChange()) {
			throw new Exception('The user has no active change!', 1364321013);
		}

		$replacePairs = array(
			'%username%' => $this->user->getUserName(),
			'%changeId%' => $this->user->getChangeId(),
			'%changeType%' => $this->user->getChangeType(),
		);

		return $this->getChangeStorageLocation($replacePairs);
	}

	protected function raiseExceptionUnlessFileOperationWasSuccessful($fileOperation, $messageTemplate, $filePath) {
		if (FALSE === $fileOperation) {
			$msg = sprintf($messageTemplate, $filePath) . ' Please check the file/directory permissions!';
			throw new Exception($msg, 1364321227);
		}
	}

	public function findAll() {
		$filePath = t3lib_extMgm::extPath('dbmigrate', self::$storageLocation);
		$files = t3lib_div::getFilesInDir($filePath, 'sql', FALSE);

		$changes = array();

		foreach ($files as $file) {
			$change = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Model_Change');
			$change->setName($file);
			$changes[] = $change;
		}

		return $changes;
	}

	public function findAllByUser() {
		if (0 === count($this->userChanges)) {
			$changes = $this->findAll();

			foreach ($changes as $change) {
				$change->injectUser($this->user);

				if (FALSE === $change->belongsToUser()) {
					continue;
				}

				$this->userChanges[] = $change;
			}
		}

		return $this->userChanges;
	}

	public function countAllByUser() {
		$userChanges = $this->findAllByUser();

		return count($userChanges);
	}

	public function findNextFreeChangeOfUser() {
		$replacePairs = array(
			'%username%' => $this->user->getUserName(),
		);

		$i = 0;

		do {
			$changeId = sprintf(Tx_Dbmigrate_Domain_Model_Change::$idFormat, $i);

			$replacePairs['%changeId%'] = $changeId;
			$replacePairs['%changeType%'] = 'Command';

			$filePathCommand = $this->getChangeStorageLocation($replacePairs);

			$replacePairs['%changeType%'] = 'Data';

			$filePathData = $this->getChangeStorageLocation($replacePairs);

			$i++;
		} while(file_exists($filePathCommand) || file_exists($filePathData));

		return $changeId;
	}

	protected function getChangeStorageLocation($replacePairs) {
		$replacePairs['%date%'] = date('Ymd');

		$filePath = strtr(Tx_Dbmigrate_Domain_Model_Change::$nameFormat, $replacePairs);

		return t3lib_extMgm::extPath('dbmigrate', self::$storageLocation . $filePath);
	}

	public function findOneByName($name) {
		$change = NULL;

		$filePath = t3lib_extMgm::extPath('dbmigrate', self::$storageLocation . '/' . $name);

		if (TRUE === file_exists($filePath)) {
			$change = t3lib_div::makeInstance('Tx_Dbmigrate_Domain_Model_Change');
			$change->setName($name);
		}

		return $change;
	}
}
?>