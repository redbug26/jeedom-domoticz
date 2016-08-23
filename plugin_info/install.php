<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function domoticz_install() {
	$cron = cron::byClassAndFunction('domoticz', 'pull');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('domoticz');
		$cron->setFunction('pull');
		$cron->setEnable(1);
		$cron->setDeamon(0);
		$cron->setDeamonSleepTime(2);
		$cron->setSchedule('*/15 * * * *'); // Toutes les 15 minutes
		$cron->save();
	}

	domoticz::syncEqLogicWithRazberry();
}

function domoticz_update() {
	$cron = cron::byClassAndFunction('domoticz', 'pull');
	if (is_object($cron)) {
		$cron->setDeamon(0);
		$cron->setSchedule('*/15 * * * *'); // Toutes les 10 minutes
		$cron->save();
	}
	log::add('domoticz', 'info', "update");
}

function domoticz_remove() {

	while (1) {
		$eqLogics = eqLogic::byType('domoticz');

		if (count($eqLogics) == 0) {
			break;
		}

		$eqLogics[0]->remove();
	}

	$cron = cron::byClassAndFunction('domoticz', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}

}
?>
