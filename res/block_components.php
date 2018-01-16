<?php \Civi\Setup::assertRunning(); ?>
<h2 id="components"><?php echo ts('Components'); ?></h2>

<?php

$desc = array(
  'CiviContribute' => ts('Accept donations and payments'),
  'CiviEvent' => ts('Accept event registrations'),
  'CiviMail' => ts('Send email blasts and newsletters'),
  'CiviMember' => ts('Accept recurring memberships'),
  'CiviCase' => ts('Track case histories'),
  'CiviPledge' => ts('Accept pledges'),
  'CiviReport' => ts('Generate reports'),
  'CiviCampaign' => ts('Organize campaigns'),
  'CiviGrant' => ts('Receive grant applications'),
);
?>

<div>
  <?php
  foreach ($model->getField('components', 'options') as $comp => $label) {
  ?>
    <input class="comp-cb sr-only" style="display: none;" type="checkbox" name="civisetup[components][<?php echo $comp; ?>]" id="civisetup[components][<?php echo $comp; ?>]" <?php echo in_array($comp, $model->components) ? 'checked' : '' ?>>
    <label class="comp-box" for="civisetup[components][<?php echo $comp; ?>]">
      <span class="comp-label"><?php echo $label; ?></span>
      <span class="comp-desc"><?php echo $desc[$comp] ?></span>
    </label>
  <?php
  }
  ?>
</div>

<p class="tip">
  <strong><?php echo ts('Tip'); ?></strong>:
  <?php echo ts('Not sure? That\'s OK. After installing, you can enable and disable components at any time.'); ?>
</p>
