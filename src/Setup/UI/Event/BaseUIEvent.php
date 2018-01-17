<?php
namespace Civi\Setup\UI\Event;

use Symfony\Component\EventDispatcher\Event;

class BaseUIEvent extends Event {

  /**
   * @var \Civi\Setup\UI\SetupController
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
   * @param \Civi\Setup\UI\SetupController $ctrl
   * @param $method
   * @param $fields
   */
  public function __construct($ctrl, $method, $fields) {
    $this->ctrl = $ctrl;
    $this->method = $method;
    $this->fields = $fields;
  }

  /**
   * @return \Civi\Setup\UI\SetupController
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

  /**
   * @return \Civi\Setup\Model
   */
  public function getModel() {
    return $this->ctrl->getModel();
  }

}
