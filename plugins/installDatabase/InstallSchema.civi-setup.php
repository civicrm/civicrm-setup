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
    $m = $e->getModel();
    $files = array(
      'xmlMissing' => implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'xml']),
      'xmlSchemaMissing' => implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'xml', 'schema', 'Schema.xml']),
      'xmlVersionMissing' => implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'xml', 'version.xml']),
    );

    foreach ($files as $key => $file) {
      if (!file_exists($file)) {
        $e->addError('system', $key, "Schema file is missing: \"$file\"");
      }
    }
  });

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
    $spec = _installschema_getSpec($model->srcPath);

    \Civi\Setup::log()->info(sprintf('[%s] generateCreateSql', basename(__FILE__)));
    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateCreateSql($model->srcPath, $spec->database, $spec->tables));

    \Civi\Setup::log()->info(sprintf('[%s] generateNavigation', basename(__FILE__)));
    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateNavigation($model->srcPath));

    if (!empty($model->loadGenerated)) {
      \Civi\Setup::log()->info(sprintf('[%s] generateSample', basename(__FILE__)));
      \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateSample($model->srcPath));
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

/**
 * @param string $srcPath
 * @return \CRM_Core_CodeGen_Specification
 */
function _installschema_getSpec($srcPath) {
  $schemaFile = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'schema', 'Schema.xml']);
  $versionFile = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'version.xml']);
  $xmlBuilt = \CRM_Core_CodeGen_Util_Xml::parse($versionFile);
  $buildVersion = preg_replace('/^(\d{1,2}\.\d{1,2})\.(\d{1,2}|\w{4,7})$/i', '$1', $xmlBuilt->version_no);
  $specification = new \CRM_Core_CodeGen_Specification();
  $specification->parse($schemaFile, $buildVersion, FALSE);
  return $specification;
}
