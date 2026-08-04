<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

/**
 * Creole Plugin, header component: Creole style headers
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_header extends SyntaxPlugin
{
    /** @var helper_plugin_creole_eventhandler|null notifies sibling components of matches */
    public ?helper_plugin_creole_eventhandler $eventhandler = null;

    public function getType()
    {
        return 'container';
    }
    public function getPType()
    {
        return 'block';
    }
    public function getSort()
    {
        return 49;
    }

    public function getAllowedTypes()
    {
        return ['formatting', 'substition', 'disabled', 'protected'];
    }

    public function preConnect()
    {
        // The terminating \n is deliberately left out of the match: the
        // listblock, table and preblock entry patterns all need it to start a
        // block on the line right below a heading. syntax_plugin_creole_linebreak
        // skips it instead - see the 'creole_header_eol' status set in handle().
        $this->Lexer->addSpecialPattern(
            '(?m)^[ \t]*=+[^\n]+=*[ \t]*$',
            'base',
            'plugin_creole_header'
        );
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        global $conf;

        // get level and title
        $title = trim($match);
        if (
            ($this->getConf('precedence') == 'dokuwiki')
                && ($title[strlen($title) - 1] == '=')
        ) { // DokuWiki
            $level = 7 - strspn($title, '=');
        } else {                                   // Creole
            $level = strspn($title, '=');
        }
        if ($level < 1) $level = 1;
        elseif ($level > 5) $level = 5;
        $title = trim($title, '=');
        $title = trim($title);

        $this->eventhandler->notifyEvent('insert', 'header', 'header', $pos, $match, $handler);

        // Remember where this heading's line terminator sits so the linebreak
        // component can tell it apart from a line break inside inline content.
        $handler->setStatus('creole_header_eol', $pos + strlen($match));

        if ($handler->getStatus('section')) $handler->addCall('section_close', [], $pos);

        $handler->addCall('header', [$title, $level, $pos], $pos);

        $handler->addCall('section_open', [$level], $pos);
        $handler->setStatus('section', true) ;
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
