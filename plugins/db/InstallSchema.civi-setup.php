<?php
/**
 * @file
 *
 * Populate the database schema.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    $seedLanguage = $e->getModel()->lang;
    $sqlPath = $e->getModel()->srcPath . DIRECTORY_SEPARATOR . 'sql';

    if (!$seedLanguage) {
      return;
    }

    if (!preg_match('/^[a-z][a-z]_[A-Z][A-Z]$/', $seedLanguage)) {
      $e->addError('langMalformed', 'Language name is malformed.');
      return;
    }

    $files = array(
      $sqlPath . DIRECTORY_SEPARATOR . "civicrm_data.{$seedLanguage}.mysql",
      $sqlPath . DIRECTORY_SEPARATOR . "civicrm_acl.{$seedLanguage}.mysql",
    );

    foreach ($files as $file) {
      if (!file_exists($file)) {
        $e->addError('langMissing', "Language schema file is missing: \"$file\"");
        return;
      }
    }
  });

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installDatabase', function (\Civi\Setup\Event\InstallDatabaseEvent $e) {
    \Civi\Setup::log()->info('[DatabaseSchema] Install');
    $model = $e->getModel();

    $sqlPath = $model->srcPath . DIRECTORY_SEPARATOR . 'sql';

    \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . 'civicrm.mysql');

    if (!empty($model->loadGenerated)) {
      \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_generated.mysql', TRUE);
    }
    else {
      $seedLanguage = $model->lang;
      if ($seedLanguage) {
        \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . "civicrm_data.{$seedLanguage}.mysql");
        \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . "civicrm_acl.{$seedLanguage}.mysql");
      }
      else {
        \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
        \Civi\Setup\DbUtil::sourceSQL($model->db, $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_acl.mysql');
      }
    }

  });
