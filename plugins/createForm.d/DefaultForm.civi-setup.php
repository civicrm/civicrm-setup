<?php
/**
 * @file
 *
 * Generate the default web form.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.createForm', function (\Civi\Setup\Event\CreateFormEvent $e) {

    $e->setForm(new \Civi\Setup\Form());

  }, \Civi\Setup::PRIORITY_PREPARE);
