<?php

/**
 * Creole Plugin, listblock component: Creole style ordered and unordered lists
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;
use dokuwiki\plugin\creole\ListRewriter;

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_listblock extends SyntaxPlugin
{
    public $eventhandler;

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
        return 9;
    }

    public function getAllowedTypes()
    {
        return ['formatting', 'substition', 'disabled', 'protected'];
    }

    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern(
            '\n[ \t]*[\#\*](?!\*)',
            $mode,
            'plugin_creole_listblock'
        );
        $this->Lexer->addPattern(
            '\n[ \t]*[\#\*\-]+',
            'plugin_creole_listblock'
        );
    }

    public function postConnect()
    {
        $this->Lexer->addExitPattern(
            '\n',
            'plugin_creole_listblock'
        );
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
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'list', null, $pos, $match, $handler);
                $ReWriter = new ListRewriter($handler->getCallWriter());
                $handler->setCallWriter($ReWriter);
                $handler->addCall('list_open', [$match], $pos);
                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'list', null, $pos, $match, $handler);
                $handler->addCall('list_close', [], $pos);
                $ReWriter = $handler->getCallWriter();
                $ReWriter->process();
                $handler->setCallWriter($ReWriter->getCallWriter());
                break;
            case DOKU_LEXER_MATCHED:
                $handler->addCall('list_item', [$match], $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', [$match], $pos);
                break;
        }
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
