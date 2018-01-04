# civicrm-setup

`civicrm-setup` is a library for writing a CiviCRM installer.  This library defines:

* A general system facade ([Civi\Setup](src/Setup.php)) which supports all major installation tasks/activities.
* A *data model* ([Civi\Setup\Model](src/Setup/Model.php)) which lists all the standard configuration parameters.
* A list of *plugins* (`plugins/*.civi-setup.php`) which can (a) autopopulate the model in certain environments and (b) execute installation tasks.

Some key features:

* The library can be used by other, higher-level applications -- such as `cv`, `civicrm-drupal`, `civicrm-wordpress` -- to provide an installation process.
* It is a *leap* -- a separate project/repo which can be deployed optionally (as a a replacement for the default installer).
    * To enable it, add the codebase to your civicrm source tree. (This can be done manually - or as part of a build process.)
* It has minimal external dependencies. (The most basic parts of Symfony `EventDispatcher`.)

## Writing an installer

For a CMS integration (e.g. `civicrm-drupal` or `civicrm-wordpress`) which aims to incorporate an installer, you'll
first need to need to initialize the setup runtime and get a reference to the `$setup` API:

* Bootstrap the CMS/host environment
* Guard: Check if `setup/civicrm-setup-autoload.php` exists.
    * If it exists, then we'll proceed.
    * If it doesn't exist, then don't try to setup. Fail or fallback to a different installer.
* Load `setup/civicrm-setup-autoload.php`.
* If necessary, add any extra autoloaders.
    * Ex: If you use `composer` to manage the full site-build (with CMS+Civi+dependencies), then no steps are required. Your CMS and/or Civi should provide a copy of `EventDispatcher`.
    * Ex: If you use `civicrm` as a distinct sub-project (with its own `vendor` and autoloader), then you may need to load `CRM/Core/ClassLoader.php` and call `register()`.
* Initialize the `\Civi\Setup` subystem.
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

For uninstallation, you can use the corresponding functions `$setup->removeSchema()` and `$setup->removeSettings()`.

## Writing a plugin

A plugin is a PHP file with these characteristics:

* The file's name ends in `*.civi-setup.php`. (Plugins in `civicrm-setup/plugins/*.civi-setup.php` are autodetected.)
* The file has a guard on (`defined('CIVI_SETUP')`). If this constant is missing, then bail out. This prevents direct execution.
* The file's logic locates the event-dispatcher and registers listeners, e.g. `\Civi\Setup::instance()->getDisptacher()->addListener($eventName, $callback)`.

Observe that the primary way for a plugin to interact with the system is to register for events (using Symfony's
`EventDispatcher`).  The `$event` names and classes correspond to the methods of `Civi\Setup`, e.g.

* `\Civi\Setup::init()` => `civi.setup.init` => `Civi\Setup\InitEvent`
* `\Civi\Setup::checkAuthorized()` => `civi.setup.checkAuthorized` => `Civi\Setup\CheckAuthorizedEvent`
* `\Civi\Setup::checkInstalled()` => `civi.setup.checkInstalled` => `Civi\Setup\CheckInstalledEvent`
* `\Civi\Setup::checkRequirements()` => `civi.setup.checkRequirements` => `Civi\Setup\CheckRequirementsEvent`
* `\Civi\Setup::installSettings()` => `civi.setup.installSettings` => `Civi\Setup\InstallSettingsEvent`
* `\Civi\Setup::installSchema()` => `civi.setup.installSchema` => `Civi\Setup\InstallSchemaEvent`
* `\Civi\Setup::createForm()` => `civi.setup.createForm` => `Civi\Setup\CreateFormEvent`

All events provide access to the setup data-model.

> __Ex__: To get the path to the `civicrm.settings.php` file, read `$event->getModel()->settingsPath`.

The `check*` events provide additional methods for relaying information.

> __Ex__: For `checkAuthorized`, use `$event->setAuthorized(bool $authorized)` to indicate whether authorization is permitted.
