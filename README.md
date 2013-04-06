ScenarioEd
----------

Requirements
============

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
============
#. Verify that you have set the date.timezone in the php.ini

#. Clone the repository
     git clone git://github.com/scenarioed/scenarioed.git

#. Change directory into the scenarioed folder
     cd scenarioed

#. Get composer: 
     curl -sS https://getcomposer.org/installer | php

#. Install: 
     php composer.phar install

#. Create database
     mysql -u root -p
     create database database_name
     grant all on database_name.* to database_user@localhost

#. Edit app/config/parameters.yml to set the database name and database user

#. Load the database schema
     php app/console doctrine:schema:create

#. Ensure the web server has write privileges to app/cache and /app/logs. Replace www-data with the user which runs the webserver.

     chown www-data app/logs app/cache

#. Configure Apache to point at the scenarioed/web folder

#. Load yourdomain.com/project
