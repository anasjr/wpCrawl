<?php
session_start();


use Crawler\Crawler;
use Database\Database;
use Dotenv\Dotenv;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

require __DIR__ . '/../../vendor/autoload.php';


// Load environment variables from .env file
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
$dotenv->load();

// Get database and browser instances
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];

$url = $_SESSION['crawler']['url'] ?? '';
// echo ($url);
$httpClient = HttpClient::create();
$browser = new HttpBrowser($httpClient);

// Crawl the website
$db = new Database($dbHost, $dbName, $dbUsername, $dbPassword);
$crawler = new Crawler($db->getConnection(), $url, $browser);
$crawler->crawlWebsite();
$_SESSION['crawler']['results'] = $crawler->getResults();
