<?php

$app = require_once __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$app->handle($request);
