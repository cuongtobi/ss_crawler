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

    public function getPageTitle()
    {
        $element = $this->getFirstElementByTagName('title');

        if (!$element) {
            return false;
        }

        return trim($element->textContent);
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
