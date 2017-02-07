<?php
class Net {
	public static function GetServerIP($servername = false) {
		if (!$servername) $servername = $_SERVER['HTTP_HOST'];
		$dnsARecord = dns_get_record($servername,DNS_A);
		if ($dnsARecord) return $dnsARecord[0]['ip'];
	}
}