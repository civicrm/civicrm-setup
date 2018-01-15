<?php
/**
 * @file
 *
 * Run the PHP+MySQL system requirements checks from Civi\Install\Requirements.
 *
 * Aesthetically, I'd sorta prefer to remove this and (instead) migrate the
 * `Requirements.php` so that each check was its own plugin. But for now this works.
 */

if (!defined('CIVI_SETUP')) {
  exit("Installation plugins must only be loaded by the installer.\n");
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    $model = $e->getModel();

    $levelMap = array(
      \Civi\Install\Requirements::REQUIREMENT_OK => 'info',
      \Civi\Install\Requirements::REQUIREMENT_WARNING => 'warning',
      \Civi\Install\Requirements::REQUIREMENT_ERROR => 'error',
    );

    $r = new \Civi\Install\Requirements();
    list ($host, $port) = \Civi\Setup\DbUtil::decodeHostPort($model->db['server']);
    $msgs = $r->checkAll(array(
      'file_paths' => array(/* we do this elsewhere */),
      'db_config' => array(
        'host' => $host,
        'port' => $port,
        'username' => $model->db['username'],
        'password' => $model->db['password'],
        'database' => $model->db['database'],
      ),
    ));

    foreach ($msgs as $msg) {
      $e->addMessage($levelMap[$msg['severity']], $msg['title'], $msg['details']);
    }
  });
