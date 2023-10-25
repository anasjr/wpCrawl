<?php
session_start();

use Database\Database;
use Dotenv\Dotenv;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Crawler\Crawler;

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

function isValidUrl($url)
{
    return preg_match('/^https?:\/\/[a-zA-Z0-9\-\.]+\\.[a-zA-Z]{2,}(\/[a-zA-Z0-9\-._?\'\/\\+&%=#$=~]*)?$/', $url);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crawl'])) {
        $url = $_POST['url'];

        if (empty($url)) {
            $error = "URL is required to trigger a crawl.";
        } elseif (!isValidUrl($url)) {
            $urlError = "Invalid URL format. Please enter a valid URL.";
        } else {
            $db = new Database($dbHost, $dbName, $dbUsername, $dbPassword);
            $crawler = new Crawler($db->getConnection(), $url, $browser);
            $crawler->crawlWebsite();
            $homepageFilePath = __DIR__ . '/homepage.html';
            $crawler->saveHomepageAsHTML($url, $homepageFilePath);

            $_SESSION['crawler'] = [
                'url' => $url,
                'results' => $crawler->getResults(),
            ];
            header("Location: index.php");
        }
    }

    // Handle Show Results button click event after crawling
    if (isset($_POST['showResults']) && isset($_SESSION['crawler'])) {
        $results = $_SESSION['crawler']['results'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SEO Crawler Admin</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
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
        <input type="url" id="url" name="url" placeholder="Website Url">
        <span id="urlError" class="error-message"></span><br>
        <button type="submit" name="crawl" id="triggerButton" disabled>Trigger Crawl</button>
        <button type="submit" name="showResults" id="results">Show Results</button>
    </form>

    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($results) && !empty($results)) : ?>
        <h2>Crawl Results:</h2>
        <ul>
            <?php foreach ($results as $result) : ?>
                <li><?php echo htmlspecialchars($result); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No existing results</p>
    <?php endif; ?>

    <script type="text/javascript">
        $(document).ready(function() {
            function isValidUrl(url) {
            // URL validation logic, return true if valid, false otherwise
            return /^(ftp|http|https):\/\/[^ "]+$/.test(url);
        }
            // Function to handle input change event
            function handleInputChange() {
            var url = $('#url').val();
            var triggerButton = $('#triggerButton');
            var urlError = $('#urlError');

            if (url.trim() === '') {
                triggerButton.prop('disabled', true);
                urlError.text(''); // Clear the error message if input is empty
            } else {
                if (!isValidUrl(url)) {
                    triggerButton.prop('disabled', true);
                    urlError.text('Invalid URL format. Please enter a valid URL.');
                } else {
                    triggerButton.prop('disabled', false);
                    urlError.text('');
                }
            }
        }

        // Bind input change event to handleInputChange function
        $('#url').on('input', handleInputChange);

        // Initial check to disable the button if input is empty
        handleInputChange();

            // Initial check to disable the button if input is empty or URL is invalid
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
</body>

</html>
