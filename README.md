# civicrm-setup

`civicrm-setup` is a library for writing a CiviCRM installer.  It aims to support both the CLI command `cv` (`cv
core:install` and `cv core:uninstall`) as well as fine-tuned, per-CMS installers (e.g.  for `civicrm-drupal` or
`civicrm-wordpress`).

General design:

* Installers call a high-level API ([Civi\Setup](src/Setup.php)) which supports all major installation tasks/activities -- such as:
    * Check system requirements (`$setup->checkRequirements()`)
    * Check installation status (`$setup->checkInstalled()`)
    * Install data files (`$setup->installFiles()`)
    * Install database (`$setup->installDatabase()`)
* Each major task corresponds to an [*event*](https://github.com/civicrm/civicrm-setup/tree/master/src/Setup/Event) -- such as:
    * `civi.setup.checkRequirements`
    * `civi.setup.checkInstalled`
    * `civi.setup.installFiles`
    * `civi.setup.installDatabase`
* A *data model* ([Civi\Setup\Model](src/Setup/Model.php)) lists all the standard configuration parameters -- such as:
    * The path to CiviCRM's code (`$model->srcPath`)
    * The system language (`$model->lang`)
    * The DB credentials (`$model->db`)
* *Plugins* (`plugins/*/*.civi-setup.php`) work with the model and the events. For example:
    * The `WordPress.civi-setup.php` plugin runs during initialization (`civi.setup.init`). It reads the WordPress config (e.g.`get_locale()` and `DB_HOST`) then updates the model (`$model->lang` and `$model->db`).
    * The `SetLanguage.civi-setup.php` plugin runs when installing the database (`civi.setup.installDatabase`). It reads the `$model->lang` and updates various Civi settings.

Some key features:

* The library can be used by other projects -- such as `cv`, `civicrm-drupal`, `civicrm-wordpress` -- to provide an installation process.
* It is a *leap*. It can coexist with the old installer, and it lives in a separate project/repo which can be deployed optionally.
    * To enable it, add the codebase to your civicrm source tree. (This can be done manually - or as part of a build process.)
* It has minimal external dependencies. (The codebase for CiviCRM and its dependencies must be available -- but nothing else is needed.)

## Documentation

* [Getting started](docs/getting-started.md)
* [Writing an installer](docs/new-installer.md)
* [Writing a plugin](docs/new-plugin.md)
* [Managing plugins](docs/plugins.md)
