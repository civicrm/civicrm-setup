<?php \Civi\Setup::assertRunning(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $short_lang_code; ?>" lang="<?php echo $short_lang_code; ?>" dir="<?php echo $text_direction; ?>">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo ts('CiviCRM Installer'); ?></title>
  <script type="text/javascript" src="<?php echo $jqueryURL; ?>"></script>
  <script type="text/javascript">
    window.csj$ = jQuery.noConflict();
  </script>
  <link rel="stylesheet" type="text/css" href=<?php echo $installURLPath . "template.css"?> />
<?php
if ($text_direction == 'rtl') {
  echo "  <link rel='stylesheet' type='text/css' href='{$installURLPath}template-rtl.css' />\n";
}
?>
</head>
<body>

<?php
$mainClasses = array(
  'civicrm-setup-body',
  count($reqs->getErrors()) ? 'has-errors' : '',
  count($reqs->getWarnings()) ? 'has-warnings' : '',
  (count($reqs->getErrors()) + count($reqs->getWarnings()) > 0) ? 'has-problems' : '',
  (count($reqs->getErrors()) + count($reqs->getWarnings()) === 0) ? 'has-no-problems' : '',
);
?>

<div class="<?php echo implode(' ', $mainClasses); ?>">
<div id="All">
  <div class="civicrm-logo"><strong><?php echo ts('Version %1', array(1 => "{$civicrm_version} {$model->cms}")); ?></strong><br/>
    <span><img src=<?php echo $installURLPath . "block_small.png"?> /></span>
  </div>

<h1><?php echo ts("CiviCRM Installer"); ?></h1>

<noscript>
<p class="error"><?php echo ts("Error: Javascipt appears to be disabled. The CiviCRM web-based installer requires Javascript.");?></p>
</noscript>

<p><?php echo ts("Thanks for choosing CiviCRM! Please follow the instructions below to install CiviCRM."); ?></p>

<form name="civicrm_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">

  <div class="cvs-requirements if-problems"><?php include __DIR__ . DIRECTORY_SEPARATOR . './block_requirements.php'; ?></div>
  <div class="cvs-l10n if-no-errors"><?php include __DIR__ . DIRECTORY_SEPARATOR . './block_l10n.php'; ?></div>
  <div class="cvs-sample-data if-no-errors"><?php include __DIR__ . DIRECTORY_SEPARATOR . './block_sample_data.php'; ?></div>
  <div class="cvs-components if-no-errors"><?php include __DIR__ . DIRECTORY_SEPARATOR . './block_components.php'; ?></div>
  <div class="cvs-advanced"><?php include __DIR__ . DIRECTORY_SEPARATOR . './block_advanced.php'; ?></div>

  <div class="cvs-install if-no-errors">
    <input id="install_button" type="submit" name="civisetup[action][Install]"
           value="<?php echo htmlentities(ts('Install CiviCRM')); ?>"
           onclick="document.getElementById('saving_top').style.display = ''; this.value = '<?php echo ts('Installing CiviCRM...', array('escape' => 'js')); ?>'"/>

    <span id="saving_top" style="display: none">
  &nbsp;
    <img src=<?php echo $installURLPath . "network-save.gif" ?>/>
      <?php echo ts('(this will take a few minutes)'); ?>
  </span>
  </div>

</form>
</div>
</div>
</body>
</html>
