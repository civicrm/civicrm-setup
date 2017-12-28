<?php
namespace Civi\Setup;

class DbUtil {

  /**
   * @param string $dsn
   * @return array
   */
  public static function parseDsn($dsn) {
    $parsed = parse_url($dsn);
    return array(
      'server' => $parsed['host'] . ($parsed['port'] ? (':' . $parsed['port']) : ''),
      'username' => $parsed['user'] ?: NULL,
      'password' => $parsed['pass'] ?: NULL,
      'database' => $parsed['path'] ? ltrim($parsed['path'], '/') : NULL,
    );
  }

  /**
   * @param array $db
   * @return \mysqli
   */
  public static function connect($db) {
    $host = $db['server'];
    $hostParts = explode(':', $host);
    if (count($hostParts) > 1 && strrpos($host, ']') !== strlen($host) - 1) {
      $port = array_pop($hostParts);
      $host = implode(':', $hostParts);
    }
    else {
      $port = NULL;
    }
    $conn = @mysqli_connect($host, $db['username'], $db['password'], $db['database'], $port);
    return $conn;
  }

}
