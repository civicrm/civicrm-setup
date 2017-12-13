<?php
namespace Civi\Setup;

/**
 * Class Model
 * @package Civi\Setup
 *
 * The `Model` defines the main options and inputs that are used to configure
 * the installer.
 *
 * @property string $civicrmRoot
 * @property string $uf
 *   Ex: 'Drupal', 'WordPress', 'Joomla'.
 * @property array $db
 * @property array $cmsDb
 * @property array $components
 *   Ex: array('CiviMail', 'CiviContribute', 'CiviEvent', 'CiviMember', 'CiviReport')
 * @property array $extensions
 *   Ex: array('org.civicrm.flexmailer', 'org.civicrm.shoreditch').
 */
class Model {

  protected $sorted = FALSE;
  protected $fields = array();
  protected $values = array();

  public function __construct() {
    $this->addField(array(
      'name' => 'civicrmRoot',
      'type' => 'string',
    ));
    $this->addField(array(
      'name' => 'uf',
      'type' => 'string',
    ));
    $this->addField(array(
      'name' => 'locale',
      'type' => 'string',
    ));
    $this->addField(array(
      'name' => 'db',
      'type' => 'dsn',
      'value' => array('host' => '', 'user' => '', 'pass' => '', 'name' => ''),
    ));
    $this->addField(array(
      'name' => 'cmsDb',
      'type' => 'dsn',
      'value' => array('host' => '', 'user' => '', 'pass' => '', 'name' => ''),
    ));
    $this->addField(array(
      'name' => 'components',
      'type' => 'array',
      'value' => array(),
    ));
    $this->addField(array(
      'name' => 'extensions',
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

  /**
   * Set the values of multiple fields.
   *
   * @param array $values
   *   Ex: array('civicrmRoot' => '/var/www/sites/default/files/civicrm')
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
