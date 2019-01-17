# Tournament Score Center
Tournament scoring web application containing scoring, self scheduling, result exporting
features and more! This open source application is built on PHP, MySQL, HTML, CSS, and JavaScript.

### System Requirements ###
* Apache or NGINX Web Server
* PHP 5.6+ (with required modules)
   * bz2
   * gd
   * mysqli
   * xml
   * zip
* MySQL 5.6+ (not tested on mysql 8.0+)

### Application Installation ###
**1** Download or Clone the Git repository to your web server/local machine.
Use the most recent code or most recent tagged commit. Unzip the downloaded file if needed.

**2** Create a new schema in your MySQL server called score_center_db. 
The database must be accessible by your web server.

**3** Import the database/score_center_db.sql file into the score_center_db schema. 

**4** Create a new database user with the minimum permissions of
SELECT, INSERT, UPDATE, DELETE for your score_center_db schema.
Remember these credentials to include in the scorecenter code later.

**5** On your web server where you downloaded the repository, move the scorecenter
directory to your web hosting directory. ex) /var/www/html/

**6** Modify the following file and add your database configuration. 

(web hosting directory)/scorecenter/login.php
```
	$db_hostname = '127.0.0.1'; // Hostname of your database server
	$db_username = 'scorecenter_user'; // Username you created
	$db_password = '12345'; // Passoword you created
	$db_database = 'score_center_db'; // Name of your db schema
```

**7** The Tournament Score Center Application should now be installed
and configured. See the next section for testing and next steps.

**|** If you already have an existing database of scorecenter data, a database
update script will be provided from version to version. In this case, the new
application code should be updated, then only run the database update script(s)
since your last version.


### Application Usage & Testing: ###
**|** The application should be available at (YOUR DOMAIN)/scorecenter/logon.php.
If the login screen loads, enter the default credentials below and click login. 
If the application navigates to index.php (home) page, the database and application
have been installed successfully. For security purposes, change the admin password
and username once logged in!
```
username: admin | password: admin
```

**|** For questions regarding application usage and functionlity, refer to the 
*Tournament Score Center Documentation.pdf* file in the GitHub repository.


### Troubleshooting / Tips ###

**|** If you come across an issue with the application, please create an issue on the GitHub repository 
including server/environment configuration and a detailed description of the problem.
  

**|** If you are getting a 502 Bad Gateway error and using NGINX web server, you may need to increase
the fastcgi buffers in the web server configuration file.

/etc/nginx/nginx.conf
```
fastcgi_buffers 16 16k;
fastcgi_buffer_size 32k;
```  

**|** If a page is not loading, functioning properly, or you are suspecting an error, you can add the
following code to print out any PHP errors on the page. 
```
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
?>
```

You can also turn on PHP error reporting for the entire server by updating the following properties 
in the PHP.ini file and restarting your server.

/etc/php(version)/php.ini
```
error_reporting(E_ALL);
ini_set('display_errors', 'On');
```