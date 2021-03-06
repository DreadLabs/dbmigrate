<?php
namespace DreadLabs\Dbmigrate\Task\RepositoryManager\Command;

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
 * MysqlDumpCommand.php
 *
 * Dumps specific tables of a TYPO3 database.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class MysqlDumpCommand extends \DreadLabs\Dbmigrate\Task\RepositoryManager\AbstractCommand {

	protected $commandTemplate = 'mysqldump -u%user% -h%host% -p%password% -c --no-create-db %database% %default% %additional% > %targetPath%%projectName%.sql';

	protected $errorPreface = 'The dumping of the the baseline file failed. Maybe the reason can be found in the output:';
}
?>