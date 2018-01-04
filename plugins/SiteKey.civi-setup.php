<?php
if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.installSettings', function (\Civi\Setup\Event\InstallSettingsEvent $e) {

    $e->getModel()->siteKey = \CRM_Utils_String::createRandom(32, \CRM_Utils_String::ALPHANUMERIC);

  }, \Civi\Setup::PRIORITY_PREPARE);
