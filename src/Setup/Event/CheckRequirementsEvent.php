<?php
namespace Civi\Setup\Event;

class CheckRequirementsEvent extends BaseSetupEvent {

  protected $messages;

  public function addMessage($name, $message, $isOK) {
    $this->messages[$name] = array(
      'name' => $name,
      'message' => $message,
      'is_ok' => $isOK,
    );
    return $this;
  }

  public function addOk($name, $message = '') {
    $this->messages[$name] = array(
      'name' => $name,
      'message' => $message,
      'is_ok' => TRUE,
    );
    return $this;
  }

  public function addError($name, $message = '') {
    $this->messages[$name] = array(
      'name' => $name,
      'message' => $message,
      'is_ok' => FALSE,
    );
    return $this;
  }

  /**
   * @return mixed
   */
  public function getMessages() {
    return $this->messages;
  }

  public function getOks() {
    return array_filter($this->messages, function ($m) {
      return $m['is_ok'];
    });
  }

  public function getErrors() {
    return array_filter($this->messages, function ($m) {
      return !$m['is_ok'];
    });
  }

}
