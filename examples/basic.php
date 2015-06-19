<?php

require_once __DIR__ . '/../src/Inflection.php';

$inflection = new Inflection();
$inflected = $inflection->inflect('desítková soustava');

print "Je potřeba znát " . $inflected[4] . PHP_EOL;
// Je potřeba znát desítkovou soustavu
