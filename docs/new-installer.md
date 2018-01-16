## Writing an installer

For a CMS integration (e.g. `civicrm-drupal` or `civicrm-wordpress`) which aims to incorporate an installer, you'll
first need to initialize the setup runtime and get a reference to the `$setup` API:

* Bootstrap the CMS/host environment.
    * __Tip__: You may not need to do anything here -- this is often implicitly handled by the host environment.
* Check if `civicrm-setup-autoload.php` exists.
    * If it exists, then we'll proceed.
    * If it doesn't exist, then don't try to setup. Fail or fallback gracefully.
* Load `civicrm-setup-autoload.php`.
* Load the CiviCRM class-loader.
    * Ex: If you use `civicrm` as a distinct sub-project (with its own `vendor` and autoloader), then you may need to load `CRM/Core/ClassLoader.php` and call `register()`.
    * Ex: If you use `composer` to manage the full site-build (with CMS+Civi+dependencies), then you may not need to take any steps.
* Initialize the `\Civi\Setup` subsystem.
    * Call `\Civi\Setup::init($modelValues = array(), $pluginCallback = NULL)`.
    * The `$modelValues` provides an opportunity to seed the configuration options, such as DSN and file-settings path. See the fields defined for the [Model](src/Setup/Model.php).
    * The `$pluginCallback` (`function(array $files) => array $files`) provides an opportunity to add/remove/override plugin files.
    * __Tip__: During initialization, some values may be autodetected. After initialization, you can inspect or revise these with `Civi\Setup::instance()->getModel()`.
* Get a reference to the `$setup` API.
    * Call `$setup = Civi\Setup::instance()`.

For example, this code will check for `civicrm-setup-autoload.php` in three
locations; if found, it sets up the class-loader and initializes the `\Civi\Setup` subsystem.

```php
<?php
$civicrmCore = '/path/to/civicrm';
$setupPaths = array(
  implode(DIRECTORY_SEPARATOR, ['vendor', 'civicrm', 'civicrm-setup']),
  implode(DIRECTORY_SEPARATOR, ['packages', 'civicrm-setup',]),
  implode(DIRECTORY_SEPARATOR, ['setup']),
);
foreach ($setupPaths as $setupPath) {
  $loader = implode(DIRECTORY_SEPARATOR, [$civicrmCore, $setupPath, 'civicrm-setup-autoload.php']);
  if (file_exists($loader)) {
    require_once $loader;
    require_once implode(DIRECTORY_SEPARATOR, [$civicrmCore, 'CRM', 'Core', 'ClassLoader.php']);
    CRM_Core_ClassLoader::singleton()->register();
    \Civi\Setup::init([
      'cms' => 'WordPress',
      'srcPath' => $civicrmCore,
      'setupPath' => dirname($loader),
    ]);
    $setup = Civi\Setup::instance();
    break;
  }
}
```

Once you have a copy of the `$setup` API, there are a few ways to work with it. For example, you might load
the pre-built installation form:

```
// Create and execute the default setup controller (with standard PHP I/O).
$ctrl = \Civi\Setup::instance()->createController()->getCtrl();
$ctrl->setUrls(['res' => ..., 'ctrl' => ...]);
\Civi\Setup\BasicRunner::run($ctrl);

// As above, but customize the input/output process.
$ctrl = \Civi\Setup::instance()->createController()->getCtrl();
$ctrl->setUrls(['res' => ..., 'ctrl' => ...]);
list ($headers, $htmlBody) = $ctrl->run($_SERVER['REQUEST_METHOD'], $_POST);
```

Alternatively, you might build a custom UI or an automated installer with these functions:

* `$setup->checkAuthorized()`: Determine if the current user is authorized to perform an installation.
* `$setup->checkInstalled()`: Determine if CiviCRM is already installed.
* `$setup->checkRequirements()`: Determine if the local system meets the instalation requirements.
* `$setup->installFiles()`: Create data files, such as `civicrm.settings.php` and `templates_c`.
* `$setup->installDatabase()`: Create database schema (tables, views, etc). Perform first bootstrap and configure the system.
* `$setup->uninstallDatabase()`: Purge database schema (tables, views, etc).
* `$setup->uninstallFiles()`: Purge data files, such as `civicrm.settings.php`.
