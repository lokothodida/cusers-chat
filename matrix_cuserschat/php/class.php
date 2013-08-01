<?php

class MatrixCUsersChat {
  /* constants */
  const FILE              = 'matrix_cuserschat';
  const ID                = 'cusers_chat';
  const VERSION           = '0.1';
  const AUTHOR            = 'Lawrence Okoth-Odida';
  const URL               = 'http://lokida.co.uk';
  const PAGE              = 'plugins';
  const TABLE_SHOUTS      = 'cusers-chat';
  const TABLE_CONFIG      = 'cusers-chat-config';
  
  /* properties */
  private $plugin;
  private $tables;
  private $matrix;
  private $parser;
  private $schema;
  private $directories;
  private $users;
  private $health;
  
  /* methods */
  # constructor
  public function __construct() {
    // plugin details
    $this->plugin = array();
    $this->plugin['id']          = self::FILE;
    $this->plugin['name']        = i18n_r(self::FILE.'/PLUGIN_TITLE');
    $this->plugin['version']     = self::VERSION;
    $this->plugin['author']      = self::AUTHOR;
    $this->plugin['url']         = self::URL;
    $this->plugin['description'] = i18n_r(self::FILE.'/PLUGIN_DESC');
    $this->plugin['page']        = self::PAGE;
    $this->plugin['sidebar']     = i18n_r(self::FILE.'/PLUGIN_SIDEBAR');
    
    if ($this->checkDependencies()) {
      $this->matrix = new TheMatrix;
      $this->parser = new TheMatrixParser;
      $this->core   = new MatrixCUsers;
      $this->tables = array(self::TABLE_SHOUTS => array(), self::TABLE_CONFIG => array());
      $this->schema = array();
      $this->config = array();
      $this->users  = $this->core->returnUsers();
      
      // directories
      $this->directories = array();
      $this->directories['data']['core'] = array('dir' => GSDATAOTHERPATH.self::ID.'/');
      $this->directories['plugins']['php'] = GSPLUGINPATH.self::FILE.'/php/';
      $this->core->mkdir($this->directories['data']);
      
      // create tables
      $this->createTables();
      
      // config
      $config = $this->matrix->recordExists(self::TABLE_CONFIG, 0);
      $this->config['title'] = $config['title'];
      $this->config['slug'] = $config['slug'];
      $this->config['shouts-per-page'] = $config['shouts-per-page'];
      $this->config['max-shouts'] = $config['max-shouts'];
      if ($config['allow-guests'] == i18n_r(MatrixCUsers::FILE.'/YES')) {
           $this->config['allow-guests'] = true;
      }
      else $this->config['allow-guests'] = false;
      $this->config['template'] = $config['template'];
      $this->config['css'] = $config['css'];
      $this->health = true;
    }
    else $this->health = false;
  }
  
  # get plugin info
  public function pluginInfo($info) {
    if (isset($this->plugin[$info])) {
      return $this->plugin[$info];
    }
    else return null;
  }
  
  # check dependencies
  private function checkDependencies() {
    if (
      (class_exists('TheMatrix') && TheMatrix::VERSION >= '1.02') &&
      (class_exists('MatrixCUsers') && MatrixCUsers::VERSION >= '1.01')
    ) return true;
    else return false;
  }
  
  # missing dependencies
  private function missingDependencies() {
    $dependencies = array();
    
    if (!(class_exists('TheMatrix') && TheMatrix::VERSION >= '1.02')) {
      $dependencies[] = array('name' => 'The Matrix (1.02+)', 'url' => 'https://github.com/n00dles/DM_matrix/');
    }
    if (!(class_exists('MatrixCUsers') && MatrixCUsers::VERSION >= '1.01')) {
      $dependencies[] = array('name' => 'Centralized Users (1.01+)', 'url' => 'http://get-simple.info/extend/plugin/centralised-users/657/');
    }
    
    return $dependencies;
  }
  
  # create tables
  private function createTables() {
    $tables = $this->tables;
    include(GSPLUGINPATH.self::FILE.'/php/admin/tables.php');
    $this->core->buildSchema($tables);
  }
  
  # reset
  private function reset($url) {
    foreach ($this->tables as $table => $array) {
      $this->matrix->deleteTable($table);
    }
    $this->createTables();
    echo '<script>window.location = "'.$url.'"</script>';
  }
  
  # get shouts
  private function getShouts() {
    $return = array();
    $shouts = $this->matrix->query('SELECT * FROM '.self::TABLE_SHOUTS.' ORDER BY date DESC', 'MULTI', true, 'date');
    $pages = count($shouts) / $this->config['shouts-per-page'];
    $tmp = array();
    for ($i = 0; $i < $pages; $i++) {
      $arr = array_slice($shouts, ($i * $this->config['shouts-per-page']), $this->config['shouts-per-page'], true);
      $tmp[] = array_reverse($arr);
    }
    foreach ($tmp as $t) $return = array_merge($return, $t);
    return $return;
  }
  
