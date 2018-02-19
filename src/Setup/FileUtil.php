<?php
namespace Civi\Setup;

class FileUtil {

  public static function isCreateable($file) {
    if (file_exists($file)) {
      return is_writable($file);
    }

    $next = dirname($file);
    do {
      $current = $next;
      if (file_exists($current)) {
        return is_writable($current);
      }
      $next = dirname($current);
    } while ($current && $next && $current != $next);

    return FALSE;
  }

  public static function makeWebWriteable($path) {
    // Blerg: Setting world-writable works as a default, but
    // it 'sprone to break systems that rely on umask's or facl's.
    chmod($path, 0777);
  }

  public static function isDeletable($path) {
    return is_writable(dirname($path));
  }

  /**
   * @param $cmsPath
   *
   * @return string
   */
  public static function getDrupalSiteDir($cmsPath) {
    static $siteDir = '';

    if ($siteDir) {
      return $siteDir;
    }

    $sites = CIVICRM_DIRECTORY_SEPARATOR . 'sites' . CIVICRM_DIRECTORY_SEPARATOR;
    $modules = CIVICRM_DIRECTORY_SEPARATOR . 'modules' . CIVICRM_DIRECTORY_SEPARATOR;
    preg_match("/" . preg_quote($sites, CIVICRM_DIRECTORY_SEPARATOR) .
      "([\-a-zA-Z0-9_.]+)" .
      preg_quote($modules, CIVICRM_DIRECTORY_SEPARATOR) . "/",
      $_SERVER['SCRIPT_FILENAME'], $matches
    );
    $siteDir = isset($matches[1]) ? $matches[1] : 'default';

    if (strtolower($siteDir) == 'all') {
      // For this case - use drupal's way of finding out multi-site directory
      $uri = explode(CIVICRM_DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']);
      $server = explode('.', implode('.', array_reverse(explode(':', rtrim($_SERVER['HTTP_HOST'], '.')))));
      for ($i = count($uri) - 1; $i > 0; $i--) {
        for ($j = count($server); $j > 0; $j--) {
          $dir = implode('.', array_slice($server, -$j)) . implode('.', array_slice($uri, 0, $i));
          if (file_exists($cmsPath . CIVICRM_DIRECTORY_SEPARATOR .
            'sites' . CIVICRM_DIRECTORY_SEPARATOR . $dir
          )) {
            $siteDir = $dir;
            return $siteDir;
          }
        }
      }
      $siteDir = 'default';
    }

    return $siteDir;
  }

}
