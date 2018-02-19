<?php
/**
 * @file
 *
 * Populate the database schema.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

class InstallSchemaPlugin implements \Symfony\Component\EventDispatcher\EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      'civi.setup.checkRequirements' => [
        ['checkXmlFiles', 0],
        ['checkSqlFiles', 0],
      ],
      'civi.setup.installDatabase' => [
        ['installDatabase', 0]
      ],
    ];
  }

  public function checkXmlFiles(\Civi\Setup\Event\CheckRequirementsEvent $e) {
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
  }

  public function checkSqlFiles(\Civi\Setup\Event\CheckRequirementsEvent $e) {
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

    if (!file_exists($e->getModel()->settingsPath)) {
      $e->addError('system', 'settingsPath', sprintf('The CiviCRM setting file is missing.'));
    }

    $e->addInfo('system', 'lang', "Language $seedLanguage is allowed.");
  }

  public function installDatabase(\Civi\Setup\Event\InstallDatabaseEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Install database schema', basename(__FILE__)));

    $model = $e->getModel();

    $sqlPath = $model->srcPath . DIRECTORY_SEPARATOR . 'sql';
    $spec = $this->loadSpecification($model->srcPath);

    require_once $model->settingsPath;

    \Civi\Setup::log()->info(sprintf('[%s] generateCreateSql', basename(__FILE__)));
    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateCreateSql($model->srcPath, $model->db['database'], $spec->tables));

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

    \Civi\Setup::log()->info(sprintf('[%s] generateNavigation', basename(__FILE__)));
    \Civi\Setup\DbUtil::sourceSQL($model->db, \Civi\Setup\DbUtil::generateNavigation($model->srcPath));
  }

  /**
   * @param string $srcPath
   * @return \CRM_Core_CodeGen_Specification
   */
  protected function loadSpecification($srcPath) {
    $schemaFile = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'schema', 'Schema.xml']);
    $versionFile = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'version.xml']);
    $xmlBuilt = \CRM_Core_CodeGen_Util_Xml::parse($versionFile);
    $buildVersion = preg_replace('/^(\d{1,2}\.\d{1,2})\.(\d{1,2}|\w{4,7})$/i', '$1', $xmlBuilt->version_no);
    $specification = new \CRM_Core_CodeGen_Specification();
    $specification->parse($schemaFile, $buildVersion, FALSE);
    return $specification;
  }

}

\Civi\Setup::dispatcher()->addSubscriber(new InstallSchemaPlugin());
