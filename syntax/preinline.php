<?php
/**
 * Creole Plugin, inline preformatted component: Creole style preformatted text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */
 
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_preinline extends DokuWiki_Syntax_Plugin {
 
  function getInfo(){
    return array(
      'author' => 'Esther Brunner',
      'email'  => 'wikidesign@gmail.com',
      'date'   => '2006-10-02',
      'name'   => 'Creole Plugin, inline preformatted component',
      'desc'   => 'Creole style preformatted text',
      'url'    => 'http://wiki.splitbrain.org/plugin:creole',
    );
  }

  function getType(){ return 'protected'; }
  function getSort(){ return 102; }
  
  function connectTo($mode){
    $this->Lexer->addEntryPattern(
      '\{\{\{(?=.*?\}\}\})',
      $mode,
      'plugin_creole_preinline'
    );
  }
  
  function postConnect(){
    $this->Lexer->addExitPattern(
      '\}\}\}',
      'plugin_creole_preinline'
    );
  }
  
  function handle($match, $state, $pos, &$handler){
    switch ($state){
      case DOKU_LEXER_ENTER:
        $handler->_addCall('monospace_open', array(), $pos);
        break;
      case DOKU_LEXER_UNMATCHED:
        $handler->_addCall('unformatted', array($match), $pos);
        break;
      case DOKU_LEXER_EXIT:
        $handler->_addCall('monospace_close', array(), $pos);
        break;
    }
    return true;
  }
  
  function render($mode, &$renderer, $data){
    return true;
  }
}
     
//Setup VIM: ex: et ts=4 enc=utf-8 :