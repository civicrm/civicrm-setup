<?php
/**
 * @file
 *
 * Generate the site key.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installSettings', function (\Civi\Setup\Event\InstallSettingsEvent $e) {

    $e->getModel()->siteKey = \CRM_Utils_String::createRandom(32, \CRM_Utils_String::ALPHANUMERIC);

  }, \Civi\Setup::PRIORITY_PREPARE);