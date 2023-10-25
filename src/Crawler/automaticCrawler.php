<?php
/**
 * Automatic Crawl file.
 *
 * PHP version >=7.1
 *
 * @category Crawler
 * @package  WP_Crawler
 * @author   Anass Jaafri <anasjaafri6@gmail.com>
 * @license  MIT License
 * @link     https://github.com/anasjr/wpCrawl.git
 */

// Start a new session
session_start();

// Import necessary classes and libraries
use Crawler\Crawler;
use Database\Database;
use Dotenv\Dotenv;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

// Require Composer autoloader
require __DIR__ . '/../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
$dotenv->load();

// Get database and browser instances from environment variables
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];

// Get URL from session data or set it to an empty string
$url = $_SESSION['crawler']['url'] ?? '';

// Create an HTTP client and a browser instance
$httpClient = HttpClient::create();
$browser = new HttpBrowser($httpClient);

// Create a new Database instance with the provided credentials
$db = new Database($dbHost, $dbName, $dbUsername, $dbPassword);

// Create a new Crawler instance with the database connection, base URL, and browser
$crawler = new Crawler($db->getConnection(), $url, $browser);

// Crawl the website and store the results in the session data
$crawler->crawlWebsite();
$_SESSION['crawler']['results'] = $crawler->getResults();
