<?php
include_once('header.php');
include_once('hw6-lib.php');
connect($db);

session_start();
session_destroy();
header('Location:add.php');
exit;
?>
