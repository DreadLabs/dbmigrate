<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager;

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

use \TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * AbstractCommand.php
 *
 * provides some common methods for all concrete command implementations.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class AbstractCommand implements \DreadLabs\Dbmigrate\Task\RepositoryManager\CommandInterface {

	/**
	 * you have to specify this in your concrete command implementation
	 * 
	 * @var string
	 */
	protected $commandTemplate = '';

	protected $arguments = array();

	protected $command = '';

	protected $output = '';

	protected $lastLine = '';

	protected $exitCode = 0;

	protected $errorPreface = '';

	public final function setArguments(array $arguments) {
		$this->arguments = $arguments;
		$this->validateArguments();
	}

	protected function validateArguments() {
		// @TODO
	}

	public final function execute() {
		$this->command = strtr($this->commandTemplate, $this->arguments);

		$this->raiseExceptionIf('' === $this->command, 'The command is empty. Nothing to execute!');

		$this->lastLine = CommandUtility::exec($this->command, $this->output, $this->exitCode);

		$this->raiseExceptionIf(0 !== $this->exitCode, $this->createErrorMessage($this->errorPreface));
	}

	protected function createErrorMessage() {
		$msg = '';

		if ('' !== $this->errorPreface) {
			$msg .= $preface;
		}

		$msg .= '<pre>' . $this->command . '</pre>';
		$msg .= '<pre>' . $this->lastLine . '</pre>';
		$msg .= '<pre>' . implode(LF, $this->output) . '</pre>';

		return $msg;
	}

	protected function raiseExceptionIf($message) {
		if (TRUE === $condition) {
			throw new \Exception($message, 1364459453);
		}
	}

	protected function raiseExceptionUnless($message) {
		if (FALSE === $condition) {
			throw new \Exception($condition, 1364459430);
		}
	}
}
?>