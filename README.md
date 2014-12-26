Czech inclension (declension), české skloňování
===============================================

This extension should provide same functionality and API
as [this PHP extension](https://github.com/Mikulas/inflection-ext). Do note however that c extension performs way better.

Usage
-----

Install with [composer](https://getcomposer.org/):

```bash
composer require mikulas/inflection
```

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$inflection = new Inflection();
$inflected = $inflection->inflect('kožená bunda');

echo "Natrhnul jsem si $inflected[4]";
// Natrhnul jsem si koženou bundu

echo "$inflected[11] jsou nejlepší";
// kožené bundy jsou nejlepší
```

Based on original work by [Pavel Sedlák](http://www.pteryx.net/sklonovani.html), Tomáš Režnar and [Heureka](https://github.com/heureka/inflection).

This library is about 6 times faster then Heureka implementation and 9 times faster then the original implementation.

Links
-----

- http://prirucka.ujc.cas.cz/
- http://www.pteryx.net/sklonovani.html
- https://en.wikipedia.org/wiki/Czech_declension
- https://en.wikipedia.org/wiki/Grammatical_gender

License
-------

GPL 2.1
