<?php
/**
 * @file
 *
 * Populate the database schema.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'checkRequirements'));
    $seedLanguage = $e->getModel()->lang;
    $sqlPath = $e->getModel()->srcPath . DIRECTORY_SEPARATOR . 'sql';

    if (!$seedLanguage || $seedLanguage === 'en_US') {
      $e->addInfo('system', 'lang', "Default language is allowed");
      return;
    }

    if (!preg_match('/^[a-z][a-z]_[A-Z][A-Z]$/', $seedLanguage)) {
      $e->addError('system', 'langMalformed', 'Language name is malformed.');
      return;
    }

    $files = array(
      $sqlPath . DIRECTORY_SEPARATOR . "civicrm_data.{$seedLanguage}.mysql",
      $sqlPath . DIRECTORY_SEPARATOR . "civicrm_acl.{$seedLanguage}.mysql",
    );

    foreach ($files as $file) {
      if (!file_exists($file)) {
        $e->addError('system', 'langMissing', "Language schema file is missing: \"$file\"");
        return;
      }
    }

    $e->addInfo('system', 'lang', "Language $seedLanguage is allowed.");
  });

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installDatabase', function (\Civi\Setup\Event\InstallDatabaseEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Install database schema', basename(__FILE__)));

    $model = $e->getModel();

    $sqlPath = $model->srcPath . DIRECTORY_SEPARATOR . 'sql';

    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateCreateSql($srcPath, $model->db['database'], $model->tables));

    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateNavigation($sqlPath));

    if (!empty($model->loadGenerated)) {
      \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateSample($sqlPath));
    }
    else {
      $seedLanguage = $model->lang;
      // @TODO need to generate and fetch seedLanguage mysql data
      if ($seedLanguage && $seedLanguage !== 'en_US') {
        \Civi\Setup\DbUtil::sourceSQL($model->db, file_get_contents($sqlPath . DIRECTORY_SEPARATOR . "civicrm_data.{$seedLanguage}.mysql"));
        \Civi\Setup\DbUtil::sourceSQL($model->db, file_get_contents($sqlPath . DIRECTORY_SEPARATOR . "civicrm_acl.{$seedLanguage}.mysql"));
      }
      else {
        \Civi\Setup\DbUtil::sourceSQL($model->db, file_get_contents($sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql'));
        \Civi\Setup\DbUtil::sourceSQL($model->db, file_get_contents($sqlPath . DIRECTORY_SEPARATOR . 'civicrm_acl.mysql'));
      }
    }

  });
