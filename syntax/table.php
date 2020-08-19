<?php
/**
 * Creole Plugin, preformatted block component: Creole style preformatted text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */
 
use dokuwiki\Parsing\Handler\Table;

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_table extends DokuWiki_Syntax_Plugin {
  
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

  /**
   * Constructor.
   */
  public function __construct() {
    $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
  }
  
  function handle($match, $state, $pos, Doku_Handler $handler) {
    switch ( $state ) {
      case DOKU_LEXER_ENTER:
        $this->eventhandler->notifyEvent('open', 'table', NULL, $pos, $match, $handler);
        $ReWriter = new Table($handler->getCallWriter());
        $handler->setCallWriter($ReWriter);

        $handler->addCall('table_start', array(), $pos);
        //$handler->_addCall('table_row', array(), $pos);
        if ( trim($match) == '|=' ) {
          $handler->addCall('tableheader', array(), $pos);
        } else {
          $handler->addCall('tablecell', array(), $pos);
        }
      break;

      case DOKU_LEXER_EXIT:
        $this->eventhandler->notifyEvent('close', 'table', NULL, $pos, $match, $handler);
        $handler->addCall('table_end', array(), $pos);
        $handler->getCallWriter()->process();
        $ReWriter = & $handler->getCallWriter();
        $handler->setCallWriter($ReWriter->getCallWriter());
      break;

      case DOKU_LEXER_UNMATCHED:
        if ( trim($match) != '' ) {
          $handler->addCall('cdata',array($match), $pos);
        }
      break;

      case DOKU_LEXER_MATCHED:
        if ( $match == ' ' ){
          $handler->addCall('cdata', array($match), $pos);
        } else if ( preg_match('/\t+/',$match) ) {
          $handler->addCall('table_align', array($match), $pos);
        } else if ( preg_match('/ {2,}/',$match) ) {
          $handler->addCall('table_align', array($match), $pos);
        } else if ( $match == "\n|" ) {
          $handler->addCall('table_row', array(), $pos);
          $handler->addCall('tablecell', array(), $pos);
        } else if ( $match == "\n|=" ) {
          $handler->addCall('table_row', array(), $pos);
          $handler->addCall('tableheader', array(), $pos);
        } else if ( $match == '|' ) {
          $handler->addCall('tablecell', array(), $pos);
        } else if ( $match == '|=' ) {
          $handler->addCall('tableheader', array(), $pos);
        }
      break;
    }
    return true;
  }
  
  function render($mode, Doku_Renderer $renderer, $data){
    return true;
  }
}
