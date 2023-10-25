<?php

use Database\Database;
use Dotenv\Dotenv;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Crawler\Crawler;

session_start();
require __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];

$httpClient = HttpClient::create();
$browser = new HttpBrowser($httpClient);

$crawler = $_SESSION['crawler'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crawl'])) {
        $url = $_POST['url'];

        if (empty($url)) {
            $error = "URL is required to trigger a crawl.";
        } elseif (filter_var($url, FILTER_VALIDATE_URL)) {
            $db = new Database($dbHost, $dbName, $dbUsername, $dbPassword);
            $crawler = new Crawler($db->getConnection(), $url, $browser);
            $crawler->crawlWebsite();
            $homepageFilePath = __DIR__ . '/homepage.html';
            $crawler->saveHomepageAsHTML($url, $homepageFilePath);

            // Store necessary data in the session, not the PDO object
            $_SESSION['crawler'] = [
                'url' => $url,
                'results' => $crawler->getResults(),
            ];
            header("Location: index.php");
        } else {
            $error = "Invalid URL format.";
        }
    }

    // Handle Show Results button click event after crawling
    if (isset($_POST['showResults']) && isset($_SESSION['crawler'])) {
        $results = $_SESSION['crawler']['results'];
        $perPage = 10;
        $totalResults = count($results);
        $totalPages = ceil($totalResults / $perPage);

        // Get the current page number from the URL, default to page 1 if not set
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $current_page = max(1, min($totalPages, intval($current_page))); // Ensure the page number is within valid range

        $startIndex = ($current_page - 1) * $perPage;
        $displayedResults = array_slice($results, $startIndex, $perPage);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SEO Crawler Admin</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/index-style.css">
</head>

<body>
    <header>
        <h1>Hello, <?php echo $_SESSION['user']; ?></h1>
        <a href="logout.php" id="logoutLink">Logout</a>
    </header>

    <form id="crawlForm" method="post">
        <label for="url">Enter Website URL :</label>
        <input type="url" id="url" name="url"><br>

        <button type="submit" name="crawl" id="triggerButton" disabled>Trigger Crawl</button>
        <button type="submit" name="showResults" id="results">Show Results</button>
    </form>

    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($results)) : ?>
        <h2>Crawl Results:</h2>
        <ul>
            <?php foreach ($results as $result) : ?>
                <li><?php echo htmlspecialchars($result); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        // Function to handle input change event
        function handleInputChange() {
            var url = $('#url').val();
            var triggerButton = $('#triggerButton');
            // Enable or disable the button based on input value
            if (url.trim() === '') {
                triggerButton.prop('disabled', true);
            } else {
                triggerButton.prop('disabled', false);
            }
        }

        // Bind input change event to handleInputChange function
        $('#url').on('input', handleInputChange);

        // Initial check to disable the button if input is empty
        handleInputChange();
        // Function to trigger the crawl operation using AJAX
        function triggerCrawl() {

            $.ajax({
                url: '../src/Crawler/automaticCrawler.php',
                success: function(response) {
                    console.log('Automatic Crawl triggered successfully.');
                },
                error: function(xhr, status, error) {
                    console.error('Error triggering automatic crawl:', error);
                }

            });
        }
        $('#triggerButton').on('click', function(event) {

            triggerCrawl(); // Trigger the crawl immediately after the button click
        });
        // Call the triggerCrawl function every hour (3600000 milliseconds)
        setInterval(triggerCrawl, 3600000);
    });
</script>

</html>