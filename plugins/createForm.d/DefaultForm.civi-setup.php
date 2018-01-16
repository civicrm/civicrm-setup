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
  ->addListener('civi.setup.createController', function (\Civi\Setup\Event\CreateControllerEvent $e) {
    \Civi\Setup::log()->info(sprintf('[%s] Handle %s', basename(__FILE__), 'createController'));

    $e->setCtrl(new \Civi\Setup\SetupController(\Civi\Setup::instance()));

  }, \Civi\Setup::PRIORITY_PREPARE);
