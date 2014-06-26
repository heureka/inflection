Inflection for czech words
==========================
Class for basic inflection of czech words. For example names or surnames.

Usage
-----

Basic usage is add to your project via composer.

```php
$name = 'František';
$inflected = (new Inflection())->inflect($name, true);
print "Dobrý den " . $inflected[5]; //Dobrý den Františku
```

or

```php
require_one inflection/src/Inflection.php
$name = 'František';
$inflected = (new Inflection())->inflect($name, true);
print "Dobrý den " . $inflected[5]; //Dobrý den Františku
```


Třída je postavena na základě práce [Pavla Sedláka](http://www.pteryx.net/sklonovani.html), která byla zveřejněna pod GNU Lesser General Public License.
Z tohoto důvodu jsme se rozhodli ji upravit, zveřejnit a umožnit tak její využití, vylepšení a rozšíření dalším lidem.