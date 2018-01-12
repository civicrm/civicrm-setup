<?php
/**
 * @file
 *
 * Activate Civi extensions on the newly populated database.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installDatabase', function (\Civi\Setup\Event\InstallDatabaseEvent $e) {
    if (!$e->getModel()->extensions) {
      \Civi\Setup::log()->info('[InstallExtensions] No extensions to activate.');
      return;
    }

    \Civi\Setup::log()->info('[InstallExtensions] Activate extensions: ' . implode(' ', $e->getModel()->extensions));
    \civicrm_api3('Extension', 'enable', array(
      'keys' => $e->getModel()->extensions,
    ));
  }, \Civi\Setup::PRIORITY_LATE + 200);
