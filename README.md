[![Build Status](https://travis-ci.org/heureka/inflection.svg)](https://travis-ci.org/heureka/inflection)

Czech inclension (declension), české skloňování
===============================================

This extension should provide same functionality and API
as [this PHP extension](https://github.com/Mikulas/inflection-ext).

Usage
-----

Install with [composer](https://getcomposer.org/):

```bash
composer require heureka/inflection
```

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$inflection = new Inflection();
$inflected = $inflection->inflect('vlastní píseček');

echo "Nebudu si hrát jen na $inflected[6], ale kopat za komunitu";
// Nebudu si hrát jen na vlastním písečku, ale kopat za komunitu

echo "$inflected[8] nejsou vždy to nejlepší";
// vlastní písečky nejsou vždy to nejlepší

```

Based on original work by [Pavel Sedlák](http://www.pteryx.net/sklonovani.html), Tomáš Režnar.

This library is about 6 times faster then first Heureka implementation and 9 times faster then the original implementation.

Links
-----

- http://prirucka.ujc.cas.cz/
- http://www.pteryx.net/sklonovani.html
- https://en.wikipedia.org/wiki/Czech_declension
- https://en.wikipedia.org/wiki/Grammatical_gender

License
-------

GPL 2.1

Huge thanks [mikulas](https://github.com/mikulas/inflection) for his great job with improvements.
