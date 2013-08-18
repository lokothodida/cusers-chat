<?php
  // shouts
  $tables[self::TABLE_SHOUTS]['id'] = 0;
  $tables[self::TABLE_SHOUTS]['fields'] = array(
    array(
      'name' => 'date',
      'label' => i18n_r(MatrixCUsers::FILE.'/DATE'),
      'type' => 'datetimelocal',
    ),
    array(
      'name' => 'email',
      'label' => i18n_r(MatrixCUsers::FILE.'/EMAIL'),
      'placeholder' => 'user@email.com',
      'type' => 'email',
      'required' => 'required',
    ),
    array(
      'name' => 'author',
      'label' => i18n_r(MatrixCUsers::FILE.'/AUTHOR'),
      'type' => 'int',
    ),
    array(
      'name' => 'date',
      'label' => i18n_r(MatrixCUsers::FILE.'/DATE'),
      'type' => 'datetimelocal',
    ),
    array(
      'name' => 'ip',
      'label' => i18n_r(MatrixCUsers::FILE.'/IP'),
      'type' => 'text',
      'readonly' => 'readonly',
    ),
    array(
      'name' => 'content',
      'label' => i18n_r(MatrixCUsers::FILE.'/CONTENT'),
      'type' => 'bbcodeeditor',
      'required' => 'required',
    ),
  );
  $tables[self::TABLE_SHOUTS]['maxrecords'] = 0;
  $tables[self::TABLE_SHOUTS]['records'] = array();
  
  // config
  $tables[self::TABLE_CONFIG]['id'] = 0;
  $tables[self::TABLE_CONFIG]['fields'] = array(
    array(
      'name' => 'title',
      'placeholder' => i18n_r(MatrixCUsers::FILE.'/TITLE'),
      'type' => 'textlong',
    ),
    array(
      'name' => 'shouts-per-page',
      'label' => i18n_r(self::FILE.'/SHOUTS_PER_PAGE'),
      'type' => 'int',
      'class' => 'leftsec',
    ),
    array(
      'name' => 'slug',
      'label' => i18n_r(MatrixCUsers::FILE.'/SLUG'),
      'type' => 'slug',
      'class' => 'leftsec',
    ),
    array(
      'name' => 'max-shouts',
      'label' => i18n_r(self::FILE.'/MAX_SHOUTS'),
      'type' => 'int',
      'class' => 'rightsec',
    ),
    array(
      'name' => 'template',
      'label' => i18n_r(MatrixCUsers::FILE.'/TEMPLATE'),
      'type' => 'template',
      'class' => 'rightsec',
    ),
    array(
      'name' => 'allow-guests',
      'label' => i18n_r(MatrixCUsers::FILE.'/ALLOW_GUESTS'),
      'type' => 'dropdowncustom',
      'options' => implode("\n", array(i18n_r(MatrixCUsers::FILE.'/YES'), i18n_r(MatrixCUsers::FILE.'/NO'))),
      'default' => i18n_r(MatrixCUsers::FILE.'/NO'),
      'class' => 'rightsec',
    ),
    array(
      'name' => 'css',
      'label' => i18n_r(MatrixCUsers::FILE.'/CSS'),
      'type' => 'codeeditor',
    ),
  );
  $tables[self::TABLE_CONFIG]['maxrecords'] = 1;
  $tables[self::TABLE_CONFIG]['records'] = array();
  $tables[self::TABLE_CONFIG]['records'][] = array(
    'title' => 'Your GetSimple Chatbox',
    'shouts-per-page' => 10,
    'slug' => 'chat',
    'max-shouts' => 50,
    'template' => 'template.php',
    'css' => "<style>\n".file_get_contents(GSPLUGINPATH.self::FILE.'/css/default.css')."\n</style>",
  );
?>