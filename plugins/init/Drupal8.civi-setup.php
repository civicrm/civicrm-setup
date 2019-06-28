<?php
/**
 * @file
 *
 * Determine default settings for Drupal 8.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkAuthorized', function (\Civi\Setup\Event\CheckAuthorizedEvent $e) {
    $model = $e->getModel();
    if ($model->cms !== 'Drupal8' || !is_callable(['Drupal', 'currentUser'])) {
      return;
    }

    \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'checkAuthorized'));
    $e->setAuthorized(\Drupal::currentUser()->hasPermission('administer modules'));
  });

  \Civi\Setup::dispatcher()
    ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {
      $model = $e->getModel();
      if ($model->cms !== 'Drupal8' || !is_callable(['Drupal', 'currentUser'])) {
        return;
      }
      \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'init'));

      $cmsPath = \Drupal::root();

      // Compute settingsPath.
      $siteDir = \Civi\Setup\DrupalUtil::getDrupalSiteDir($cmsPath);
      $model->settingsPath = implode(DIRECTORY_SEPARATOR, [$cmsPath, 'sites', $siteDir, 'civicrm.settings.php']);

      // Compute DSN.
      $connectionOptions = \Drupal::database()->getConnectionOptions();
      $model->db = $model->cmsDb = array(
        'server' => \Civi\Setup\DbUtil::encodeHostPort($connectionOptions['host'], $connectionOptions['port'] ?: NULL),
        'username' => $connectionOptions['username'],
        'password' => $connectionOptions['password'],
        'database' => $connectionOptions['database'],
      );

      // Compute cmsBaseUrl.
      global $base_url, $base_path;
      $model->cmsBaseUrl = $base_url . $base_path;

      // Compute general paths
      $model->paths['civicrm.files']['url'] = implode(DIRECTORY_SEPARATOR, [$base_url, \Drupal\Core\StreamWrapper\PublicStream::basePath(), 'civicrm']);
      $model->paths['civicrm.files']['path'] = implode(DIRECTORY_SEPARATOR, [_drupal8_civisetup_getPublicFiles(), 'civicrm']);

      // Compute templateCompileDir.
      $model->templateCompilePath = implode(DIRECTORY_SEPARATOR, [_drupal8_civisetup_getPrivateFiles(), 'civicrm', 'templates_c']);

      // Compute default locale.
      global $language;
      $model->lang = \Civi\Setup\LocaleUtil::pickClosest($language->langcode, $model->getField('lang', 'options'));
    });

function _drupal8_civisetup_getPublicFiles() {
  $filePublicPath = realpath(\Drupal\Core\StreamWrapper\PublicStream::basePath());

  if (!CRM_Utils_File::isAbsolute($filePublicPath)) {
    $filePublicPath = \Drupal::root() . DIRECTORY_SEPARATOR . $filePublicPath;
  }

  return $filePublicPath;
}

function _drupal8_civisetup_getPrivateFiles() {
  $filePrivatePath = realpath(\Drupal\Core\StreamWrapper\PrivateStream::basePath());

  if (!$filePrivatePath) {
    $filePrivatePath = _drupal8_civisetup_getPublicFiles();
  }
  elseif ($filePrivatePath && !CRM_Utils_File::isAbsolute($filePrivatePath)) {
    $filePrivatePath = \Drupal::root() . DIRECTORY_SEPARATOR . $filePrivatePath;
  }

  return $filePrivatePath;
}
