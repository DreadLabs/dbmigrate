<?php
namespace DreadLabs\Dbmigrate\Backend;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * User.php
 *
 * Provides access to backend user properties and business logic related information.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class User implements \TYPO3\CMS\Core\SingletonInterface {

	protected static $authorTemplate = '%name% <%email%>';

	/**
	 *
	 * @var \DreadLabs\Dbmigrate\Configuration
	 */
	protected $configuration = NULL;

	protected $changeId = NULL;

	public function injectConfiguration(\DreadLabs\Dbmigrate\Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getUserName() {
		return $GLOBALS['BE_USER']->user['username'];
	}

	public function getRealName() {
		return $GLOBALS['BE_USER']->user['realName'];
	}

	public function getEmail() {
		return $GLOBALS['BE_USER']->user['email'];
	}

	public function setChange($changeId) {
		$this->setChangeId($changeId);

		$this->setSessionData('dbmigrate:change:id', $this->changeId);
	}

	public function clearChange() {
		$this->setChangeId(NULL);

		$this->setSessionData('dbmigrate:change:id', $this->changeId);
	}

	public function getChangeId() {
		if (TRUE === is_null($this->changeId)) {
			$this->changeId = $this->getSessionData('dbmigrate:change:id', NULL);
		}

		return $this->changeId;
	}

	public function setChangeId($changeId) {
		$this->changeId = $changeId;
	}

	public function hasActiveChange() {
		$hasChangeId = FALSE === is_null($this->getChangeId());

		return $hasChangeId;
	}

	public function getSessionData($key, $default) {
		$sessionData = $GLOBALS['BE_USER']->getSessionData($key);

		if (TRUE === isset($sessionData)) {
			$value = $sessionData;
		} else {
			$value = $default;
		}

		return $value;
	}

	public function setSessionData($key, $value) {
		$GLOBALS['BE_USER']->setAndSaveSessionData($key, $value);
	}

	public function getAuthorRFC2822Formatted() {
		$name = $this->getUserName();

		$realName = $this->getRealName();

		if ('' !== $realName) {
			$name = $realName;
		}

		$email = sprintf('%s@%s', $this->getUserName(), GeneralUtility::getIndpEnv('HTTP_HOST'));

		if ('' !== $this->getEmail()) {
			$email = $this->getEmail();
		}

		$replacePairs = array(
			'%name%' => $name,
			'%email%' => $email,
		);

		return strtr(self::$authorTemplate, $replacePairs);
	}
}
?>