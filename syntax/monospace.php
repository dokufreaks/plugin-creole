<?php
/**
 * Creole Plugin, monospace component: Creole style monospaced text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_monospace extends DokuWiki_Syntax_Plugin {

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
                '##(?=.*?##)',
                $mode,
                'plugin_creole_monospace'
                );
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
                '##',
                'plugin_creole_monospace'
                );
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        global $conf;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                if ( $this->getConf('monospace') == 'DokuWiki' ) {
                    $handler->_addCall('monospace_open', array(), $pos);
                } else {
                    return array($state);
                }
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->_addCall('cdata', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                if ( $this->getConf('monospace') == 'DokuWiki' ) {
                    $handler->_addCall('monospace_close', array(), $pos);
                } else {
                    return array($state);
                }
                break;            
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        list($state) = $data;
        switch ($state) {
            case DOKU_LEXER_ENTER :
                $renderer->doc .= '<tt>';
                break;
            case DOKU_LEXER_EXIT :
                $renderer->doc .= '</tt>';
                break;
        }
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
