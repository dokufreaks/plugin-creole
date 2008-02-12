<?php
/**
 * Creole Plugin, preformatted block component: Creole style preformatted text
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
class syntax_plugin_creole_preblock extends DokuWiki_Syntax_Plugin {
 
  function getInfo(){
    return array(
      'author' => 'Gina Häußge, Michael Klier, Esther Brunner',
      'email'  => 'dokuwiki@chimeric.de',
      'date'   => '2008-02-12',
      'name'   => 'Creole Plugin, preformatted block component',
      'desc'   => 'Creole style preformatted text',
      'url'    => 'http://wiki.splitbrain.org/plugin:creole',
    );
  }

  function getType(){ return 'protected'; }
  function getPType(){ return 'block'; }
  function getSort(){ return 101; }
  
  function connectTo($mode){
    $this->Lexer->addEntryPattern(
      '\n\{\{\{\n(?=.*?\n\}\}\}\n)',
      $mode,
      'plugin_creole_preblock'
    );
  }
  
  function postConnect(){
    $this->Lexer->addExitPattern(
      '\n\}\}\}\n',
      'plugin_creole_preblock'
    );
  }
  
  function handle($match, $state, $pos, &$handler){
    if ($state == DOKU_LEXER_UNMATCHED){
      $handler->_addCall('preformatted', array($match), $pos);
    }
    return true;
  }
  
  function render($mode, &$renderer, $data){
    return true;
  }
}
     
//Setup VIM: ex: et ts=4 enc=utf-8 :
