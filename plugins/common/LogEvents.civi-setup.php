<?php
/**
 * @file
 *
 * Record a log message at the start and end of each major business operation.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}
use \Civi\Setup;

$setup = Setup::instance();

$eventNames = array(
  'civi.setup.init',
  'civi.setup.checkAuthorized',
  'civi.setup.checkRequirements',
  'civi.setup.checkInstalled',
  'civi.setup.installFiles',
  'civi.setup.installDatabase',
  'civi.setup.uninstallDatabase',
  'civi.setup.uninstallFiles',
  'civi.setup.createForm',
);
foreach ($eventNames as $eventName) {
  $setup->getDispatcher()
    ->addListener(
      $eventName,
      function ($event) use ($eventName, $setup) {
        $setup->getLog()->info("[EventDispatcher] Start $eventName");
      },
      Setup::PRIORITY_START + 1
    );
  $setup->getDispatcher()
    ->addListener(
      $eventName,
      function ($event) use ($eventName, $setup) {
        $setup->getLog()->info("[EventDispatcher] Finish $eventName");
      },
      Setup::PRIORITY_END - 1
    );
}
