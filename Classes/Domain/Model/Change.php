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
 * Change.php
 *
 * Depict the domain model "change".
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */

/**
 * Depict the domain model "change".
 *
 * @author Thomas Juhnke <tommy@van-tomas.default>
 */
class Tx_Dbmigrate_Domain_Model_Change {

	public static $idFormat = '%04d';

	public static $nameFormat = '%date%-%username%-%changeId%-%changeType%.sql';

	/**
	 * 
	 * @var Tx_Dbmigrate_Backend_User
	 */
	protected $user = NULL;

	protected $id = '';

	protected $name = '';

	public function injectUser(Tx_Dbmigrate_Backend_User $user) {
		$this->user = $user;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function belongsToUser() {
		$fileNamePattern = '/^(.*)' . $this->user->getUserName() . '(.*)$/';

		return 1 === preg_match($fileNamePattern, $this->name);
	}

	public function getSize() {
		$filePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '/' . $this->name);
		$fileInformation = stat($filePath);

		$fileSizeUnits = array(' Byte', ' KB', ' MB');
		$maxSize = count($fileSizeUnits);
		$i = 0;

		$fileSize = $fileInformation['size'];
		$fileSizeUnit = $fileSizeUnits[$i];

		while ($fileSize > 1024 || $i === $maxSize) {
			$i = $i + 1;
			$fileSizeUnit = $fileSizeUnits[$i];
			$fileSize = $fileSize / 1024;
		}

		return round($fileSize, 1) . $fileSizeUnit;
	}

	public function getContent() {
		$content = '';

		$changePath = t3lib_extMgm::extPath('dbmigrate', Tx_Dbmigrate_Domain_Repository_ChangeRepository::$storageLocation . '/' . $this->name);

		$fh = @fopen($changePath, 'r');

		if (FALSE === $fh) {
			$msg = sprintf('The selected file %s could not been opened. Check directory permissions!', $this->name);
			throw new Exception($msg, 1363976580);
		}

		while (FALSE === feof($fh)) {
			$content .= fread($fh, 8192);
		}

		@fclose ($fh);

		return $content;
	}
}
?>