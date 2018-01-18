# Writing an installer

For a CMS integration (e.g. `civicrm-drupal` or `civicrm-wordpress`) which aims to incorporate an installer, you'll
first need to initialize the runtime and get a reference to the `$setup` object:

1. Bootstrap the CMS/host environment.
    a. __Tip__: You may not need to do anything here -- this is often implicitly handled by the host environment.
2. Check if `civicrm-setup-autoload.php` exists.
    a. If it exists, then we'll proceed.
    b. If it doesn't exist, then don't try to setup. Fail or fallback gracefully.
3. Load `civicrm-setup-autoload.php`.
4. Load the CiviCRM class-loader.
    a. __Ex__: If you use `civicrm` as a distinct sub-project (with its own `vendor` and autoloader), then you may need to load `CRM/Core/ClassLoader.php` and call `register()`.
    b. __Ex__: If you use `composer` to manage the full site-build (with CMS+Civi+dependencies), then you may not need to take any steps.
5. Initialize the `\Civi\Setup` subsystem.
    a. Call `\Civi\Setup::init($modelValues = array(), $pluginCallback = NULL)`.
    b. The `$modelValues` provides an opportunity to seed the configuration options, such as DB credentials and file-paths. See the fields defined for the [Model](src/Setup/Model.php).
    c. The `$pluginCallback` (`function(array $files) => array $files`) provides an opportunity to add/remove/override plugin files.
    d. __Tip__: During initialization, some values may be autodetected. After initialization, you can inspect or revise these with `Civi\Setup::instance()->getModel()`.
6. Get a reference to the `$setup` API.
    a. Call `$setup = Civi\Setup::instance()`.

For example, this code will check for `civicrm-setup-autoload.php` in three locations;
if found, it sets up the class-loaders, and it initializes the `\Civi\Setup` subsystem.

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

```php
<?php
// Create and execute the default setup controller.
$ctrl = \Civi\Setup::instance()->createController()->getCtrl();
$ctrl->setUrls(array(
  'ctrl' => 'url://for/the/install/ui',
  'res' => 'url://for/civicrm-setup/res',
  'jquery.js' => 'url://for/jquery.js',
  'font-awesome.css' ='url://for/font-awesome.css',
  'finished' => 'url://to/open/after/finishing',
));
\Civi\Setup\BasicRunner::run($ctrl);
```

The `BasicRunner::run()` function uses PHP's standard, global I/O (e.g.
`$_POST` for input; `echo` for output).  However, some frameworks have their
own variants on this.  You can get more direct control of I/O by calling the
controller directly, e.g.:

```php
<?php
list ($httpHeaders, $htmlBody) = $ctrl->run($_SERVER['REQUEST_METHOD'], $_POST);
```

Alternatively, you might build a custom UI or an automated installer. `$setup` provides a number of functions:

* `$setup->getModel()`: Get a copy of the `Model`. You may want to tweak the model's data before performing installation.
* `$setup->checkAuthorized()`: Determine if the current user is authorized to perform an installation.
* `$setup->checkInstalled()`: Determine if CiviCRM is already installed.
* `$setup->checkRequirements()`: Determine if the local system meets the installation requirements.
* `$setup->installFiles()`: Create data files, such as `civicrm.settings.php` and `templates_c`.
* `$setup->installDatabase()`: Create database schema (tables, views, etc). Perform first bootstrap and configure the system.
* `$setup->uninstallDatabase()`: Purge database schema (tables, views, etc).
* `$setup->uninstallFiles()`: Purge data files, such as `civicrm.settings.php`.
