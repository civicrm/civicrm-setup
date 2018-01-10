# civicrm-setup

`civicrm-setup` is a library for writing a CiviCRM installer.  It is to support the CLI command `cv` (`cv core:install`
and `cv core:uninstall`) as well as fine-tuned, per-CMS installers (e.g.  for `civicrm-drupal` or `civicrm-wordpress`).

This library defines:

* A general system facade ([Civi\Setup](src/Setup.php)) which supports all major installation tasks/activities.
* A *data model* ([Civi\Setup\Model](src/Setup/Model.php)) which lists all the standard configuration parameters.
* A list of *plugins* (`plugins/*/*.civi-setup.php`) which can (a) autopopulate the model in certain environments and (b) execute installation tasks.

Some key features:

* The library can be used by other, higher-level applications -- such as `cv`, `civicrm-drupal`, `civicrm-wordpress` -- to provide an installation process.
* It is a *leap* -- a separate project/repo which can be deployed optionally (as a a replacement for the default installer).
    * To enable it, add the codebase to your civicrm source tree. (This can be done manually - or as part of a build process.)
* It has minimal external dependencies.

There are a small number of external dependencies.  To allow for the variety
of ways in which different builds manage their dependencies, we leave it up
to the downstream implementer to satisfy them.

* Symfony `EventDispatcher` (`symfony/event-dispatcher` v2.x or v3.x)
* PSR-3 (`psr/log` v1.x)

## Development tips

The library can be used to implement different installers, but consider using `cv` CLI as a reference/aide.
Here are a few useful elements/workflows which are supported by `cv`:

 * __Dev loop__ (`cv core:install -f -vv`): When writing a patch to the installer logic, you may want to alternately update the
   code and re-run the installation. You can do this quickly on the CLI with `cv`. Note the two options: `-f`
   will force-reinstall (removing any old settings-files or database-tables), and `-vv` will enable very-verbose output.

 * __Inspection__ (`cv core:install --debug-event`): Most of the installation logic is organized into *plugins* which
   listen to *events*.  To better understand the installation logic, inspect the list of plugins and events.  For
   example:

   ```
   $ cv core:install --debug-event
   ...
   [Event] civi.setup.checkInstalled
   +-------+-----------------------------------------------------------------------------------------------------+
   | Order | Callable                                                                                            |
   +-------+-----------------------------------------------------------------------------------------------------+
   | #1    | closure(/var/www/sites/all/modules/civicrm/setup/plugins/common/LogEvents.civi-setup.php@30)        |
   | #2    | closure(/var/www/sites/all/modules/civicrm/setup/plugins/db/CheckInstalled.civi-setup.php@13)       |
   | #3    | closure(/var/www/sites/all/modules/civicrm/setup/plugins/settings/CheckInstalled.civi-setup.php@13) |
   | #4    | closure(/var/www/sites/all/modules/civicrm/setup/plugins/common/LogEvents.civi-setup.php@38)        |
   +-------+-----------------------------------------------------------------------------------------------------+
   ...
   ```

## Writing an installer

For a CMS integration (e.g. `civicrm-drupal` or `civicrm-wordpress`) which aims to incorporate an installer, you'll
first need to initialize the setup runtime and get a reference to the `$setup` API:

* Bootstrap the CMS/host environment
* Guard: Check if `civicrm-setup-autoload.php` exists.
    * If it exists, then we'll proceed.
    * If it doesn't exist, then don't try to setup. Fail or fallback gracefully.
* Load `civicrm-setup-autoload.php`.
* If necessary, add any extra autoloaders.
    * Ex: If you use `civicrm` as a distinct sub-project (with its own `vendor` and autoloader), then you may need to load `CRM/Core/ClassLoader.php` and call `register()`.
    * Ex: If you use `composer` to manage the full site-build (with CMS+Civi+dependencies), then no steps are required. Your CMS and/or Civi should provide a copy of `EventDispatcher` and `psr/log`.
* Initialize the `\Civi\Setup` subsystem.
    * Call `\Civi\Setup::init($modelValues = array(), $pluginCallback = NULL)`.
    * The `$modelValues` provides an opportunity to seed the configuration options, such as DSN and file-settings path. See the fields defined for the [Model](src/Setup/Model.php).
    * The `$pluginCallback` (`function(array $files) => array $files`) provides an opportunity to add/remove/override plugin files.
    * __Tip__: During initialization, some values may be autodetected. After initialization, you can inspect or revise these with `Civi\Setup::instance()->getModel()`.
