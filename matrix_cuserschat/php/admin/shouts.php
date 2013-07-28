<?php
  // delete shout
  if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete = $this->matrix->deleteRecord(self::TABLE_SHOUTS, $_GET['delete']);
    
    if ($delete) {
      $this->matrix->getAdminError(i18n_r(MatrixCUsers::FILE.'/DELETE_SUCCESS'), true);
    }
    else {
      $this->matrix->getAdminError(i18n_r(MatrixCUsers::FILE.'/DELETE_ERROR'), false);
    }
  }

  $shouts = $this->matrix->query('SELECT * FROM '.self::TABLE_SHOUTS.' ORDER BY date DESC', 'MULTI', true, 'date');
  $users  = $this->core->returnUsers();
?>


<h3 class="floated"><?php echo i18n_r(self::FILE.'/SHOUTS'); ?></h3>
<div class="edit-nav">
  <a href="<?php echo $url; ?>"><?php echo i18n_r(MatrixCUsers::FILE.'/CONFIG'); ?></a>
  <a href="<?php echo $url; ?>&shouts" class="current"><?php echo i18n_r(self::FILE.'/SHOUTS'); ?></a>
  <div class="clear"></div>
</div>

<table class="highlight edittable">
  <thead>
    <tr>
      <th style="width: 20%;"><?php echo i18n_r(MatrixCUsers::FILE.'/USER'); ?></th>
      <th style="width: 50%;"><?php echo i18n_r(MatrixCUsers::FILE.'/CONTENT'); ?></th>
      <th style="width: 25%;"><?php echo i18n_r(MatrixCUsers::FILE.'/DATE'); ?></th>
      <th style="width: 5%;" style="text-align: right;"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($shouts as $shout) { ?>
      <tr>
        <td>
          <?php echo $users[$shout['author']]['displayname']; ?><br />
          (<?php echo $shout['ip']; ?>)
        </td>
        <td><?php echo $this->core->bbcode($shout['content']); ?></td>
        <td><?php echo date('r', $shout['date']); ?></td>
        <td style="text-align: right;"><a href="<?php echo $url; ?>&shouts&delete=<?php echo $shout['id']; ?>" class="cancel delete">&times;</a></td>
      </tr>
    <?php } ?>
    <?php if (empty($shouts)) { ?>
      <tr>
        <td colspan="100%"><?php echo i18n_r(self::FILE.'/NO_SHOUTS'); ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>