<?php

function domoticzGetConfigurationData($userId, $userPassword) {

	return domoticzGetModules($userId, $userPassword, 0);
}

function domoticzGetModules($ip, $port, $decode = 1) {

// http://musicbox.mons.tec-wl.be:8080/json.htm?type=devices&filter=all&used=true&order=Name
	//
	$url = "http://" . $ip . ":" . $port . "/json.htm?type=devices&filter=all&used=true&order=Name";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);

	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");

	$output = curl_exec($ch);

	curl_close($ch);

	if ($output == "") {
		echo "Invalid return";
	}

	if ($decode == 0) {
		return $output;
	}

	$domoticz = json_decode($output);

	return $domoticz->result;
}

function domoticzSendCommand($ip, $port, $idx, $switchCmd, $level) {

	$url = sprintf("http://%s:%s/json.htm?type=command&param=switchlight&idx=%d&switchcmd=%s&level=%s", $ip, $port, $idx, $switchCmd, $level);
	$jsonData = file_get_contents($url);

	$output = json_decode($jsonData);

	return $output;
}

?>
