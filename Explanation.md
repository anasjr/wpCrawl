# Problem to be solved:
The problem at hand is to create a WordPress plugin or a PHP app that help a website admin in improving their website's SEO rankings.
The admin desires a tool that allow them to understand the linkage between various webpages and the home page.
As a suggested solution, a crawler needs to be developed that can extract internal hyperlinks from the website's homepage and store these links for analysis. 
The administrator also needs to have the ability to manually trigger this crawl and view the results.
# Technical Specification :
The solution involves building a PHP-based web crawler that interacts with the website, extracts internal links, and stores them in a database. 
Here’s how the technical components come together:
## Crawler Class ('Crawler.php'):
1.  Utilizes Symfony components for making HTTP requests and parsing HTML.
1.  Contains methods to crawl a website, store URLs in a database, clear previous results, and save the homepage as an HTML file.
1.  Ensures adherence to modern OOP principles and PSR standards.
   
  Methods in Crawler Class (Crawler.php):
  
1.  crawlWebsite()
Purpose: Initiates the crawl operation by making HTTP requests to the specified website, extracting internal links from the homepage, and storing them in the database.
Functionality: Uses Symfony components for HTTP requests and HTML parsing. Clears previous crawl results, extracts internal links, and saves them to the database.

1.  clearPreviousResults()
Purpose: Clears the previous crawl results from the database to prepare for a new crawl operation.
Functionality: Deletes all rows from the crawl_results table and resets the AUTO_INCREMENT value.

1.  storeResult($url)
Purpose: Stores a crawled URL in the database along with a timestamp.
Functionality: Prepares and executes an SQL query to insert the URL and timestamp into the crawl_results table.

1.  saveHomepageAsHTML($url, $filePath)
Purpose: Saves the homepage content as an HTML file on the server.
Functionality: Makes an HTTP request to the homepage, retrieves its HTML content, and saves it to the specified file path.

1.  getResults()
Purpose: Retrieves crawled results from the database.
Functionality: Prepares and executes an SQL query to select all URLs from crawl_results and returns them as an array.

## Automatic Crawl ('automaticCrawl.php')
1.  Manages the initiation of the crawl operation. It uses AJAX for the automatic triggering of the crawl.
1.  Utilizes sessions to store crawl results temporarily.
1.  Implements a periodic automatic crawl using a JavaScript interval, ensuring the crawl happens every hour.

## User Interface ('index.php' and 'login.php') : 
1.  Provides a user-friendly interface for the admin to input the website URL and trigger the crawl operation.
1.  Implements basic user authentication through a login form.
1.  Displays the crawl results to the admin upon request.

# Technical Decisions :
## Choosing Symfony\Component\DomCrawler:
Integrates CSS and XPath selectors seamlessly. CSS selectors, especially, are widely known and used, 
making it easier for developers to target specific elements in HTML.
Its syntax is clean, making it easier to read and understand. Symfony's DomCrawler is optimized for performance, allowing for efficient parsing of HTML documents
## Symfony Components for HTTP Handling : 
Using components that are known for their reliability and efficiency will guarantee a stable and high-performing crawler,
also ensures that the application can efficiently fetch web pages and can handle diverse website structures and sizes.
## Session Management for Temporary Storage:
PHP sessions offer a convenient and secure method for storing temporary data between requests. 
Since the crawl results need to persist across different interactions, sessions provide an ideal solution.
This persistence enables the admin to review and analyze the data multiple times without the need for repeated crawls, 
enhancing the usability and efficiency of the tool.
## AJAX for Asynchronous Crawling:
AJAX enables asynchronous communication with the server, allowing the crawl operation to run in the background without disrupting the user interface.
Admins can trigger crawls and continue using the application, receiving crawl results without page reloads. This real-time feedback enhances user satisfaction, 
making the tool user-friendly and efficient.
# Achieving Admin’s Desired Outcome:
The selected technical decisions collectively ensure the administrator’s desired outcome is met comprehensively:
1.  Admins can manually trigger crawls, giving them control over when to analyze SEO data. This on-demand feature caters to immediate needs, allowing admins to initiate analyses when they think it's necessary.
1.  AJAX-driven asynchronous crawling enhances user interaction. Admins can continue working within the application while receiving real-time crawl updates. This efficiency maximizes the tool’s usability, minimizing waiting time and enhancing productivity.
1.  Automatic periodic crawling facilitates continuous SEO monitoring. By ensuring regular updates, the tool becomes a reliable source of information, allowing admins to track changes in the website’s internal links effortlessly
