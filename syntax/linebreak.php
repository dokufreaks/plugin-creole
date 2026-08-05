<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

/**
 * Creole Plugin, linebreak component: Inserts a line break
 * based on Linebreak Plugin http://wiki.splitbrain.org/plugin:linebreak
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_linebreak extends SyntaxPlugin
{
    public function getType()
    {
        return 'substition';
    }
    public function getSort()
    {
        return 100;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern(
            '(?<!^|\n)\n(?!\n|>)',
            $mode,
            'plugin_creole_linebreak'
        );
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        if ($match != "\n") return false;

        // A heading occupies a whole line, so the newline closing it terminates
        // the heading rather than joining inline content. Emitting a line break
        // there would put a stray space (or <br />) at the top of the next
        // paragraph - and for a document that is nothing but a heading it would
        // wrap the break in an empty paragraph.
        if ($handler->getStatus('creole_header_eol') === $pos) return false;

        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            if ($data) {
                if ($this->getConf('linebreak') == 'Linebreak') {
                    $renderer->doc .= "<br />";
                } else {
                    $renderer->doc .= " ";
                }
            }
            return true;
        }
        return false;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
