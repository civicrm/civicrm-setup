<?php
/**
 * @file
 *
 * Build a list of available translations.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {

    /**
     * @var \Civi\Setup\Model $m
     */
    $m = $e->getModel();

    $langs = NULL;
    require implode(DIRECTORY_SEPARATOR, [$m->srcPath, 'install', 'langs.php']);
    foreach ($langs as $locale => $_) {
      if ($locale == 'en_US') {
        continue;
      }
      if (!file_exists(implode(DIRECTORY_SEPARATOR, array($m->srcPath, 'sql', "civicrm_data.$locale.mysql")))) {
        unset($langs[$locale]);
      }
    }

    $langField = $m->getField('lang');
    $langField['options'] = $langs;
    $m->addField($langField);

  }, \Civi\Setup::PRIORITY_PREPARE);