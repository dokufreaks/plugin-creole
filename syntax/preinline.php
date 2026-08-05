<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

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
class syntax_plugin_creole_preinline extends SyntaxPlugin
{
    public function getType()
    {
        return 'protected';
    }
    public function getSort()
    {
        return 102;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern(
            '\{\{\{(?=.*?\}\}\})',
            $mode,
            'plugin_creole_preinline'
        );
    }

    public function postConnect()
    {
        $this->Lexer->addExitPattern(
            '\}\}\}',
            'plugin_creole_preinline'
        );
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        match ($state) {
            DOKU_LEXER_ENTER => $handler->addCall('monospace_open', [], $pos),
            DOKU_LEXER_UNMATCHED => $handler->addCall('unformatted', [$match], $pos),
            DOKU_LEXER_EXIT => $handler->addCall('monospace_close', [], $pos),
            default => true,
        };
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
