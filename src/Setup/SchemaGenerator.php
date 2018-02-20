<?php
namespace Civi\Setup;

use Civi\Setup\Template;

class SchemaGenerator {

  /**
   * Return translated SQL content using tpl, mainly contain SQL codes on table CREATE/DROP
   *
   * @param string $srcPath
   * @param array $database
   * @return array $tables
   *   Ex: ['civicrm_view1', 'civicrm_view2']
   *
   * @return string
   */
  public static function generateCreateSql($srcPath, $database, $tables) {
    \Civi\Setup::log()->info("Generating sql file");
    $template = new Template($srcPath, 'sql');

    $template->assign('database', $database);
    $template->assign('tables', $tables);
    $dropOrder = array_reverse(array_keys($tables));
    $template->assign('dropOrder', $dropOrder);
    $template->assign('mysql', 'modern');

    return $template->getContent('schema.tpl');
  }

  /**
   * Return translated SQL content using tpl, mainly contain SQL codes to populate navigation links
   *
   * @param string $srcPath
   *
   * @return string
   */
  public static function generateNavigation($srcPath) {
    \Civi\Setup::log()->info("Generating navigation SQL content");
    $template = new Template($srcPath, 'sql');
    return $template->getContent('civicrm_navigation.tpl');
  }

  /**
   * Return translated SQL content using tpl, mainly contain SQL codes to populate essential CiviCRM data
   *
   * @param string $srcPath
   *
   * @return string
   */
  public static function generateSample($srcPath) {
    $template = new Template($srcPath, 'sql');
    $sections = ['civicrm_sample.tpl', 'civicrm_acl.tpl', 'case_sample.tpl'];
    return $template->getConcatContent($sections);
  }

}
