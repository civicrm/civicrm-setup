<?php \Civi\Setup::assertRunning(); ?>
<h2 id="requirements"><?php echo ts('System Requirements'); ?></h2>

<?php
if (count($reqs->getErrors()) > 0) {
  ?><p class="error"><?php echo ts('We are not able to install the software. Please review the errors and warnings below.'); ?></p><?php
}
elseif (count($reqs->getWarnings()) > 0) {
  ?><p class="warning"><?php echo ts('There are some issues that we recommend you look at before installing. However, you are still able to install the software.'); ?></p><?php
}
else {
  ?><p class="good"><?php echo ts("You're ready to install!"); ?></p><?php
}
?>

<?php
$severityLabels = array('info' => ts('Info'), 'warning' => ts('Warning'), 'error' => ts('Error'));
?>

<table class="reqTable">
  <thead>
  <tr>
    <th width="10%"><?php echo ts('Severity'); ?></th>
    <th width="20%"><?php echo ts('Name'); ?></th>
    <th width="69%"><?php echo ts('Details'); ?></th>
  </tr>
  </thead>
  <tbody>
  <?php
  foreach ($reqs->getErrors() as $msg) {
    ?>
  <tr class="<?php echo 'reqSeverity-' . $msg['level']; ?>">
    <td><?php echo htmlentities($severityLabels[$msg['level']]); ?></td>
    <td><?php echo htmlentities($msg['name']); ?></td>
    <td><?php echo htmlentities($msg['message']); ?></td>
  </tr>
  <?php
  }
  ?>
  <?php
  foreach ($reqs->getWarnings() as $msg) {
    ?>
  <tr class="<?php echo 'reqSeverity-' . $msg['level']; ?>">
    <td><?php echo htmlentities($severityLabels[$msg['level']]); ?></td>
    <td><?php echo htmlentities($msg['name']); ?></td>
    <td><?php echo htmlentities($msg['message']); ?></td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>

<input id="recheck_button" type="submit" name="civisetup[action][Start]" value="<?php echo htmlentities(ts('Re-test')); ?>" />
