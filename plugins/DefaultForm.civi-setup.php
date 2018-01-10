<?php
/**
 * @file
 *
 * Generate the default web form.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.createForm', function (\Civi\Setup\Event\CreateFormEvent $e) {

    $e->setForm(new \Civi\Setup\Form());

  }, \Civi\Setup::PRIORITY_PREPARE);
