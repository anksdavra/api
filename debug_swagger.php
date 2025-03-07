<?php
require 'vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([__DIR__ . '/app/Endpoint/V3']);
echo $openapi->toJson();
