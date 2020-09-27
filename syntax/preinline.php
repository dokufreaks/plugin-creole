<?php
/**
 * Creole Plugin, inline preformatted component: Creole style preformatted text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_preinline extends DokuWiki_Syntax_Plugin {

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
                '\{\{\{(?=.*?\}\}\})',
                $mode,
                'plugin_creole_preinline'
                );
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
                '\}\}\}',
                'plugin_creole_preinline'
                );
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $handler->addCall('monospace_open', array(), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('unformatted', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $handler->addCall('monospace_close', array(), $pos);
                break;
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
