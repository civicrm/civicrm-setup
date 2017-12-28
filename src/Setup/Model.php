<?php
namespace Civi\Setup;

/**
 * Class Model
 * @package Civi\Setup
 *
 * The `Model` defines the main options and inputs that are used to configure
 * the installer.
 *
 * @property string $srcPath
 *   Path to CiviCRM-core source tree.
 *   Ex: '/var/www/sites/all/modules/civicrm'.
 * @property string $setupPath
 *   Path to CiviCRM-setup source tree.
 *   Ex: '/var/www/sites/all/modules/civicrm/setup'.
 * @property string $cms
 *   Ex: 'Backdrop', 'Drupal', 'Drupal8', 'Joomla', 'WordPress'.
 * @property string $settingsPath
 *   Ex: '/var/www/sites/default/civicrm.settings.php'.'
 * @property array $db
 *   Ex: ['server'=>'localhost:3306', 'username'=>'admin', 'password'=>'s3cr3t', 'database'=>'mydb']
 * @property array $cmsDb
 *   Ex: ['server'=>'localhost:3306', 'username'=>'admin', 'password'=>'s3cr3t', 'database'=>'mydb']
 * @property array $components
 *   Ex: ['CiviMail', 'CiviContribute', 'CiviEvent', 'CiviMember', 'CiviReport']
 * @property array $extensions
 *   Ex: ['org.civicrm.flexmailer', 'org.civicrm.shoreditch']
 * @property array $paths
 *   List of hard-coded path-overrides.
 * @property array $defaultSettings
 *   List of domain settings to apply.
 * @property array $mandatorySettings
 *   List of hard-coded setting-overrides.
 */
class Model {

  protected $sorted = FALSE;
  protected $fields = array();
  protected $values = array();

  public function __construct() {
    $this->addField(array(
      'description' => 'Local path of the CiviCRM-core tree',
      'name' => 'srcPath',
      'type' => 'string',
    ));
    $this->addField(array(
      'description' => 'Local path of the CiviCRM-setup tree',
      'name' => 'setupPath',
      'type' => 'string',
    ));
    $this->addField(array(
      'description' => 'Local path to civicrm.settings.php',
      'name' => 'settingsPath',
      'type' => 'string',
    ));
    $this->addField(array(
      'description' => 'Symbolic name of the CMS/user-framework',
      'name' => 'cms',
      'type' => 'string',
    ));
    $this->addField(array(
      'description' => 'Locale of the default dataset',
      'name' => 'locale',
      'type' => 'string',
    ));
    $this->addField(array(
      'description' => 'Credentials for Civi database',
      'name' => 'db',
      'type' => 'dsn',
    ));
    $this->addField(array(
      'description' => 'Credentials for CMS database',
      'name' => 'cmsDb',
      'type' => 'dsn',
    ));
    $this->addField(array(
      'description' => 'List of CiviCRM components to enable',
      'name' => 'components',
      'type' => 'array',
      'value' => array(),
    ));
    $this->addField(array(
      'description' => 'List of CiviCRM extensions to enable',
      'name' => 'extensions',
      'type' => 'array',
      'value' => array(),
    ));
    $this->addField(array(
      'description' => 'List of mandatory path overrides.',
      'name' => 'paths',
      'type' => 'array',
      'value' => array(),
    ));
    $this->addField(array(
      'description' => 'List of setting overrides.',
      'name' => 'settings',
      'type' => 'array',
      'value' => array(),
    ));
    $this->addField(array(
      'description' => 'List of callbacks to run',
      'name' => 'callbacks',
      'type' => 'array',
      'value' => array(),
    ));
  }

  /**
   * @param array $field
   *   - name: string
   *   - description: string
   *   - type: string. One of "checkbox", "string".
   *   - weight: int. (Default: 0)
   *   - visible: bool. (Default: TRUE)
   *   - value: mixed. (Default: NULL)
   * @return $this
   */
  public function addField($field) {
    $defaults = array(
      'weight' => 0,
      'visible' => TRUE,
    );
    $field = array_merge($defaults, $field);

    $this->values[$field['name']] = isset($field['value']) ? $field['value'] : NULL;
    unset($field['value']);

    $this->fields[$field['name']] = $field;

    $this->sorted = FALSE;
    return $this;
  }

  public function getFields() {
    if (!$this->sorted) {
      uasort($this->fields, function ($a, $b) {
        if ($a['weight'] < $b['weight']) {
          return -1;
        }
        if ($a['weight'] > $b['weight']) {
          return 1;
        }
        return strcmp($a['name'], $b['name']);
      });
    }
    return $this->fields;
  }

  public function addCallback($file, $function, $arguments) {
    $sig = md5(serialize($file, $function, $arguments));
    $this->values['callbacks'][$sig] = array(
      'file' => $file,
      'function' => $function,
      'arguments' => $arguments,
    );
  }

  /**
   * Set the values of multiple fields.
   *
   * @param array $values
   *   Ex: array('root' => '/var/www/sites/default/files/civicrm')
   * @return $this
   */
  public function setValues($values) {
    foreach ($values as $key => $value) {
      $this->values[$key] = $value;
    }
    return $this;
  }

  public function getValues() {
    return $this->values;
  }

  public function &__get($name) {
    return $this->values[$name];
  }

  public function __set($name, $value) {
    $this->values[$name] = $value;
  }

  public function __isset($name) {
    return isset($this->values[$name]);
  }

  public function __unset($name) {
    unset($this->values[$name]);
  }

}
