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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../3rdparty/domoticz.inc.php';

class domoticz extends eqLogic {

	public static function pull($_options) {

		domoticz::syncEqLogicWithRazberry();

		/*
	foreach (eqLogic::byType('Zeebase') as $Zeebase) {
	if (is_object($Zeebase)) {
	foreach ($Zeebase->getCmd() as $cmd) {
	if ($cmd->getName() == 'Etat' || $cmd->getName() == 'Etat Sensor') {
	$cmd->event($cmd->execute());
	}
	}
	}
	}
	 */
	}

	public static function pullSonde($_options) {
		/*
	foreach (eqLogic::byType('Zeebase') as $Zeebase) {
	if (is_object($Zeebase)) {
	foreach ($Zeebase->getCmd() as $cmd) {
	if ($cmd->getName() == 'Température' || $cmd->getName() == 'Humidité' || $cmd->getName() == 'Luminosité' || $cmd->getName() == 'Consommation Totale' || $cmd->getName() == 'Consommation Instantanée' || $cmd->getName() == 'Pluie' || $cmd->getName() == 'Pluie Tot' || $cmd->getName() == 'Vent' )
	$cmd->event($cmd->execute());
	}
	}
	}
	 */
	}

	public function postInsert() {

	}

	public static function deamonRunning() {

		$ip = config::byKey('ip', 'domoticz');
		$port = config::byKey('port', 'domoticz');

		$modules = domoticzGetModules($ip, $port);

		return is_array($modules);
	}

	public static function getConfigurationData($_serverId = 1) {

		$userId = config::byKey('userId', 'domoticz');
		$userPassword = config::byKey('userPassword', 'domoticz');

		return domoticzGetConfigurationData($userId, $userPassword);
	}

	public static function syncEqLogicWithRazberry($_serverId = 1) {

		$ip = config::byKey('ip', 'domoticz');
		$port = config::byKey('port', 'domoticz');

		$modules = domoticzGetModules($ip, $port);

		$eqLogics = eqLogic::byType('domoticz');

		foreach ($modules as $module) {

			$found = false;

			foreach ($eqLogics as $eqLogic) {
				if ($module->idx == $eqLogic->getConfiguration('idx')) {
					$eqLogic_found = $eqLogic;
					$found = true;
					break;
				}
			}

			if (!$found) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('domoticz');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->setName($module->Name);
				$eqLogic->setConfiguration('type', $module->Type);
				$eqLogic->setConfiguration('idx', $module->idx);
				$eqLogic->setConfiguration('SwitchType', $module->SwitchType);
				$eqLogic->save();

				$eqLogic = self::byId($eqLogic->getId());

				if ($module->SwitchType == "On/Off") {

					// On

					$domoticzCmd = new domoticzCmd();

					$domoticzCmd->setType('action');
					$domoticzCmd->setSubType('other');

					$domoticzCmd->setName("On");
					$domoticzCmd->setEqLogic_id($eqLogic->getId());
					$domoticzCmd->setConfiguration('idx', $module->idx);
					$domoticzCmd->setConfiguration('commandName', "On");

					$domoticzCmd->save();

					// Off

					$domoticzCmd = new domoticzCmd();

					$domoticzCmd->setType('action');
					$domoticzCmd->setSubType('other');

					$domoticzCmd->setName("Off");
					$domoticzCmd->setEqLogic_id($eqLogic->getId());
					$domoticzCmd->setConfiguration('idx', $module->idx);
					$domoticzCmd->setConfiguration('commandName', "Off");

					$domoticzCmd->save();

					// State

					$domoticzCmd = new domoticzCmd();

					$domoticzCmd->setType('info');
					$domoticzCmd->setSubType('binary');

					$domoticzCmd->setName("Etat");
					$domoticzCmd->setEqLogic_id($eqLogic->getId());

					$domoticzCmd->save();
				}

				if ($module->SwitchType == "??") {
				}

			} else {
				$eqLogic = $eqLogic_found;
			}

			if ($module->SwitchType == "On/Off") {

				foreach ($eqLogic->getCmd() as $command) {

					if ($command->getName() == "Etat") {
						$command->setCollectDate('');
						$command->event($module->Status == "On");
					}
				}

			}

			if ($module->SwitchType == "??") {
			}

			// fin
		}
	}
}

class domoticzCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function preSave() {
		/*
	if ($this->getConfiguration('userId') == '') {
	throw new Exception('user ne peut etre vide');
	}
	if ($this->getConfiguration('userPassword') == '') {
	throw new Exception('password ne peut etre vide');
	}
	 */

	}

	public function execute($_options = null) {

		$eqLogics = eqLogic::byType('domoticz');

		foreach ($eqLogics as $eqLogic0) {
			if ($this->getConfiguration('idx') == $eqLogic0->getConfiguration('idx')) {
				$eqLogic = $eqLogic0;
				break;
			}
		}

		if ($eqLogic->getConfiguration('SwitchType') == "On/Off") {

			$ip = config::byKey('ip', 'domoticz');
			$port = config::byKey('port', 'domoticz');
			$idx = $this->getConfiguration('idx');
			$switchCmd = $this->getConfiguration('commandName');
			$level = 0;

			$retour = domoticzSendCommand($ip, $port, $idx, $switchCmd, $level);

			if ($retour->status == "OK") {

				foreach ($eqLogic->getCmd() as $command) {

					if ($command->getName() == "Etat") {
						$command->setCollectDate('');
						$command->event($switchCmd == "On");
					}
				}
			}
		}

	}

	/*     * **********************Getteur Setteur*************************** */
}

?>
