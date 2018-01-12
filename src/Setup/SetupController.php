<?php
namespace Civi\Setup;

class SetupController implements SetupControllerInterface {

  const PREFIX = 'civisetup';

  /**
   * @var \Civi\Setup
   */
  protected $setup;

  /**
   * @var array
   *   Some mix of the following:
   *     - res: The base URL for loading resource files (images/javascripts) for this
   *       project. Includes trailing slash.
   *     - ctrl: The URL of this setup controller. May be used for POST-backs.
   */
  protected $urls;

  /**
   * SetupController constructor.
   * @param \Civi\Setup $setup
   */
  public function __construct(\Civi\Setup $setup) {
    $this->setup = $setup;
  }

  /**
   * @param string $method
   *   Ex: 'GET' or 'POST'.
   * @param array $fields
   *   List of any HTTP GET/POST fields.
   * @return array
   *   The HTTP headers and response text.
   *   [0 => $headers, 1 => $body].
   */
  public function run($method, $fields = array()) {
    $fields[self::PREFIX]['action'] = empty($fields[self::PREFIX]['action']) ? 'start' : $fields[self::PREFIX]['action'];
    $func = 'run' . $fields[self::PREFIX]['action'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $fields[self::PREFIX]['action'])
      || !is_callable([$this, $func])
    ) {
      return $this->createError('Invalid action');
    }
    return call_user_func([$this, $func], $method, $fields);
  }

  public function runStart($method, $fields) {
    if (!$this->setup->checkAuthorized()->isAuthorized()) {
      return $this->createError("Not authorized to perform installation");
    }

    $checkInstalled = $this->setup->checkInstalled();
    if ($checkInstalled->isDatabaseInstalled() || $checkInstalled->isSettingInstalled()) {
      return $this->createError("CiviCRM is already installed");
    }

    $body = "<p>Hello world</p>" .
      "<form method='post'><input type='text' name='foo'><input type='submit'></form>";
    $body .= "<pre>" . htmlentities(var_export([
        'method' => $method,
        'urls' => $this->urls,
        'data' => $fields,
      ], 1)) . "</pre>";

    return array(array(), $body);
  }

  public function createError($message) {
    // TODO use error template?
    return array(array(), "<h1>Error</h1> " . htmlentities($message));
  }

  /**
   * @inheritdoc
   */
  public function setUrls($urls) {
    foreach ($urls as $k => $v) {
      $this->urls[$k] = $v;
    }
    return $this;
  }

}
