<?php
$config_path = __DIR__;
$util = $config_path .'/util.php';
require $util;
setup();
?>
<html>
<?php stampaIntestazione(); ?>
<body>
<?php stampaNavbar(); ?>