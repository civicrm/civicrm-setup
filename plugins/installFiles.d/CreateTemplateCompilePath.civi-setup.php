<?php
/**
 * @file
 *
 * Validate and create the template compile folder.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    $m = $e->getModel();

    if (empty($m->templateCompilePath)) {
      $e->addError('templateCompilePath', sprintf('The templateCompilePath is undefined.'));
    }

    if (!\Civi\Setup\FileUtil::isCreateable($m->templateCompilePath)) {
      $e->addError('templateCompilePathWritable', sprintf('The template compile dir "%s" cannot be created. Ensure the parent folder is writable.', $m->templateCompilePath));
    }
  });

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.installFiles', function (\Civi\Setup\Event\InstallFilesEvent $e) {
    $m = $e->getModel();

    if (!file_exists($m->templateCompilePath)) {
      Civi\Setup::log()->info('[TemplateCompilePath] mkdir "{path}"', [
        'path' => $m->templateCompilePath,
      ]);
      mkdir($m->templateCompilePath, 0777, TRUE);
      \Civi\Setup\FileUtil::makeWebWriteable($m->templateCompilePath);
    }
  });
