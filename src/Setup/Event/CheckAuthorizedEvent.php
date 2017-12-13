<?php
namespace Civi\Setup\Event;

class CheckAuthorizedEvent extends BaseSetupEvent {

  /**
   * @var bool
   */
  private $authorized = FALSE;

  /**
   * @return bool
   */
  public function isAuthorized() {
    return $this->authorized;
  }

  /**
   * @param bool $authorized
   */
  public function setAuthorized($authorized) {
    $this->authorized = $authorized;
  }

}
