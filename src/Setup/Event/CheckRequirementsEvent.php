<?php
namespace Civi\Setup\Event;

/**
 * Check if the local system meets the installation requirements.
 *
 * Event Name: 'civi.setup.checkRequirements'
 */
class CheckRequirementsEvent extends BaseSetupEvent {

  protected $messages;

  /**
   * @param string $level
   *   Severity/level.
   *   Ex: 'info', 'warning', 'error'.
   * @param string $section
   *   Symbolic machine name for this group of messages.
   *   Ex: 'database' or 'system'.
   * @param string $name
   *   Symbolic machine name for this particular message.
   *   Ex: 'mysqlThreadstack'
   * @param string $message
   *   Displayable explanation.
   *   Ex: 'The MySQL thread stack is too small.'
   * @return $this
   */
  public function addMessage($level, $section, $name, $message) {
    $this->messages[$name] = array(
      'section' => $section,
      'name' => $name,
      'message' => $message,
      'level' => $level,
    );
    return $this;
  }

  public function addInfo($section, $name, $message = '') {
    return $this->addMessage('info', $section, $name, $message);
  }

  public function addError($section, $name, $message = '') {
    return $this->addMessage('error', $section, $name, $message);
  }

  public function addWarning($section, $name, $message = '') {
    return $this->addMessage('warning', $section, $name, $message);
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
