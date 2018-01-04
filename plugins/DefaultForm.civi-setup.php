<?php
if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::instance()->getDispatcher()
  ->addListener('civi.setup.createForm', function (\Civi\Setup\Event\CreateFormEvent $e) {

    $e->setForm(new \Civi\Setup\Form());

  }, \Civi\Setup::PRIORITY_PREPARE);
