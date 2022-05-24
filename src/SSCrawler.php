<?php
namespace SS;

use Exception;
use DOMDocument;
use DOMXpath;

class Crawler
{
    private $url;
    private $dom;
    private $requestHeaders;

    public function __construct($url, $requestHeaders)
    {
        $this->url = $url;
        $this->requestHeaders = $requestHeaders;
        $html = $this->getHtml();

        if (!$html) {
            throw new Exception('Get html error!', 1);
        }

        $doc = new DOMDocument;
        $doc->loadHTML($html);
        $this->dom = new DOMXpath($doc);
    }

    public function getElements($path)
    {
        $query = $this->buildQuery($path);

        $elements = $this->dom->query($query);

        if ($elements->count() === 0) {
            return false;
        }

        return $elements;
    }

    public function getElementsByTagName($tagName)
    {
        $query = "//$tagName";
        $elements = $this->dom->query($query);

        if ($elements->count() === 0) {
            return false;
        }

        return $elements;
    }

    public function getFirstElementByTagName($tagName)
    {
        $elements = $this->getElementsByTagName($tagName);

        if (!$elements) {
            return false;
        }

        return $elements->item(0);
    }

    public function getMetaTag($name)
    {
        $query = "//meta[contains(@name, \"$name\")]";
        $elements = $this->dom->query($query);

        if ($elements->count() === 0) {
            return false;
        }

        return $elements->item(0)->attributes->getNamedItem('content')->value;
    }

    public function getPageTitle()
    {
        $element = $this->getFirstElementByTagName('title');

        if (!$element) {
            return false;
        }

        return trim($element->textContent);
    }

    public function getPageDescription()
    {
        $element = $this->getMetaTag('description');

        if (!$element) {
            return false;
        }

        return $element;
    }

    private function buildQuery($path)
    {
        $pathArray = explode(' ', $path);

        $pathArray = array_map(function ($p) {
            if (strpos($p, '.') !== false) {
                $q = explode('.', $p);
                $q = $q[0] . '[contains(@class, "' . $q[1] . '")]';

                return $q;
            } elseif (strpos($p, '#') !== false) {
                $q = explode('#', $p);
                $q = $q[0] . '[contains(@id, "' . $q[1] . '")]';

                return $q;
            } else {
                return $p;
            }
        }, $pathArray);

        $query = implode('/', $pathArray);
        $query = "//$query";
        
        return $query;
    }

    private function getHtml()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $this->requestHeaders,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
