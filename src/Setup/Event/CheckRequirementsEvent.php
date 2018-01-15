<?php
namespace Civi\Setup\Event;

class CheckRequirementsEvent extends BaseSetupEvent {

  protected $messages;

  /**
   * @param string $level
   *   Severity/level.
   *   Ex: 'info', 'warning', 'error'.
   * @param string $name
   *   Symbolic machine name.
   *   Ex: 'mysqlThreadstack'
   * @param string $message
   *   Displayable explanation.
   *   Ex: 'The MySQL thread stack is too small.'
   * @return $this
   */
  public function addMessage($level, $name, $message) {
    $this->messages[$name] = array(
      'name' => $name,
      'message' => $message,
      'level' => $level,
    );
    return $this;
  }

  public function addInfo($name, $message = '') {
    return $this->addMessage('info', $name, $message);
  }

  public function addError($name, $message = '') {
    return $this->addMessage('error', $name, $message);
  }

  public function addWarning($name, $message = '') {
    return $this->addMessage('warning', $name, $message);
  }

  /**
   * @param string|NULL $level
   *   Filter by severity of the message.
   *   Ex: 'info', 'error', 'warning'.
   * @return array
   *   List of messages. Each has fields:
   *     - name: string, symbolic name.
   *     - message: string, displayable message.
   *     - level: string, ex: 'info', 'warning', 'error'.
   */
  public function getMessages($level = NULL) {
    if ($level === NULL) {
      return $this->messages;
    }
    else {
      return array_filter($this->messages, function ($m) use ($level) {
        return $m['level'] == $level;
      });
    }
  }

  public function getInfos() {
    return $this->getMessages('info');
  }

  public function getErrors() {
    return $this->getMessages('error');
  }

  public function getWarnings() {
    return $this->getMessages('warning');
  }

}
