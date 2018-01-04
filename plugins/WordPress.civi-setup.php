<?php
if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'WordPress' || !function_exists('current_user_can')) {
      return;
    }

    // Compute settingsPath.
    $uploadDir = wp_upload_dir();
    $preferredSettingsPath = $uploadDir['basedir'] . DIRECTORY_SEPARATOR . 'civicrm' . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $oldSettingsPath = CIVICRM_PLUGIN_DIR . 'civicrm.settings.php';
    if (file_exists($preferredSettingsPath)) {
      $model->settingsPath = $preferredSettingsPath;
    }
    elseif (file_exists($oldSettingsPath)) {
      $model->settingsPath = $oldSettingsPath;
    }
    else {
      $model->settingsPath = $preferredSettingsPath;
    }

    // Compute DSN.
    $model->db = $model->cmsDb = array(
      'server' => DB_HOST,
      'username' => DB_USER,
      'password' => DB_PASSWORD,
      'database' => DB_NAME,
    );

    $model->paths['wp.frontend.base']['url'] = home_url() . '/';
    $model->paths['wp.backend.base']['url'] = admin_url();
    $model->mandatorySettings['userFrameworkResourceURL'] = plugin_dir_url(CIVICRM_PLUGIN_FILE) . 'civicrm';
  });

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.checkAuthorized', function (\Civi\Setup\Event\CheckAuthorizedEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'WordPress') {
      return;
    }

    $e->setAuthorized(current_user_can('activate_plugins'));
  });
