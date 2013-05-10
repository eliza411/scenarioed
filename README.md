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

5. Install ScenarioEd: 

         php composer.phar install

6. Set up the example project:
          cd web/example
          php ../../composer.phar install

7. Create database

         mysql -u root -p
         create database scenarioed 
         grant all on scenarioed.* to scenarioed@localhost

8. If you wish to use a different database name or user name, edit app/config/parameters.yml and set the values accordingly.

9. Load the database schema

         php app/console doctrine:schema:create

10. Ensure the web server has write privileges where needed. Replace www-data with the user which runs the webserver.

         chown -R www-data app/logs app/cache web/projects

11. Configure Apache to point at the scenarioed/web folder

12. Load yourdomain.com
