<?php
if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.checkInstalled', function (\Civi\Setup\Event\CheckInstalledEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'Drupal') {
      return;
    }

    if ($model->settingsPath && file_exists($model->settingsPath)) {
      $e->setSettingInstalled(TRUE);
    }

    if ($model->db) {
      $conn = \Civi\Setup\DbUtil::connect($model->db);
      $found = FALSE;
      foreach ($conn->query('SHOW TABLES LIKE "civicrm_%"') as $result) {
        $found = TRUE;
      }
      $conn->close();
      $e->setDatabaseInstalled($found);
    }
  });
