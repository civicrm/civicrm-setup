<?php
/**
 * @file
 *
 * Populate the database schema.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::instance()->getDispatcher()
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

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.installSchema', function (\Civi\Setup\Event\InstallSchemaEvent $e) {
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

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.removeSchema', function (\Civi\Setup\Event\RemoveSchemaEvent $e) {
    \Civi\Setup::log()->info('[DatabaseSchema] Remove all tables and views (civicrm_* and log_civicrm_*)');
    $model = $e->getModel();

    $conn = \Civi\Setup\DbUtil::connect($model->db);
    \Civi\Setup\DbUtil::execute($conn, 'SET FOREIGN_KEY_CHECKS=0;');

    foreach (\Civi\Setup\DbUtil::findViews($conn, $model->db['database']) as $view) {
      if (preg_match('/^(civicrm_|log_civicrm_)/', $view)) {
        \Civi\Setup\DbUtil::execute($conn, sprintf('DROP VIEW `%s`', $conn->escape_string($view)));
      }
    }

    foreach (\Civi\Setup\DbUtil::findTables($conn, $model->db['database']) as $table) {
      if (preg_match('/^(civicrm_|log_civicrm_)/', $table)) {
        \Civi\Setup\DbUtil::execute($conn, sprintf('DROP TABLE `%s`', $conn->escape_string($table)));
      }
    }

    // TODO Perhaps we should also remove stored-procedures/functions?

    $conn->close();
  });
