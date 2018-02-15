<?php
namespace Civi\Setup;

class SmartyUtil {

  /**
   * Create a Smarty instance.
   *
   * @return \Smarty
   */
  public static function createSmarty($srcPath) {
    $packagePath = implode(DIRECTORY_SEPARATOR, [$srcPath, 'packages']);
    require_once $packagePath . DIRECTORY_SEPARATOR . 'Smarty'. DIRECTORY_SEPARATOR . 'Smarty.class.php';

    $smarty = new \Smarty();
    $smarty->template_dir = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'templates']);
    $smarty->plugins_dir = [
      implode(DIRECTORY_SEPARATOR, [$packagePath, 'Smarty', 'plugins']),
      implode(DIRECTORY_SEPARATOR, [$srcPath, 'CRM', 'Core', 'Smarty', 'plugins']),
    ];
    $smarty->compile_dir = \Civi\Setup\FileUtil::createTempDir('templates_c');
    $smarty->clear_all_cache();

    // CRM-5308 / CRM-3507 - we need {localize} to work in the templates
    require_once implode(DIRECTORY_SEPARATOR, [$srcPath, 'CRM', 'Core', 'Smarty', 'plugins', 'block.localize.php']);
    $smarty->register_block('localize', 'smarty_block_localize');

    return $smarty;
  }
}
