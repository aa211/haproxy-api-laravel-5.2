<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class configuration extends Controller
{
	private $LocalPort;
	private $LocalSecurePort;
	private $Name;
	private $Config;
	private $ProccessOut;
	private $Rules;
	private $SecureRules;

	public function __construct(Request $request)
	{
		self::ProcessingTypes($request->Config, $request);
	}

    public function set(Request $request)
	{
		Self::SpecialProcessing();
		file_put_contents("/etc/haproxy/haproxy.cfg", $this->ProccessOut);

		$response = exec("setsid service haproxy restart &> /tmp/tmp ; cat /tmp/tmp ; rm -rf /tmp/tmp");

		if (file_exists("/var/run/haproxy.pid")){
			$cat = file_get_contents('/var/run/haproxy.pid');
			dd($cat);
			if (! is_numeric($cat)) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				 die('Error the server could not be started! a');
			}
		} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				die('Error the server could not be started! b');
		}

		if (preg_match("#.*(errors|fail.).*#im", $response)) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				die('Error the server could not be started! p');
		} else {
				die('Server has started successfuly!');
		}
	}


	private function SpecialProcessing()
	{
		$out = $this->ProccessOut;
		foreach ($this->tags as $tag) {
			$out = str_replace("{! " . $tag . " !}", $this->$tag, $out);
		}
		$this->ProccessOut = $out;
	}

	private function BuildRules($request)
	{
		if (( is_array( $request->IP ) && is_array( $request->Port )) || ( is_array( $request->SecureIP ) && is_array( $request->SecurePort ) )){
			$Rules = '';
			$SecureRules = '';
			for ($i=0; $i < count($request->IP); $i++) { 
				$Rules .= "server\t" . $request->Name.$i . "\t" . $request->IP[$i] . ":" . $request->Port[$i] . "\tcheck\n";
				if (isset( $request->SecureIP[$i] ) && ! empty( $request->SecureIP[$i] ) && isset( $request->SecurePort[$i] ) && ! empty( $request->SecurePort[$i] )) {
					$SecureRules .= "\tserver\t" . $request->Name.$i . "\t" . $request->SecureIP[$i] . ":" . $request->SecurePort[$i] . "\tcheck\n";
				}
			}
			if (! empty($SecureRules) ) {
				$this->SecureRules = $SecureRules;
			}
			$this->Rules = $Rules;
		} elseif (( ! empty( $request->IP ) && ! empty( $request->Port )) || ( ! empty( $request->SecureIP ) && ! empty( $request->SecurePort ) )) {
			$this->Rules = "server\t" . $request->Name . "1\t" . $request->IP . ":" . $request->Port . "\tcheck\n";
			if (isset( $request->SecureIP ) && ! empty( $request->SecureIP ) && isset( $request->SecurePort ) && ! empty( $request->SecurePort )) {
				$this->SecureRules = "\tserver\t" . $request->Name . "1\t" . $request->SecureIP . ":" . $request->SecurePort . "\tcheck\n";
			}
		} else {
			$error = "You must send one of <pre>\tIP\r\n\tPort</pre> OR <pre>\tSecureIP\r\n\tSecurePort</pre>\n\rAs a post request.";
			die($error);
		}
	}

	private function ProcessingTypes($config, $request)
	{
		$config = ( ! empty($request->Config) && isset($request->Config) ) ? ( $request->Config ) : 'http';
		$this->Config   = ( ! file_exists(public_path($config . '.conf')) ) ? public_path('http.conf') : public_path($config . '.conf');
		$this->ProccessOut = file_get_contents( $this->Config );
		switch ($config) {
			case 'http':
					$this->LocalPort = ( isset($request->LocalPort) && ! empty($request->LocalPort) ) ? $request->LocalPort : 80;
					$this->LocalSecurePort = ( isset($request->LocalSecurePort) && ! empty($request->LocalSecurePort) ) ? $request->LocalSecurePort : 443;
					$this->tags = ['LocalPort','Rules','LocalSecurePort','SecureRules'];
				break;
			case 'ftp':
					$this->LocalPort = ( isset($request->LocalPort) && ! empty($request->LocalPort) ) ? $request->LocalPort : 21;
					$this->tags = ['LocalPort','Rules'];
				break;
			case 'mysql':
					$this->LocalPort = ( isset($request->LocalPort) && ! empty($request->LocalPort) ) ? $request->LocalPort : 21;
					$this->tags = ['LocalPort','Rules'];
				break;			
			default:
					$this->LocalPort = ( isset($request->LocalPort) && ! empty($request->LocalPort) ) ? $request->LocalPort : 80;
					$this->LocalSecurePort = ( isset($request->LocalSecurePort) && ! empty($request->LocalSecurePort) ) ? $request->LocalSecurePort : 443;
					$this->tags = ['LocalPort','Rules','LocalSecurePort','SecureRules'];
				break;
		}
		self::BuildRules($request);
	}



}
