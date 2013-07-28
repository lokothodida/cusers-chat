<?php

  // save changes
  if ($_SERVER['REQUEST_METHOD']=='POST') {
    $update = $this->matrix->updateRecord(self::TABLE_CONFIG, 0, $_POST);
    
    if ($update) {
      $this->matrix->getAdminError(i18n_r(MatrixCUsers::FILE.'/UPDATE_SUCCESS'), true);
    }
    else {
      $this->matrix->getAdminError(i18n_r(MatrixCUsers::FILE.'/UPDATE_ERROR'), false);
    }
  }
  // reset
  if (isset($_GET['reset'])) {
    $this->reset($url);
  }

?>

<h3 class="floated"><?php echo i18n_r(self::FILE.'/CHATBOX'); ?> (<?php echo i18n_r(MatrixCUsers::FILE.'/CONFIG'); ?>)</h3>
<div class="edit-nav">
  <a href="<?php echo $url; ?>&reset"><?php echo i18n_r(MatrixCUsers::FILE.'/RESET'); ?></a>
  <a href="<?php echo $url; ?>" class="current"><?php echo i18n_r(MatrixCUsers::FILE.'/CONFIG'); ?></a>
  <a href="<?php echo $url; ?>&shouts"><?php echo i18n_r(self::FILE.'/SHOUTS'); ?></a>
  <div class="clear"></div>
</div>

<form method="post" action="<?php echo $url; ?>">
  <?php $this->matrix->displayForm(self::TABLE_CONFIG, 0); ?>
  <input type="submit" class="submit" name="save" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
</form>