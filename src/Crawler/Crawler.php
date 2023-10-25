<?php
/**
 * Crawler class file.
 *
 * PHP version >=7.1
 *
 * @category Crawler
 * @package  WP_Crawler
 * @author   Anass Jaafri <anasjaafri6@gmail.com>
 * @license  MIT License
 * @link     https://github.com/anasjr/wpCrawl.git
 */

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
// Constructor to initialize the class properties.
    public function __construct(PDO $db, $baseUrl, HttpBrowser $browser)
    {
        $this->db = $db;
        $this->baseUrl = $baseUrl;
        $this->browser = $browser;
    }
// Method to crawl the website and store valid URLs in the database.
    public function crawlWebsite()
    {
// Create an HTTP client and a browser instance.
        $httpClient = HttpClient::create();
        $browser = new HttpBrowser($httpClient);
// Request the base URL and get all anchor links.
        $crawler = $browser->request('GET', $this->baseUrl);

        $links = $crawler->filter('a')->links();
// Clear previous results from the database.
        $this->clearPreviousResults();
 // Iterate through the links, store valid URLs in the database.
        foreach ($links as $link) {
            $url = $link->getUri();

            if (strpos($url, $this->baseUrl) !== false) {
                $this->storeResult($url);
            }
        }
        // Remove sitemap.html file if it exists.
        $sitemapPath = __DIR__ . '/sitemap.html';
        if (file_exists($sitemapPath)) {
            unlink($sitemapPath);
        }
    }

     // Method to clear previous results from the database.
    private function clearPreviousResults()
    {
        // Delete all rows from the crawl_results table.
        $stmt = $this->db->prepare("DELETE FROM crawl_results");
        $stmt->execute();
 
        // Reset the AUTO_INCREMENT value of the crawl_results table.
        $resetAutoIncrement = $this->db->prepare("ALTER TABLE crawl_results AUTO_INCREMENT = 1");
        $resetAutoIncrement->execute();
    }

      // Method to store a crawled URL in the database.
    public function storeResult($url)
    {
        // Get the current timestamp.
        $timestamp = date('Y-m-d H:i:s');
  
        // Prepare and execute the SQL query to insert the URL and timestamp.
        $stmt = $this->db->prepare("INSERT INTO crawl_results (url, timestamp) VALUES(:url, :timestamp)");
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->execute();
    }

   // Method to save the homepage content as an HTML file.
    public function saveHomepageAsHTML($url, $filePath)
    {
        try {
            // Request the homepage and get its HTML content.
            $crawler = $this->browser->request('GET', $url);
            $htmlContent = $crawler->html();

            // Save the HTML content to the specified file path.
            if (file_put_contents($filePath, $htmlContent) !== false) {
                return true;
            } else {
                throw new Exception('Failed to save HTML content to the file');
            }
        } catch (Exception $e) {
            // Log an error if saving the HTML content fails.
            error_log('Error while saving homepage as HTML: ' . $e->getMessage());
            return false;
        }
    }
     // Method to retrieve crawled results from the database.
    public function getResults()
    {
        // Prepare and execute the SQL query to select all URLs from crawl_results.
        $stmt = $this->db->prepare("SELECT * FROM crawl_results");
        $stmt->execute();
        $results = [];
 
        // Fetch URLs and add them to the results array.
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row['url'];
        }
 
        // Return the array of crawled URLs.
        return $results;
    }
    public static function isValidUrl($url)
    {
        return preg_match('/^https?:\/\/[a-zA-Z0-9\-\.]+\\.[a-zA-Z]{2,}(\/[a-zA-Z0-9\-._?\'\/\\+&%=#$=~]*)?$/', $url);
    }
}
