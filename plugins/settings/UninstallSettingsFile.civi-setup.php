<?php
/**
 * @file
 *
 * Remove the civicrm.settings.php file.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.uninstallSettings', function (\Civi\Setup\Event\UninstallSettingsEvent $e) {
    Civi\Setup::log()->info('[SettingsFile] Remove civicrm.settings.php');

    $file = $e->getModel()->settingsPath;
    if (file_exists($file)) {
      if (!\Civi\Setup\FileUtil::isDeletable($file)) {
        throw new \Exception("Cannot remove $file");
      }
      unlink($file);
    }
  });
