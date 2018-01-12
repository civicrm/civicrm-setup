<?php
namespace Civi\Setup\Event;

use Civi\Setup\SetupController;

class CreateControllerEvent extends BaseSetupEvent {

  protected $ctrl;

  /**
   * @return SetupControllerInterface
   */
  public function getCtrl() {
    return $this->ctrl;
  }

  /**
   * @param SetupControllerInterface $ctrl
   */
  public function setCtrl($ctrl) {
    $this->ctrl = $ctrl;
  }

}
