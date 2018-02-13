<?php
/**
 * @file
 *
 * Generate the schema files.
 */

if (!defined('CIVI_SETUP')) {
  exit("Genrate Schema plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'checkRequirements'));

    if (!is_writable($e->getModel()->srcPath)) {
      $e->addError('system', 'accessDenied', "Do not premission to create and write schema files.");
      return;
    }
  });

  \Civi\Setup::dispatcher()
    ->addListener('civi.setup.generateSchema', function (\Civi\Setup\Event\GenerateSchemaEvent $e) {
      $model = $e->getModel();

      \Civi\Setup::log()->info(sprintf('[%s] Generate database schema', basename(__FILE__)));

      $genCode = new \CRM_Core_CodeGen_Main(
        implode(DIRECTORY_SEPARATOR, array($model->srcPath, 'CRM', 'Core', 'DAO')) . DIRECTORY_SEPARATOR, // $CoreDAOCodePath
        implode(DIRECTORY_SEPARATOR, array($model->srcPath, 'sql')), // $sqlCodePath
        $model->srcPath, // $phpCodePath
        implode(DIRECTORY_SEPARATOR, array($model->srcPath, 'templates')) . DIRECTORY_SEPARATOR, // $tplCodePath
        NULL, // IGNORE
        $model->cms, // cms
        NULL, // db version
        implode(DIRECTORY_SEPARATOR, array($model->srcPath, 'schema', 'Schema.xml')) // schema file
      );
      $genCode->main();

    });