  # add shout
  private function addShout($array) {
    if ($this->core->loggedIn()) {
      $array['author'] = $_SESSION['cuser']['id'];
    }
    else $array['author'] = -1;
    $array['date'] = time();
    $array['ip'] = $this->core->getIP();
    return $this->matrix->createRecord(self::TABLE_SHOUTS, $array);
  }
  
  # display shout
  private function displayShout($shout) {
    $user = $this->users[$shout['author']];
    if ($user['id'] == -1) {
      $user['email'] = $shout['email'];
      $url = 'mailto:'.$user['email'];
    }
    else {
      $url = $this->core->getProfileURL($user['username']);
    }
    ?>
    <div class="shout">
      <?php echo $this->core->displayAvatar($user, 25); ?>
      <span class="user">
        <a href="<?php echo $url; ?>"><?php echo $user['displayname']; ?></a>
      </span>
      <span class="content"><?php echo $this->parser->bbcode($shout['content'], $this->core->getSmilies()); ?></span>
      <span class="date"><?php echo $this->core->date($shout['date']); ?></span>
    </div>
    <?php
  }
  
  # archive existing shouts
  private function archiveShouts($shouts) {
    if (count($shouts) >= $this->config['max-shouts']) {
      $num = ceil($this->config['max-shouts'] * 0.4);
      $archive = array_slice($shouts, -$num, $num);
      foreach ($archive as $arc) {
        $this->matrix->deleteRecord(self::TABLE_SHOUTS, $arc['id']);
      }
    }
    else return false;
  }
  
  # display chatbox
  public function chatbox($editor=false) {
    if ($this->health) {
      // css
      echo $this->config['css'];
      
      // add shout
      if (!empty($_POST['submitShout'])) $this->addShout($_POST);
      
      // display the chatbox
      $shouts = $this->getShouts();
      $archive = $this->archiveShouts($shouts);
      ?>
      <div class="chatbox">
        <div class="title"><?php echo $this->config['title']; ?></div>
        <div class="links top"></div>
        <div class="shouts">
          <?php foreach ($shouts as $shout) $this->displayShout($shout); ?>
        </div>
        <div class="links bottom"></div>
        <?php if ($this->core->loggedIn() || $this->config['allow-guests'])  { ?>
        <form method="post">
          <?php if (!$this->core->loggedIn()) { ?>
            <?php $this->matrix->displayField(self::TABLE_SHOUTS, 'email', ''); ?>
          <?php }?>
          <?php if ($editor) { ?>
          <?php $this->matrix->displayField(self::TABLE_SHOUTS, 'content', ''); ?>
          <?php } else { ?>
          <textarea name="content" class="content" required></textarea>
          <?php } ?>
          <div class="options">
            <input type="submit" class="addShout" name="submitShout">
            <a class="refresh" href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo i18n_r(self::FILE.'/REFRESH'); ?></a>
          </div>
        </form>
        <?php } ?>
      </div>
      <script>
        $(document).ready(function() {
          $('.chatbox').pajinate({
            'items_per_page': <?php echo json_encode($this->config['shouts-per-page']); ?>,
            'item_container_id': '.shouts',
            'nav_panel_id': '.links',
            'nav_label_first': '|&lt;&lt;',
            'nav_label_prev': '&lt;',
            'nav_label_next': '&gt;',
            'nav_label_last': '&gt;&gt;|',
          });
        }); // ready
      </script>
      <?php
    }
  }
  
  # display page
  public function display() {
    global $id, $data_index;
    if ($id == $this->config['slug']) {
      // meta
      $data_index->title         = $this->config['title'];
      $data_index->url           = $this->config['slug'];
      $data_index->template      = $this->config['template'];
      $data_index->content       = $this->core->getConfig('header-css');
      
      // content
      ob_start();
      $this->chatbox(true);
      $data_index->content .= ob_get_contents();
      ob_end_clean();
    }    
  }
  
  # content (placeholders)
  public function content($content) {
    $placeholders = $replacements = array();
    
    $placeholders[] = '(% cusers_chat %)';
    
    ob_start();
    $this->chatbox(true);
    $replacements[] = ob_get_contents();
    ob_end_clean();
    
    return str_replace($placeholders, $replacements, $content);
  }

  # admin panel
  public function admin() {
    $url = 'load.php?id='.self::FILE;
    if ($this->health) {
      if (isset($_GET['shouts'])) {
        include(GSPLUGINPATH.self::FILE.'/php/admin/shouts.php');
      }
      else {
        include(GSPLUGINPATH.self::FILE.'/php/admin/config.php');
      }
    }
    else {
      $dependencies = $this->missingDependencies();
      include(GSPLUGINPATH.self::FILE.'/php/admin/dependencies.php');
    }
  }
}

?>