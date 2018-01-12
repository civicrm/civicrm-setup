<?php
/**
 * @file
 *
 * Generate the site key.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installFiles', function (\Civi\Setup\Event\InstallFilesEvent $e) {

    $e->getModel()->siteKey = \CRM_Utils_String::createRandom(32, \CRM_Utils_String::ALPHANUMERIC);

  }, \Civi\Setup::PRIORITY_PREPARE);
