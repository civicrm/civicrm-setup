<?php
namespace Civi\Setup\Event;

use Civi\Setup\UI\SetupController;

/**
 * Create a web-based UI for handling the installation.
 *
 * Event Name: 'civi.setup.createController'
 */
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
