<?php
define('BASE_URL', (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
define('ROOT_DIR', dirname(__FILE__));

//include '../src/WebPHPack.php';
require_once "../vendor/autoload.php";
$htmlsource = file_get_contents('bootstrap.html');
$webphpack = new Phreak\WebPHPack($htmlsource);
$webphpack->matchString = 'assets/';
$webphpack->jsPath = ROOT_DIR.'/assets/js';
$webphpack->cssPath = ROOT_DIR.'/assets/css';
$webphpack->outputPath = ROOT_DIR.'/assets/cache';
$webphpack->outputURL = BASE_URL.'/assets/cache';
$webphpack->caching = true;
$webphpack->httpush = true;
echo $webphpack->combineJS()->combineCSS()->output();
