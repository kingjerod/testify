<?php
require __DIR__ . '/vendor/autoload.php';

use Testify\Command\TestifyCommandFactory;
use Symfony\Component\Console\Application;

$factory = new TestifyCommandFactory();
$application = new Application();
$application->add($factory->create());
$application->run();