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
class syntax_plugin_creole_superscript extends DokuWiki_Syntax_Plugin {
 
  function getInfo(){
    return array(
      'author' => 'Brian Hartvigsen, Gina Häußge, Michael Klier, Esther Brunner',
      'email'  => 'dokuwiki@chimeric.de',
      'date'   => '2008-02-23',
      'name'   => 'Creole Plugin, superscript component',
      'desc'   => 'Creole style superscript text',
      'url'    => 'http://wiki.splitbrain.org/plugin:creole',
    );
  }

  function getType(){ return 'formatting'; }
  function getSort(){ return 58; }
  
  function getAllowedTypes()
  {
    return array(
      'formatting',
      'substition',
      'disabled'
    );
  }
  function connectTo($mode){
    $this->Lexer->addEntryPattern(
      '\n?\^\^(?=.*?\^\^)', #\n? is so it will match before DokuWiki's table regex...
      $mode,
      'plugin_creole_superscript'
    );
  }
  
  function postConnect(){
    $this->Lexer->addExitPattern(
      '\^\^',
      'plugin_creole_superscript'
    );
  }
  
  function handle($match, $state, $pos, &$handler){
    switch ($state){
      case DOKU_LEXER_ENTER:
        $handler->_addCall('superscript_open', array(), $pos);
        break;
      case DOKU_LEXER_UNMATCHED:
        $handler->_addCall('unformatted', array($match), $pos);
        break;
      case DOKU_LEXER_EXIT:
        $handler->_addCall('superscript_close', array(), $pos);
        break;
    }
    return true;
  }
  
  function render($mode, &$renderer, $data){
    return true;
  }
}
     
//Setup VIM: ex: et ts=4 enc=utf-8 :
