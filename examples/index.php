<?php
require '../src/SSCrawler.php';

use SS\Crawler as Crawler;

$crawler = new Crawler(
    'https://cuongtobi.github.io/scraping-don-gian-trong-php-bang-curl.html',
    ['User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36'],
);

echo "<pre>";
var_dump($crawler->getPageTitle());
echo "</pre>";
