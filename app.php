#!/usr/bin/env php
<?php

ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

require __DIR__ . '../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new ImportCommand());
$application->setDefaultCommand('app:import');
$application->run();