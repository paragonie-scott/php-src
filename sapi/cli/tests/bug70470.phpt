--TEST--
Bug #70470 (Built-in server truncates headers spanning over TCP packets)
--SKIPIF--
<?php
include "skipif.inc";
?>
--XFAIL--
bug is not fixed yet
--FILE--
<?php
include "php_cli_server.inc";
php_cli_server_start("var_dump(getAllheaders());");

$fp = fsockopen(PHP_CLI_SERVER_HOSTNAME, PHP_CLI_SERVER_PORT, $errno, $errmsg, 0.5);

if (!$fp) {
	die("connect failed: " . $errmsg);
}

fwrite($fp, "GET / HTTP/1.1\r\n");
fwrite($fp, "Host: " . PHP_CLI_SERVER_HOSTNAME . "\r\n");
fwrite($fp, "Content");
fwrite($fp, "-Type: text/html; charset=UTF-8\r\n");
fwrite($fp, "Connection: clo");
fwrite($fp, "se\r\n\r\n");
while (!feof($fp)) {
	echo fgets($fp);
}
fclose($fp);
?>
--EXPECTF--
HTTP/1.1 200 OK
%a
array(3) {
  ["Host"]=>
  string(9) "localhost"
  ["Content-Type"]=>
  string(24) "text/html; charset=UTF-8"
  ["Connection"]=>
  string(5) "close"
}
