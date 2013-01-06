<?php
// check if PHP is running
if ( false ) {
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error: PHP is not running</title>
</head>
<body>
	<h1>Error: PHP is not running</h1>
	<p>The DataTank requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
</body>
</html>
<?php
}

include_once("Installer.class.php");
include_once("InstallController.class.php");
include_once("Language.class.php");

$installer = Installer::getInstance();

// detect action from url
if(count($_GET)) {
    reset($_GET);
    $action = key($_GET);
    $installer->advance($action);
}

$installer->run();