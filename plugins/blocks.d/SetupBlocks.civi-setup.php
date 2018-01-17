<?php
if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.runController', function (\Civi\Setup\Event\RunControllerEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Register blocks', basename(__FILE__)));

    /**
     * @var \Civi\Setup\SetupController $ctrl
     */
    $ctrl = $e->getCtrl();

    $blocks = array(
      'cvs-header' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'header.tpl.php',
        'class' => '',
        'weight' => 10,
      ),
      'cvs-requirements' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'requirements.tpl.php',
        'class' => 'if-no-problems',
        'weight' => 20,
      ),
      'cvs-l10n' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'l10n.tpl.php',
        'class' => 'if-no-errors',
        'weight' => 30,
      ),
      'cvs-sample-data' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'sample-data.tpl.php',
        'class' => 'if-no-errors',
        'weight' => 40,
      ),
      'cvs-components' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'components.tpl.php',
        'class' => 'if-no-errors',
        'weight' => 50,
      ),
      'cvs-advanced' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'advanced.tpl.php',
        'class' => '',
        'weight' => 60,
      ),
      'cvs-install' => array(
        'is_active' => TRUE,
        'file' => __DIR__ . DIRECTORY_SEPARATOR . 'install.tpl.php',
        'class' => 'if-no-errors',
        'weight' => 70,
      ),
    );
    $ctrl->blocks = array_merge($e->getCtrl()->blocks, $blocks);

  }, \Civi\Setup::PRIORITY_PREPARE);
