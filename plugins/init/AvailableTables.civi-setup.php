<?php
/**
 * @file
 *
 * Build a list of available CiviCRM tables.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'init'));

    $m = $e->getModel();

    $tables = NULL;
    $schemaFile = implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'xml', 'schema', 'Schema.xml']);
    if (file_exists($schemaFile)) {
      $versionFile = implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'xml', 'version.xml']);
      $xmlBuilt = \CRM_Core_CodeGen_Util_Xml::parse($versionFile);
      $buildVersion = preg_replace('/^(\d{1,2}\.\d{1,2})\.(\d{1,2}|\w{4,7})$/i', '$1', $xmlBuilt->version_no);
      $specification = new \CRM_Core_CodeGen_Specification();
      $specification->parse($schemaFile, $buildVersion);
      $tables = $specification->tables;
    }
    else {
      $e->addError('system', 'schemaMissing', "Schema file is missing: \"$schemaFile\"");
      return;
    }

    $m->setField('tables', 'options', $tables);

  }, \Civi\Setup::PRIORITY_PREPARE);
