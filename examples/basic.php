<?php

require_once 'Inflection.php';

$name = 'František';
$inflected = (new Inflection())->inflect($name, true);

print "Dobrý den " . $inflected[5];