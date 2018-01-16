# civicrm-setup

`civicrm-setup` is a library for writing a CiviCRM installer.  It aims to support the CLI command `cv` (`cv core:install`
and `cv core:uninstall`) as well as per-CMS, fine-tuned installers (e.g.  for `civicrm-drupal` or `civicrm-wordpress`).

This library defines:

* A general system facade ([Civi\Setup](src/Setup.php)) which supports all major installation tasks/activities. Each major task corresponds to an [event](https://github.com/civicrm/civicrm-setup/tree/master/src/Setup/Event) (such as `civi.setup.init` or `civi.setup.installDatabase`).
* A *data model* ([Civi\Setup\Model](src/Setup/Model.php)) which lists all the standard configuration parameters (such as the CMS type or the DB credentials).
* A list of *plugins* (`plugins/*/*.civi-setup.php`) which work with the model and the events. For example:
    * The `WordPress.civi-setup.php` plugin runs during initialization (`civi.setup.init`). It reads the WordPress config (e.g. `DB_HOST` and `get_locale()`) then updates the model (`$model->db` and `$model->lang`).
    * The `SetLanguage.civi-setup.php` plugin runs when installing the database (`civi.setup.installDatabase`). It reads the `$model->lang` and updates various Civi settings.

Some key features:

* The library can be used by other, higher-level applications -- such as `cv`, `civicrm-drupal`, `civicrm-wordpress` -- to provide an installation process.
* It is a *leap* -- a separate project/repo which can be deployed optionally (as a a replacement for the default installer).
    * To enable it, add the codebase to your civicrm source tree. (This can be done manually - or as part of a build process.)
* It has minimal external dependencies.

## Developer tips

The library can be used to implement different installers, but consider using `cv` CLI as a reference/aide.
Here are a few useful elements/workflows which are supported by `cv`:

* __Dev loop__ (`cv core:install -f -vv`): When writing a patch to the installer logic, you may want to alternately update the
  code and re-run the installer. You can do this quickly on the CLI with `cv`. Note these two options: `-f`
  will force-reinstall (removing any old settings-files or database-tables), and `-vv` will enable very-verbose output.
  This can be combined with `drush` or `wp-cli`, as in:
    * _WordPress_: `wp plugin deactivate civicrm ; cv core:install -f -vv ; wp plugin activate civicrm`
    * _Drupal 7_: `drush -y dis civicrm ; cv core:install -f -vv --cms-base-url=http://example.com/ ; drush -y en civicrm`

* __Inspection__ (`cv core:install --debug-event`): Most of the installation logic is organized into *plugins* which
  listen to *events*.  To better understand the installation logic, inspect the list of plugins and events.  For
  example:

  ```
  $ cv core:install --debug-event
  ...
  [Event] civi.setup.installFiles
  +-------+--------------------------------------------------------------------------------------------------------+
  | Order | Callable                                                                                               |
  +-------+--------------------------------------------------------------------------------------------------------+
  | #1    | closure(/home/me/src/civicrm-setup/plugins/common.d/LogEvents.civi-setup.php@30)                       |
  | #2    | closure(/home/me/src/civicrm-setup/plugins/installFiles.d/GenerateSiteKey.civi-setup.php@13)           |
  | #3    | closure(/home/me/src/civicrm-setup/plugins/installFiles.d/CreateTemplateCompilePath.civi-setup.php@33) |
  | #4    | closure(/home/me/src/civicrm-setup/plugins/installFiles.d/InstallSettingsFile.civi-setup.php@43)       |
  | #5    | closure(/home/me/src/civicrm-setup/plugins/common.d/LogEvents.civi-setup.php@38)                       |
  +-------+--------------------------------------------------------------------------------------------------------+
  ...
  ```

* __Test coverage__: This library provides little of its own test-coverage. Instead, the main test coverage is provided
  in the `cv` project (`phpunit4 --group installer`).

## Documentation

* [Writing an installer](docs/new-installer.md)
* [Writing a plugin](docs/new-plugin.md)
* [Managing plugins](docs/plugins.md)
