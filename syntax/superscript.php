<?php
/**
 * Creole Plugin, superscript component: Creole style superscripted text
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
class syntax_plugin_creole_superscript extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner, LarsDW223',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-04',
                'name'   => 'Creole Plugin, superscript component',
                'desc'   => 'Creole style superscripted text',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
                '\^\^(?=.*?\^\^)',
                $mode,
                'plugin_creole_superscript'
                );
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
                '\^\^',
                'plugin_creole_superscript'
                );
    }

    function handle($match, $state, $pos, &$handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $handler->_addCall('superscript_open', array(), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                //$handler->_addCall('unformatted', array($match), $pos);
                $handler->_addCall('cdata', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $handler->_addCall('superscript_close', array(), $pos);
                break;
        }
        return true;
    }

    function render($mode, &$renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
