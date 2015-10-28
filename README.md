PageSpeed Insights API Parser
============================

This simple php module parses the results from [PageSpeed Insights API](https://developers.google.com/speed/docs/insights/v2/getting-started).

Installation
============

The best way to install the library is by using [Composer](http://getcomposer.org). Add the following to `composer.json` in the root of your project:

``` javascript
{
    "require": {
        "c4pone/pagespeed-parser": "~0.1",
    }
}
```

Then, on the command line:

``` bash
curl -s http://getcomposer.org/installer | php
php composer.phar install
```

Use the generated `vendor/.composer/autoload.php` file to autoload the library classes.

Basic usage
===================

For easy usage we use the pagespeed insights api client from [sgrodzicki](https://github.com/sgrodzicki/pagespeed)

```php
<?php

$url = 'http://www.codebuster.de';

$pageSpeed = new \PageSpeed\Insights\Service();
$pageSpeed->getResults($url, 'en_US', 'desktop', array('screenshot' => true));

$parser = new \c4pone\PageSpeed\Parser($pageSpeed->getResults());
$parser->getTitle();
$parser->getPageStats();
$parser->getRecommendations();

$screenshot = $parser->getScreenshot();
$screenshot->save('some/path/screenshot.jpg');

```

Tests
=====

[![Build Status](https://secure.travis-ci.org/c4pone/pagespeed.png?branch=master)](http://travis-ci.org/c4pone/pagespeed-parser)

The client is tested with phpunit; you can run the tests, from the repository's root, by doing:

``` bash
phpunit
```

Some tests may fail, due to requiring an internet connection (to test against a real API response). Make sure that
you are connected to the internet before running the full test suite.

