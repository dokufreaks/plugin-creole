<?php
/**
 * Creole Plugin, subscript component: Creole style subscripted text
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
class syntax_plugin_creole_subscript extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner, LarsDW223',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-04',
                'name'   => 'Creole Plugin, subscript component',
                'desc'   => 'Creole style subscripted text',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
                ',,(?=.*?,,)',
                $mode,
                'plugin_creole_subscript'
                );
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
                ',,',
                'plugin_creole_subscript'
                );
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $handler->_addCall('subscript_open', array(), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->_addCall('cdata', array($match), $pos);
                //$handler->_addCall('unformatted', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $handler->_addCall('subscript_close', array(), $pos);
                break;
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
