<?php
/**
 * Creole Plugin, preformatted block component: Creole style preformatted text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_preblock extends DokuWiki_Syntax_Plugin {

    function getType() { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort() { return 101; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
                '\n\{\{\{\n(?=.*?\n\}\}\}\n)',
                $mode,
                'plugin_creole_preblock'
                );
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
                '\n\}\}\}\n',
                'plugin_creole_preblock'
                );
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        if ($state == DOKU_LEXER_UNMATCHED) {
            $handler->addCall('preformatted', array($match), $pos);
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
