<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_creole extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Esther Brunner',
      'email'  => 'wikidesign@gmail.com',
      'date'   => '2007-01-26',
      'name'   => 'Creole Plugin',
      'desc'   => 'Changes the editor toolbar if markup precedence is set to Creole',
      'url'    => 'http://www.wikidesign.ch/en/plugin/creole/start',
    );
  }

  /**
   * register the eventhandlers
   */
  function register(&$contr){
    $contr->register_hook('TOOLBAR_DEFINE',
                          'AFTER',
                          $this,
                          'define_toolbar',
                           array());
  }

  /**
   * modifiy the toolbar JS defines
   *
   * @author  Esther Brunner  <wikidesign@gmail.com>
   */
  function define_toolbar(&$event, $param){
    // return false;  
    if ($this->getConf('precedence') != 'creole') return false; // leave untouched
        
    $c = count($event->data);
    for ($i = 0; $i <= $c; $i++){
      if ($event->data[$i]['type'] == 'format'){
        
        // headers
        if (preg_match("/h(\d)\.png/", $event->data[$i]['icon'], $match)){
          $markup = substr('======', 0, $match[1] + 1);
          $event->data[$i]['open']  = $markup." ";
          $event->data[$i]['close'] = " ".$markup."\\n";
          
        // ordered lists
        } elseif ($event->data[$i]['icon'] == 'ol.png'){
          $event->data[$i]['open']  = "# ";
          
        // unordered lists
        } elseif ($event->data[$i]['icon'] == 'ul.png'){
          $event->data[$i]['open']  = "* ";
        }
      }
    }
    
    return true;
  }
  
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
