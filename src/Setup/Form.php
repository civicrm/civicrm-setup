<?php
namespace Civi\Setup;

class Form {

  /**
   * Execute the form and display the output.
   *
   * @param array $postFields
   *   List of any HTTP POST fields.
   */
  public function run($postFields = array()) {
    list ($headers, $body) = $this->main($postFields);
    foreach ($headers as $k => $v) {
      header("$k: $v");
    }
    echo $body;
    exit();
  }

  /**
   * @param array $postFields
   *   List of any HTTP POST fields.
   * @return array
   *   The HTTP headers and response text.
   *   [0 => $headers, 1 => $body].
   */
  public function main($postFields = array()) {
    return array(array(), "<p>Hello world</p>");
  }

}
