<?php
namespace Civi\Setup\UI\Event;

use Civi\Setup\Event\BaseSetupEvent;
use Civi\Setup\UI\SetupController;
use Civi\Setup\UI\SetupControllerInterface;

/**
 * Run the stock web-based UI.
 *
 * Event Name: 'civi.setupui.boot'
 */
class UIBootEvent extends BaseSetupEvent {

  /**
   * @var \Civi\Setup\UI\SetupControllerInterface
   */
  protected $ctrl;

  /**
   * @var string
   *   Ex: 'POST', 'GET'.
   */
  protected $method;

  /**
   * @var array
   */
  protected $fields;

  /**
   * RunControllerEvent constructor.
   *
   * @param SetupControllerInterface $ctrl
   * @param $method
   * @param $fields
   */
  public function __construct($ctrl, $method, $fields) {
    $this->ctrl = $ctrl;
    $this->method = $method;
    $this->fields = $fields;
  }

  /**
   * @return SetupControllerInterface
   */
  public function getCtrl() {
    return $this->ctrl;
  }

  /**
   * @return mixed
   */
  public function getMethod() {
    return $this->method;
  }

  /**
   * @return mixed
   */
  public function getFields() {
    return $this->fields;
  }

}
