<?php if (!defined('CIVI_SETUP')): exit("Installation plugins must only be loaded by the installer.\n"); endif; ?>
<h2><?php echo ts('Details'); ?></h2>

<p>
  <?php echo ts('The system settings were auto-detected. CiviCRM will be installed with:'); ?>
</p>

<div style="">
  <table class="settingsTable">
    <tbody>
    <tr>
      <th><?php echo ts('CMS Database'); ?></th>
      <td>
        <code><?php echo htmlentities('mysql://' . $model->cmsDb['username'] . ':CENSORED@' . $model->cmsDb['server'] . '/' . $model->cmsDb['database']); ?></code>
      </td>
    </tr>
    <tr>
      <th><?php echo ts('CiviCRM Database'); ?></th>
      <td>
        <code><?php echo htmlentities('mysql://' . $model->db['username'] . ':CENSORED@' . $model->db['server'] . '/' . $model->db['database']); ?></code>
      </td>
    </tr>
    <tr>
      <th><?php echo ts('CiviCRM Settings File'); ?></th>
      <td><code><?php echo htmlentities($model->settingsPath); ?></code></td>
    </tr>
    <tr>
      <th><?php echo ts('CiviCRM Source Code'); ?></th>
      <td><code><?php echo htmlentities($model->srcPath); ?></code></td>
    </tr>
    </tbody>
  </table>
</div>

<p class="tip">
  <strong><?php echo ts('Tip'); ?></strong>:
  <?php echo ts('Need more advanced control? You may alternatively use the <a href="%1" target="%2">command-line installer</a>.', array(1 => 'https://github.com/civicrm/cv', 2 => '_blank')); ?>
</p>
