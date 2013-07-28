<?php
/* Centralized Users: Chat */

# thisfile
  $thisfile = basename(__FILE__, ".php");
 
# language
  i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US');
 
# requires
  require_once(GSPLUGINPATH.$thisfile.'/php/class.php');
  
# class instantiation
  $mcuserschat = new MatrixCUsersChat; // instantiate class

# register plugin
  register_plugin(
    $mcuserschat->pluginInfo('id'),           // id
    $mcuserschat->pluginInfo('name'),         // name
    $mcuserschat->pluginInfo('version'),      // version
    $mcuserschat->pluginInfo('author'),       // author
    $mcuserschat->pluginInfo('url'),          // url
    $mcuserschat->pluginInfo('description'),  // description
    $mcuserschat->pluginInfo('page'),         // page type - on which admin tab to display
    array($mcuserschat, 'admin')              // administration function
  );

# activate actions/filters
  # front-end
    add_action('error-404', array($mcuserschat, 'display')); // display for plugin
  # back-end
    add_action($mcuserschat->pluginInfo('page').'-sidebar', 'createSideMenu' , array($mcuserschat->pluginInfo('id'), $mcuserschat->pluginInfo('sidebar'))); // sidebar link
 
# functions
  function cusers_chat($editor=false) {
    $mcuserschat = new MatrixCUsersChat;
    $mcuserschat->chatbox($editor);
  }
 
?>