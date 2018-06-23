Kaliop eZPublish 5 installer
============================

A package which makes deploying your installations a breeze

Installation
------------

* Add this package as requirement to your project via composer: in composer.json

        ...
        "require": {
            ...
            "kaliop/ezpublish5universalinstaller": "*",
            ...
        },
        ...


Execution
---------

Different commands are available, all through a command-line application. To see the list of commands, run:

        php bin/ezp5installer.php

(assuming that the top-level bin folder is set to 'bin' in composer.json, as it is by default in eZPublish)

Most commands need to have an environment defined to successfully execute. This can be set either using the '--env'
option, or the SYMFONY_ENV environment variable


Feature: managing legacy settings
---------------------------------

* This allows to keep the settings for eZP Legacy somewhere in your eZ5 repository, outside of a Legacy Extension

* You can store them in any dir you like, default is 'ezpublish/legacy_settings'

* Both 'common' settings and 'per environment' settings files are allowed, with the latter overwriting the former if a
    file is present in both locations

* Settings files are symlinked by default. On windows, when run as not-admin, they get copied over

* The command to deploy the settings is:

        php bin/ezp5installer.php legacy-settings:install --env <env>

* The expected structure of settings folders is:

         Structure: <baseDir>
                    |- common
                    | |- override
                    |   |- *.ini
                    | |- siteaccess
                    |   |- <name>
                    |     |- *.ini
                    |- <env>
                      |- override
                      | |- *.ini
                      |- siteaccess
                        |- <name>
                          |- *.ini


Feature: managing per-environment configuration (apache, solr, etc)
-------------------------------------------------------------------

* This makes it possible to keep files like .htaccess in the project, with one version per env or one global version,
    and deploy them to a target dir (inside the eZP root dir or even outside of it)

* Using a similar subdirectory structure as legacy settings; the default dir for storing the files is 'ezpublish/misc_files'

* The command to deploy the files is:

        php bin/ezp5installer.php misc:install --env <env>

    If one of the target files exists and is not a symlink, deployment will fail. Use the '-o' option to allow overwrites


Feature: cleaning the database from temporary data
--------------------------------------------------

While developing the site, the eZ db will accumulate 'temporary cruft' in some tables, like the most searched-for contents,
pending notifications and last visited pages for logged-in users.

While it is not mandatory to do so, it is a good idea to remove this data before shipping the db to UAT/PROD.
One of the available commands: `database:cleanup` is designed to do so.

It is also a useful tool if you use full database dumps at any point in time, and want to compare then by using 'diff' or
similar tools.


Feature: purging the Memcache cache
-----------------------------------

See the memcache:purge command, eg:

    php bin/ezp5installer.php memcache:purge

NB: only works when eZPublish is set up using yml config files


Feature: purging the Varnish server
-----------------------------------

See the varnish:purge command, eg:

    php $DIR/ezp5installer.php varnish:purge --key=ezpublish.system.my_siteaccess_group.http_cache.purge_servers

NB: only works when eZPublish is set up using yml config files


Feature: purging the OPCache cache
-----------------------------------

In order to achieve that, we have to make an http request to the web server. One way is to:

1. set up a script to purge opcache in the web root

        <?php

        if (function_exists('opcache_reset')) {
            opcache_reset();
            echo 'OK';
        } else {
            echo 'KO';
        }

2. protect access to it, so that it can only requested by the deploy scripts. Ex. Apache htaccess config

        # Allow ONLY TRUSTED IPS to send calls to clean the OPCache
        RewriteCond %{REMOTE_ADDR} ^127\.0\.0\.1
        RewriteRule ^clearopcache.php - [L]

3. add the url to be used to connect to the web server in some per-env. config, eg: parameters.yml

        parameters:
            opcache_purge_url: http://localhost/clearopcache.php

4. use the http:request command, eg:

        php bin/ezp5installer.php http:request --key=parameters.opcache_purge_url

NB: only works when eZPublish is set up using yml config files


Feature: getting a config value
-------------------------------

In order to allow you to build complex shell scripts for deployment, there is a small helper which gives you config values.
Eg:

    DBUSER=`php ./bin/ezp5installer.php config:get --key=doctrine.dbal.connections.my_project_repository_connection.user --env=uat`


Feature: etc...
---------------

No more features available at this point in time!

For an inspiration, take a look at the doc/reference folder, esp. at the phing file in there...


Developers notes
----------------

* the ezp5installer.php app HAS to run even when eZP is misconfigured or not configured at all. So it can not just hook
    into the kernel.
    Ideally, it should work even when it is installed *outside* eZP, or *without* it (eg on a server with only solr)

* the core logic for commands has to be in classes in the Common namespace, and not tied to the SF Commands

* in the future we might also expose each Sf commands as Composer commands (via php classes with the good API).
    Advantage: they are easier to make independent of the base OS.

* problem: how to make the ezp5installer commands use configuration options which depend on the environment?
    Discussion:
    - at the moment the environment which is being deployed is defined by using SYMFONY_ENV env var: easy peasy
    - some commands will probably need more configuration than a couple of cli switches/env vars. Where to store it?
        We can not hardcode the current environment in composer.json!
    - a1: in composer.json, "extra" section, have a top-level key, say 'ez5ui', with sub-value per environment: ez5ui.<env>.this.that
        This is easy to do but a bit messy. Also it does not work well if we keep the dedicated-command-line-tool approach
    - a2: we keep the settings for the installer in a dedicated yaml file, with a known location, which depends on the
        environment. We could even use keys in the 'extra' section to define the location of the file (with a default
        value of course) to make it super flexible... (problem: the dedicated-cli-tool would not know about the extras
        values, but we can add cli switches for that)


To Do
-----

* make autoload work when this project is used standalone

* make our commands able to echo something: inject a simple logger by default (eg https://bitbucket.org/fool/echolog),
    and an appropriate one when working in composer-command mode

* add a command which does a cleanup of images in var directory which do not exist any more in ezimage table
  (same with binary files)

* instead of relying on composer autoload for the yml parser, copy it inside this project

[![License](https://poser.pugx.org/kaliop/ezpublish5universalinstaller/license)](https://packagist.org/packages/kaliop/ezpublish5universalinstaller)
[![Latest Stable Version](https://poser.pugx.org/kaliop/ezpublish5universalinstaller/v/stable)](https://packagist.org/packages/kaliop/ezpublish5universalinstaller)
[![Total Downloads](https://poser.pugx.org/kaliop/ezpublish5universalinstaller/downloads)](https://packagist.org/packages/kaliop/ezpublish5universalinstaller)
