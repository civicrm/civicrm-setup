<?php
/**
 * @file
 *
 * Verify that the CMS base URL is well-formed.
 *
 * Ex: When installing via CLI, the URL cannot be determined automatically.
 */

if (!defined('CIVI_SETUP')) {
  exit();
}

\Civi\Setup::dispatcher()
  ->addListener('civi.setup.checkRequirements', function (\Civi\Setup\Event\CheckRequirementsEvent $e) {
    $model = $e->getModel();

    if (!$model->cmsBaseUrl || !preg_match('/^https?:/', $model->cmsBaseUrl)) {
      $e->addError('cmsBaseUrl', "The \"cmsBaseUrl\" ($model->cmsBaseUrl) is unavailable or malformed. Consider setting it explicitly.");
      return;
    }

    if (PHP_SAPI === 'cli' && strpos($model->cmsBaseUrl, dirname($_SERVER['PHP_SELF'])) !== FALSE) {
      $e->addError('cmsBaseUrl', "The \"cmsBaseUrl\" ($model->cmsBaseUrl) is unavailable or malformed. Consider setting it explicitly.");
      return;
    }

    $e->addOk('cmsBaseUrl', 'The "cmsBaseUrl" appears well-formed.');
  });
