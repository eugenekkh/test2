<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
$app = require_once __DIR__ . '/bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $app->GetEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
