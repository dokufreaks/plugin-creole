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
class syntax_plugin_creole_table extends DokuWiki_Syntax_Plugin {

  function getInfo(){
    return array(
      'author' => 'Brian Hartvigsen, Gina Häußge, Michael Klier, Esther Brunner',
      'email'  => 'dokuwiki@chimeric.de',
      'date'   => '2008-02-23',
      'name'   => 'Creole Plugin, table component',
      'desc'   => 'Creole style tables',
      'url'    => 'http://wiki.splitbrain.org/plugin:creole',
    );
  }
  
  function getType() { return 'container'; }
  function getSort() { return 59; }

  function getAllowedTypes(){
    return array('formatting', 'substition', 'disabled', 'protected');
  }

  function connectTo($mode) {
    $this->Lexer->addEntryPattern('\n\|=',$mode,'plugin_creole_table');
    $this->Lexer->addEntryPattern('\n\|',$mode,'plugin_creole_table');
  }

  function postConnect() {
    $this->Lexer->addPattern('\n\|=','plugin_creole_table');
    $this->Lexer->addPattern('\n\|','plugin_creole_table');
    $this->Lexer->addPattern('[\t ]+','plugin_creole_table');
    $this->Lexer->addPattern('\|=','plugin_creole_table');
    $this->Lexer->addPattern('\|','plugin_creole_table');
    $this->Lexer->addExitPattern('\n','plugin_creole_table');
  }
  
  function handle($match, $state, $pos, &$handler) {
    switch ( $state ) {
      case DOKU_LEXER_ENTER:
        $ReWriter = & new Doku_Handler_Table($handler->CallWriter);
        $handler->CallWriter = & $ReWriter;

        $handler->_addCall('table_start', array(), $pos);
        //$handler->_addCall('table_row', array(), $pos);
        if ( trim($match) == '|=' ) {
          $handler->_addCall('tableheader', array(), $pos);
        } else {
          $handler->_addCall('tablecell', array(), $pos);
        }
      break;

      case DOKU_LEXER_EXIT:
        $handler->_addCall('table_end', array(), $pos);
        $handler->CallWriter->process();
        $ReWriter = & $handler->CallWriter;
        $handler->CallWriter = & $ReWriter->CallWriter;
      break;

      case DOKU_LEXER_UNMATCHED:
        if ( trim($match) != '' ) {
          $handler->_addCall('cdata',array($match), $pos);
        }
      break;

      case DOKU_LEXER_MATCHED:
        if ( $match == ' ' ){
          $handler->_addCall('cdata', array($match), $pos);
        } else if ( preg_match('/\t+/',$match) ) {
          $handler->_addCall('table_align', array($match), $pos);
        } else if ( preg_match('/ {2,}/',$match) ) {
          $handler->_addCall('table_align', array($match), $pos);
        } else if ( $match == "\n|" ) {
          $handler->_addCall('table_row', array(), $pos);
          $handler->_addCall('tablecell', array(), $pos);
        } else if ( $match == "\n|=" ) {
          $handler->_addCall('table_row', array(), $pos);
          $handler->_addCall('tableheader', array(), $pos);
        } else if ( $match == '|' ) {
          $handler->_addCall('tablecell', array(), $pos);
        } else if ( $match == '|=' ) {
          $handler->_addCall('tableheader', array(), $pos);
        }
      break;
    }
    return true;
  }
  
  function render($mode, &$renderer, $data){
    return true;
  }
}
