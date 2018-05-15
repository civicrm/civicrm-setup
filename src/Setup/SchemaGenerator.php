<?php
namespace Civi\Setup;

use Civi\Setup\Template;

class SchemaGenerator {

  /**
   * Return translated SQL content using tpl, mainly contain SQL codes on table CREATE/DROP
   *
   * @param string $srcPath
   * @param array $database
   * @param array $tables
   * @return string
   */
  public static function generateCreateSql($srcPath, $database, $tables) {
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
    $versionFile = implode(DIRECTORY_SEPARATOR, [$srcPath, 'xml', 'version.xml']);
    $xml = \CRM_Core_CodeGen_Util_Xml::parse($versionFile);

    $template = new Template($srcPath, 'sql');
    $template->assign('db_version', $xml->version_no);

    $sections = [
      'civicrm_country.tpl',
      'civicrm_state_province.tpl',
      'civicrm_currency.tpl',
      'civicrm_data.tpl',
      'civicrm_acl.tpl',
      'civicrm_sample.tpl',
      'case_sample.tpl',
      'civicrm_version_sql.tpl',
    ];
    return $template->getConcatContent($sections);
  }

}