* Get a reference to the `$setup` API
    * Call `$setup = Civi\Setup::instance()`.

When you have a copy of the `$setup` API, there are a few ways to work with it. For example, you might load
the pre-built installation form (`$setup->createForm()->getForm()->run(array $postFields)`).

Alternatively, you might build a custom UI or a headless installer with these functions:

* Guard: Check installation permissions (`$setup->checkAuthorized()`) and inspect the resulting object.
* Guard: Check if Civi was previously installed (`$setup->checkInstalled()`) and inspect the resulting object.
* Guard: Check the system requirements (`$setup->checkRequirements()`) and inspect the resulting object.
* Create the settings file (`$setup->installSettings()`).
* Create the database schema (`$setup->installSchema()`).
    * __Tip__: This will create the database tables, bootstrap Civi, and perform first-run configuration.

For uninstallation, you can use the corresponding functions `$setup->uninstallSchema()` and `$setup->uninstallSettings()`.


## Writing a plugin

A plugin is a PHP file with these characteristics:

* The file's name ends in `*.civi-setup.php`. (Plugins in `civicrm-setup/plugins/*.civi-setup.php` are autodetected.)
* The file has a guard at the top (`defined('CIVI_SETUP')`). If this constant is missing, then bail out. This prevents direct execution.
* The file's logic locates the event-dispatcher and registers listeners, e.g. `\Civi\Setup::instance()->getDisptacher()->addListener($eventName, $callback)`.

Observe that the primary way for a plugin to interact with the system is to register for events (using Symfony's
`EventDispatcher`).  The `$event` names and classes correspond to the methods of `Civi\Setup`, e.g.

* `\Civi\Setup::init()` => `civi.setup.init` => `Civi\Setup\InitEvent`
* `\Civi\Setup::checkAuthorized()` => `civi.setup.checkAuthorized` => `Civi\Setup\CheckAuthorizedEvent`
* `\Civi\Setup::checkInstalled()` => `civi.setup.checkInstalled` => `Civi\Setup\CheckInstalledEvent`
* `\Civi\Setup::checkRequirements()` => `civi.setup.checkRequirements` => `Civi\Setup\CheckRequirementsEvent`
* `\Civi\Setup::installSettings()` => `civi.setup.installSettings` => `Civi\Setup\InstallSettingsEvent`
* `\Civi\Setup::installSchema()` => `civi.setup.installSchema` => `Civi\Setup\InstallSchemaEvent`
* `\Civi\Setup::uninstallSettings()` => `civi.setup.uninstallSettings` => `Civi\Setup\UninstallSettingsEvent`
* `\Civi\Setup::uninstallSchema()` => `civi.setup.uninstallSchema` => `Civi\Setup\UninstallSchemaEvent`
* `\Civi\Setup::createForm()` => `civi.setup.createForm` => `Civi\Setup\CreateFormEvent`

All events provide access to the setup data-model.

> __Ex__: To get the path to the `civicrm.settings.php` file, read `$event->getModel()->settingsPath`.

The `check*` events provide additional methods for relaying information.

> __Ex__: For `checkAuthorized`, use `$event->setAuthorized(bool $authorized)` to indicate whether authorization is permitted,
> and use `$event->isAuthorized()` to see if authorization has been permitted.

## Managing plugins

Plugins in `civicrm-setup/plugins/*/*.civi-setup.php` are automatically
detected and loaded.  The simplest way to manage plugins is adding and
removing files from this folder.

However, you may find it useful to manage plugins programmatically.  For
example, the `civicrm-drupal` integration or the `civicrm-wordpress`
integration might refine the installation process by:

* Adding a new plugin
* Removing a default plugin

To programmatically manage plugins, take note of the
`\Civi\Setup::init(...)` function.  It accepts an argument,
`$pluginCallback`, which can edit the plugin list. For example:

```php
<?php
function myPluginCallback($files) {
  $files['ExtraWordPressInstallPlugin'] = '/path/to/ExtraWordPressInstallPlugin.php';
  ksort($files);
  return $files;
}

\Civi\Setup::init(..., 'myPluginCallback');
```
