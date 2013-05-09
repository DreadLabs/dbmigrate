<?php
namespace DreadLabs\Dbmigrate\Domain\Model;

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
 * Depicts the domain model "change".
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class Change {

	public static $idFormat = '%04d';

	public static $nameFormat = '%date%-%username%-%changeId%.sql';

	protected static $fileSizeBreakPoint = 1024;

	protected static $fileSizeUnits = array(' Byte', ' KB', ' MB');

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Backend\User
	 */
	protected $user = NULL;

	protected $id = '';

	protected $name = '';

	protected $storageLocation = '';

	public function injectUser(\DreadLabs\Dbmigrate\Backend\User $user) {
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

	public function setStorageLocation($storageLocation) {
		$this->storageLocation = $storageLocation;
	}

	public function getStorageLocation() {
		return $this->storageLocation;
	}

	public function belongsToUser() {
		$fileNamePattern = '/^(.*)' . $this->user->getUserName() . '(.*)$/';

		return 1 === preg_match($fileNamePattern, $this->name);
	}

	public function getSize() {
		$fileInformation = stat($this->storageLocation);

		$maxSize = count(self::$fileSizeUnits);
		$i = 0;

		$fileSize = $fileInformation['size'];
		$fileSizeUnit = self::$fileSizeUnits[$i];

		while ($fileSize > self::$fileSizeBreakPoint || $i === $maxSize) {
			$i = $i + 1;
			$fileSizeUnit = self::$fileSizeUnits[$i];
			$fileSize = $fileSize / self::$fileSizeBreakPoint;
		}

		return round($fileSize, 1) . $fileSizeUnit;
	}

	public function getContent() {
		$content = '';

		$fh = @fopen($this->storageLocation, 'r');

		if (FALSE === $fh) {
			$msg = sprintf('The selected file %s could not be opened. Check directory permissions!', $this->name);
			throw new \Exception($msg, 1363976580);
		}

		while (FALSE === feof($fh)) {
			$content .= fread($fh, 8192);
		}

		@fclose ($fh);

		return $content;
	}
}
?>