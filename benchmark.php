<?php

$loader = require __DIR__ . '/vendor/autoload.php';

$sumTimes = 0;
$count = 0;

$words = explode(' ', 'Tato verze dokáže skloňovat i jednoduchá sousloví, jako např. "anaerobní bakterie", "čtyřdobý spalovací motor", nebo "moje slovo", byly odstraněny některé chyby. Nefungují sousloví obsahující druhý pád, spojky a příslovce. Převážná většina slov se vyskloňuje spisovně, případy kdy se vygeneruje nepřesný (hovorový, málo frekventovaný) nebo dokonce nesprávný tvar jsou přesto dosud četné. U slov mužského rodu by bylo obtížné bez poměrně rozsáhlého slovníku rozlišit, zda se má použít životné nebo neživotné skloňování (někdy to také závisí na kontextu), proto je zde potřebný zásah uživatele. Kromě životného a neživotného skloňování by v některých případech bylo třeba rozlišit osobní a neosobní skloňování. Ve verzi 0.97 byla opravena některá nedořešená slova, uživateli byla dána možnost vynutit si změnu rodu, což může být užitečné např. při pokusu o skloňování příjmení. V takovém případě se však zvyšuje pravděpodobnost chyb nebo nepřesností. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod');

for ($i = 0; $i < 10; $i++)
{
	$start = microtime(TRUE);
	$lib = new Inflection();

	foreach ($words as $word)
	{
		$lib->inflect($word);
	}

	$duration = microtime(TRUE) - $start;
	echo count($words) . " words, \t";
	echo number_format($duration, 3) . " seconds, \t";
	echo number_format(memory_get_peak_usage(TRUE) / 1e6, 3) . " MB\n";

	$sumTimes += $duration;
	$count++;
}

echo "---------------\n";
$avg = $sumTimes / $count;
echo 'Avg duration:   ' . number_format($avg, 3) . " seconds\n";
