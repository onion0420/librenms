#!/usr/bin/env php
<?php
/*
 * Copyright (C) 2014 Daniel Preussker <f0o@devilcode.org>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Alerts Cronjob
 * @author f0o <f0o@devilcode.org>
 * @copyright 2014 f0o, LibreNMS
 * @license GPL
 * @package LibreNMS
 * @subpackage Alerts

 * Edited 4/1/19
 * Changed to OOP
 * @author: Heath Barnhart <hbarnhart@kanren.net>
 */

use LibreNMS\Alert\RunAlerts;
use LibreNMS\Util\FileLock;

$init_modules = ['alerts', 'laravel'];
require __DIR__ . '/includes/init.php';

$options = getopt('d::');

$alerts_lock = FileLock::lockOrDie('alerts');

$alerts = new RunAlerts();

if (set_debug(isset($options['d']))) {
    echo "DEBUG!\n";
}

if (! defined('TEST') && \LibreNMS\Config::get('alert.disable') != 'true') {
    echo 'Start: ' . date('r') . "\r\n";
    echo 'ClearStaleAlerts():' . PHP_EOL;
    $alerts->clearStaleAlerts();
    echo "RunFollowUp():\r\n";
    $alerts->runFollowUp();
    echo "RunAlerts():\r\n";
    $alerts->runAlerts();
    echo "RunAcks():\r\n";
    $alerts->runAcks();
    echo 'End  : ' . date('r') . "\r\n";
}

$alerts_lock->release();
