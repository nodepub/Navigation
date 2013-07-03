<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('NodePub\Navigation', __DIR__.'/../lib');