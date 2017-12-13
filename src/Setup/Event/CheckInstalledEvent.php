<?php
namespace Civi\Setup\Event;

class CheckInstalledEvent extends BaseSetupEvent {

  /**
   * @var string
   *   One of the following:
   *     - uninstalled
   *     - installed
   *     - partial
   */
  private $status = 'uninstalled';

  /**
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param string $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

}
