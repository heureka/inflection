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