<?php
namespace Civi\Setup\Event;

class CreateFormEvent extends BaseSetupEvent {

  protected $form;

  /**
   * @return mixed
   */
  public function getForm() {
    return $this->form;
  }

  /**
   * @param mixed $form
   */
  public function setForm($form) {
    $this->form = $form;
  }

}
