<?php

$loader = require __DIR__ . '/vendor/autoload.php';

$lib = new Inflection();

array_shift($argv);
foreach ($argv as $word)
{
    var_dumP($lib->inflect($word));
}
