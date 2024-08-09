<?php

print (new \Exception())->getTraceAsString() . "\n";

require_once 'lib/BetterTypeSpecifier/TypeSpecifier.php';
require_once 'lib/BetterTypeSpecifier/TypeSpecifierFactory.php';
