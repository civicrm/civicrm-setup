<?php
namespace Civi\Setup;

class DrupalUtil {

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
