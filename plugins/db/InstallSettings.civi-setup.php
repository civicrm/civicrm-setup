<?php
/**
 * @file
 *
 * Configure settings on the newly populated database.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installSchema', function (\Civi\Setup\Event\InstallSchemaEvent $e) {
    foreach ($e->getModel()->settings as $settingKey => $settingValue) {
      \Civi\Setup::log()->info('[InstallSettings] Set value of ' . $settingKey);
      \Civi::settings()->set($settingKey, $settingValue);
    }
  }, \Civi\Setup::PRIORITY_LATE + 100);
