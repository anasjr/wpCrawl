<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use Database\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];

$database = new Database($dbHost, $dbName, $dbUsername, $dbPassword);
$pdo = $database->getConnection();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Crawler Admin Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/login-style.css">

    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>

    <section class="container forms">
        <div class="form login">
            <div class="form-content">
                <header>Crawler Admin Login</header>
                <form method="post">
                    <div class="field input-field">
                        <?php if (isset($error)) : ?>
                            <p><?php echo $error; ?></p>
                        <?php endif; ?>
                        <input type="text" id="username" name="username" class="input" placeholder="username" required>
                    </div>
                    <div class="field input-field">
                    <input type="password"
                        id="password"
                        name="password"
                        placeholder="Password"
                        class="password"
                        required>
                        <i class='bx bx-hide eye-icon'></i>
                    </div>


                    <div class="field button-field">
                        <button type="submit">Login</button>
                    </div>
                </form>

            </div>
    </section>

    <!-- JavaScript -->
    <script src="../assets/js/hide-eye.js"></script>
</body>

</html>