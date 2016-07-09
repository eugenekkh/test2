<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require_once __DIR__ . '/src/config.php';

$app = new Evgeny\Application($config);

return $app;
