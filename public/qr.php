<?php
session_start();

error_reporting(E_ERROR);

include('../vendor/phpqrcode/qrlib.php'); 

$url =  'http://' . $_SERVER["SERVER_NAME"] . ($_SERVER["SERVER_PORT"] == 80 ? "" : (":" . $_SERVER["SERVER_PORT"])). "/" . $_GET["url"]; ; 

QRcode::png($url, false, QR_ECLEVEL_L, 7);