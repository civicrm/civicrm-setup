<?php
/**
 * @file
 *
 * Determine default settings for WordPress.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkAuthorized', function (\Civi\Setup\Event\CheckAuthorizedEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'WordPress') {
      return;
    }

    $e->setAuthorized(current_user_can('activate_plugins'));
  });


\Civi\Setup::dispatcher()
  ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'WordPress' || !function_exists('current_user_can')) {
      return;
    }

    // Note: We know WP is bootstrapped, but we don't know if the `civicrm` plugin is active,
    // so we have to make an educated guess.
    $civicrmPluginFile = implode(DIRECTORY_SEPARATOR, [WP_PLUGIN_DIR, 'civicrm', 'civicrm.php']);

    // Compute settingsPath.
    $uploadDir = wp_upload_dir();
    $preferredSettingsPath = $uploadDir['basedir'] . DIRECTORY_SEPARATOR . 'civicrm' . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $oldSettingsPath = plugin_dir_path($civicrmPluginFile) . 'civicrm.settings.php';
    if (file_exists($preferredSettingsPath)) {
      $model->settingsPath = $preferredSettingsPath;
    }
    elseif (file_exists($oldSettingsPath)) {
      $model->settingsPath = $oldSettingsPath;
    }
    else {
      $model->settingsPath = $preferredSettingsPath;
    }

    $model->templateCompilePath = implode(DIRECTORY_SEPARATOR, [$uploadDir['basedir'], 'civicrm', 'templates_c']);

    // Compute DSN.
    $model->db = $model->cmsDb = array(
      'server' => DB_HOST,
      'username' => DB_USER,
      'password' => DB_PASSWORD,
      'database' => DB_NAME,
    );

    // Compute URLs
    $model->cmsBaseUrl = site_url();
    $model->paths['wp.frontend.base']['url'] = home_url() . '/';
    $model->paths['wp.backend.base']['url'] = admin_url();
    $model->mandatorySettings['userFrameworkResourceURL'] = plugin_dir_url($civicrmPluginFile) . 'civicrm';
  });
