# Writing a plugin

A plugin is a PHP file with these characteristics:

* The file's name ends in `*.civi-setup.php`. (Plugins in `civicrm-setup/plugins/*.civi-setup.php` are autodetected.)
* The file has a guard at the top (`defined('CIVI_SETUP')`). If this constant is missing, then bail out. This prevents direct execution.
* The file's logic locates the event-dispatcher and registers listeners, e.g. `\Civi\Setup::disptacher()->addListener($eventName, $callback)`.

For example, here is a basic plugin that logs a message during database installation:

```php
<?php
if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installDatabase', function (\Civi\Setup\Event\InstallDatabaseEvent $event) {
    \Civi\Setup::log()->info("I like to run the plugin during installation.");
  });
```

Observe that the primary way for a plugin to interact with the system is to register for events (using Symfony's
`EventDispatcher`).  The `$event` names and classes correspond to the methods of `Civi\Setup`, e.g.

* `\Civi\Setup::init()` => `civi.setup.init` => `Civi\Setup\Event\InitEvent`
* `\Civi\Setup::checkAuthorized()` => `civi.setup.checkAuthorized` => `Civi\Setup\Event\CheckAuthorizedEvent`
* `\Civi\Setup::checkInstalled()` => `civi.setup.checkInstalled` => `Civi\Setup\Event\CheckInstalledEvent`
* `\Civi\Setup::checkRequirements()` => `civi.setup.checkRequirements` => `Civi\Setup\Event\CheckRequirementsEvent`
* `\Civi\Setup::installFiles()` => `civi.setup.installFiles` => `Civi\Setup\Event\InstallFilesEvent`
* `\Civi\Setup::installDatabase()` => `civi.setup.installDatabase` => `Civi\Setup\Event\InstallDatabaseEvent`
* `\Civi\Setup::uninstallFiles()` => `civi.setup.uninstallFiles` => `Civi\Setup\Event\UninstallFilesEvent`
* `\Civi\Setup::uninstallDatabase()` => `civi.setup.uninstallDatabase` => `Civi\Setup\Event\UninstallDatabaseEvent`
* `\Civi\Setup::createController()` => `civi.setup.createController` => `Civi\Setup\Event\CreateControllerEvent`

All events provide access to the setup data-model.

> __Ex__: To get the path to the `civicrm.settings.php` file, read `$event->getModel()->settingsPath`.

The `check*` events provide additional methods for relaying information.

> __Ex__: For `checkAuthorized`, use `$event->setAuthorized(bool $authorized)` to indicate whether authorization is permitted,
> and use `$event->isAuthorized()` to see if authorization has been permitted.
>
> __Ex__: For `checkRequirements`, use `$event->addError(...)` to record an
> error that prevents installation.  Similarly, use `addWarning(...)` and
> `addInfo(...)` to report less critical issues.
