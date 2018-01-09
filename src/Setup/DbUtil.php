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

  /**
   * @param array $db
   * @param string $fileName
   * @param bool $lineMode
   *   What does this mean? Seems weird.
   */
  public static function sourceSQL($db, $fileName, $lineMode = FALSE) {
    $conn = self::connect($db);
    if (mysqli_connect_errno()) {
      throw new \Exception(sprintf("Connection failed: %s\n", mysqli_connect_error()));
    }

    mysqli_free_result($conn->query('SET NAMES utf8'));

    if (!$lineMode) {
      $string = file_get_contents($fileName);

      // change \r\n to fix windows issues
      $string = str_replace("\r\n", "\n", $string);

      //get rid of comments starting with # and --

      $string = preg_replace("/^#[^\n]*$/m", "\n", $string);
      $string = preg_replace("/^(--[^-]).*/m", "\n", $string);

      $queries = preg_split('/;\s*$/m', $string);
      foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
          if ($result = $conn->query($query)) {
            mysqli_free_result($result);
          }
          else {
            throw new \Exception("Cannot execute $query: " . mysqli_error($conn));
          }
        }
      }
    }
    else {
      $fd = fopen($fileName, "r");
      while ($string = fgets($fd)) {
        $string = preg_replace("/^#[^\n]*$/m", "\n", $string);
        $string = preg_replace("/^(--[^-]).*/m", "\n", $string);

        $string = trim($string);
        if (!empty($string)) {
          if ($result = $conn->query($string)) {
            mysqli_free_result($result);
          }
          else {
            throw new \Exception("Cannot execute $string: " . mysqli_error($conn));
          }
        }
      }
    }
  }

}
