<?php

require_once '../src/Inflection.php';

$name = 'František';
$inflection = new Inflection();
$inflected = $inflection->inflect($name, true);

print "Dobrý den " . $inflected[5] . PHP_EOL;
