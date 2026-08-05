<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

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
class syntax_plugin_creole_preblock extends SyntaxPlugin
{
    public function getType()
    {
        return 'protected';
    }
    public function getPType()
    {
        return 'block';
    }
    public function getSort()
    {
        return 101;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern(
            '\n\{\{\{\n(?=.*?\n\}\}\}\n)',
            $mode,
            'plugin_creole_preblock'
        );
    }

    public function postConnect()
    {
        $this->Lexer->addExitPattern(
            '\n\}\}\}\n',
            'plugin_creole_preblock'
        );
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        if ($state == DOKU_LEXER_UNMATCHED) {
            $handler->addCall('preformatted', [$match], $pos);
        }
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
