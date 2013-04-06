ScenarioEd
==========

Requirements
------------

 * Git
 * cURL (or wget - instructions use curl)
 * Apache
 * MySQL
 * Php 5.3.8 or higher (with pear, mysql, xml and mbstring extensions)
   --Php config note-- 
   You MUST set in the php.ini or the application will not run, e.g.:
     date.timezone = America/Los_Angeles
   You should also disable the short open tags, which can conflict with XML
     short_open_tag = off 



Installation
------------

1. Verify that you have set the date.timezone in the php.ini

2. Clone the repository

         git clone git://github.com/scenarioed/scenarioed.git

3. Change directory into the scenarioed folder

         cd scenarioed

4. Get composer: 

         curl -sS https://getcomposer.org/installer | php

5. Install: 

         php composer.phar install

6. Create database

         mysql -u root -p
         create database database_name
         grant all on database_name.* to database_user@localhost

7. Edit app/config/parameters.yml to set the database name and database user

8. Load the database schema

         php app/console doctrine:schema:create

9. Ensure the web server has write privileges to app/cache and /app/logs. Replace www-data with the user which runs the webserver.

         chown www-data app/logs app/cache

10. Configure Apache to point at the scenarioed/web folder

11. Load yourdomain.com/project
