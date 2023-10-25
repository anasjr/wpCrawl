<?php

namespace Crawler;

use Exception;
use PDO;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class Crawler
{
    private $db;
    private $baseUrl;
    private $browser;

    public function __construct(PDO $db, $baseUrl, HttpBrowser $browser)
    {
        $this->db = $db;
        $this->baseUrl = $baseUrl;
        $this->browser = $browser;
    }

    public function crawlWebsite()
    {
        $httpClient = HttpClient::create();
        $browser = new HttpBrowser($httpClient);

        $crawler = $browser->request('GET', $this->baseUrl);

        $links = $crawler->filter('a')->links();

        $this->clearPreviousResults();

        foreach ($links as $link) {
            $url = $link->getUri();

            if (strpos($url, $this->baseUrl) !== false) {
                $this->storeResult($url);
            }
        }
        $sitemapPath = __DIR__ . '/sitemap.html';
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }
    }

    private function clearPreviousResults()
    {
        $stmt = $this->db->prepare("DELETE FROM crawl_results");
        $stmt->execute();

        $resetAutoIncrement = $this->db->prepare("ALTER TABLE crawl_results AUTO_INCREMENT = 1");
        $resetAutoIncrement->execute();
    }

    public function storeResult($url)
    {
        $timestamp = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("INSERT INTO crawl_results (url,timestamp) VALUES(:url,:timestamp)");
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->execute();
    }

    public function saveHomepageAsHTML($url, $filePath)
    {
        try {
            $crawler = $this->browser->request('GET', $url);
            $htmlContent = $crawler->html();

            if (file_put_contents($filePath, $htmlContent) !== false) {
                return true;
            } else {
                throw new Exception('Failed to save HTML content to the file');
            }
        } catch (Exception $e) {
            error_log('Error while saving homepage as HTML: ' . $e->getMessage());
            return false;
        }
    }
    public function getResults()
    {
        $stmt = $this->db->prepare("SELECT * FROM crawl_results");
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row['url'];
        }
        return $results;
    }
}
