ScenarioEd
----------

Running
-------

Dependencies for running the app are handled with Puppet. Production and staging environments should use the Puppet manifests, Vagrant configuration is provided for Development purposes.

    vagrant up

Packages managed by composer.

    composer install --dev

Install schema:

    vagrant ssh
    cd /vagrant
    php app/console doctrine:schema:create

Then browse to http://dev.scenarioed.com/app_dev.php/project/
