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
   *   [0 => array $headers, 1 => string $body].
   */
  public function run($method, $fields = array()) {
    if (!$this->setup->checkAuthorized()->isAuthorized()) {
      return $this->createError("Not authorized to perform installation");
    }

    $this->boot($fields);

    $fields[self::PREFIX]['action'] = empty($fields[self::PREFIX]['action']) ? 'Start' : $fields[self::PREFIX]['action'];
    $func = 'run' . $fields[self::PREFIX]['action'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $fields[self::PREFIX]['action'])
      || !is_callable([$this, $func])
    ) {
      return $this->createError('Invalid action');
    }
    return call_user_func([$this, $func], $method, $fields);
  }

  public function runStart($method, $fields) {
    $checkInstalled = $this->setup->checkInstalled();
    if ($checkInstalled->isDatabaseInstalled() || $checkInstalled->isSettingInstalled()) {
      return $this->createError("CiviCRM is already installed");
    }

    /**
     * @var \Civi\Setup\Model $model
     */
    $model = $this->setup->getModel();

    $tplFile = implode(DIRECTORY_SEPARATOR, [$model->setupPath, 'res', 'template.html']);
    $tplVars = [
      'civicrm_version' => \CRM_Utils_System::version(),
      'lang' => $model->lang,
      'loadGenerated' => $model->loadGenerated,
      'installURLPath' => $this->urls['res'],
      'short_lang_code' => \CRM_Core_I18n_PseudoConstant::shortForLong($GLOBALS['tsLocale']),
      'text_direction' => (\CRM_Core_I18n::isLanguageRTL($GLOBALS['tsLocale']) ? 'rtl' : 'ltr'),
      'model' => $model,
      'jqueryURL' => $this->urls['jquery'],
      'reqs' => $this->setup->checkRequirements(),
    ];

    $body = "<p>Hello world</p>" .
      "<form method='post'><input type='text' name='foo'><input type='submit'></form>";
    $body .= "<pre>" . htmlentities(print_r([
        'method' => $method,
        'urls' => $this->urls,
        'data' => $fields,
        'tplFile' => $tplFile,
        'tplVars' => $tplVars,
      ], 1)) . "</pre>";

    $body = $this->render($tplFile, $tplVars);

    return array(array(), $body);
  }

  /**
   * Partially bootstrap Civi services (such as localization).
   */
  protected function boot($fields) {
    $model = $this->setup->getModel();

    define('CIVICRM_UF', $model->cms);

    // Set the Locale (required by CRM_Core_Config)
    global $tsLocale;
    $tsLocale = 'en_US';

    // CRM-16801 This validates that lang is valid by looking in $langs.
    // NB: the variable is initial a $_REQUEST for the initial page reload,
    // then becomes a $_POST when the installation form is submitted.
    $langs = $model->getField('lang', 'options');
    if (array_key_exists('lang', $fields)) {
      $model->lang = $fields['lang'];
    }
    if ($model->lang and isset($langs[$model->lang])) {
      $tsLocale = $model->lang;
    }

    \CRM_Core_Config::singleton(FALSE);
    $GLOBALS['civicrm_default_error_scope'] = NULL;

    // The translation files are in the parent directory (l10n)
    \CRM_Core_I18n::singleton();
  }

  public function createError($message, $title = 'Error') {
    $tplFile = implode(DIRECTORY_SEPARATOR, [$this->setup->getModel()->setupPath, 'res', 'error.html']);
    return array(array(), $this->render($tplFile, [
      'errorTitle' => htmlentities($title),
      'errorMsg' => htmlentities($message),
      'installURLPath' => $this->urls['res'],
    ]));
  }

  /**
   * Render a *.php template file.
   *
   * @param string $_tpl_file
   *   The path to the file.
   * @param array $_tpl_params
   *   Any variables that should be exported to the scope of the template.
   * @return string
   */
  public function render($_tpl_file, $_tpl_params = array()) {
    extract($_tpl_params);
    unset($_tpl_params);
    ob_start();
    require $_tpl_file;
    return ob_get_clean();
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
