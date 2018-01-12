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
  ->addListener('civi.setup.installDatabase', function (\Civi\Setup\Event\InstallDatabaseEvent $e) {
    if ($e->getModel()->lang) {
      \Civi\Setup::log()->info('[SetLanguage] Set default language to ' . $e->getModel()->lang);
      \Civi::settings()->set('lcMessages', $e->getModel()->lang);
    }
  }, \Civi\Setup::PRIORITY_LATE + 400);
