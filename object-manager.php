<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], true);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
