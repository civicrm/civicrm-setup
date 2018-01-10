<?php
/**
 * @file
 *
 * Specify default components.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.init', function (\Civi\Setup\Event\InitEvent $e) {

    $e->getModel()->components = array('CiviEvent', 'CiviContribute', 'CiviMember', 'CiviMail', 'CiviReport');

  }, \Civi\Setup::PRIORITY_PREPARE);
