<?php
/**
 * Database class file.
 *
 * PHP version >=7.1
 *
 * @category Database
 * @package  WP_Crawler
 * @author   Anass Jaafri <anasjaafri6@gmail.com>
 * @license  MIT License
 * @link     https://github.com/anasjr/wpCrawl.git
 */

namespace Database;

use PDO;

/**
 * Database class handles database operations.
 *
 * @category Database
 * @package  WP_Crawler
 * @author   Anass Jaafri <anasjaafri6@gmail.com>
 * @license  MIT License
 * @link     https://github.com/anasjr/wpCrawl.git
 */
class Database
{
    // Database class code here
    
    // Private property to store the PDO database connection.
    private $pdo;
    
    /**
     * Database constructor.
     *
     * @param string $host     Database host.
     * @param string $dbname   Database name.
     * @param string $username Database username.
     * @param string $password Database password.
     */
    public function __construct($host, $dbname, $username, $password)
    {
        // Create a new PDO instance with the provided database credentials.
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        
        // Set PDO error mode to throw exceptions on errors.
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Get a PDO database connection.
     *
     * @return PDO Database connection instance.
     */
    public function getConnection()
    {
        // Return the established PDO database connection.
        return $this->pdo;
    }
}
