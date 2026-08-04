<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;
use dokuwiki\plugin\creole\SyntaxEvent;

/**
 * Creole Plugin, emphasis component: Creole style emphasis text
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_emphasis extends SyntaxPlugin
{
    /** @var helper_plugin_creole_eventhandler|null notifies sibling components of matches */
    public ?helper_plugin_creole_eventhandler $eventhandler = null;

    public function getInfo()
    {
        return [
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner, LarsDW223',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-29',
                'name'   => 'Creole Plugin, emphasis component',
                'desc'   => 'Creole style emphasis text',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                ];
    }

    public function getType()
    {
        return 'protected';
    }

    public function getSort()
    {
        return 9;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern(
            '\/\/',
            $mode,
            'plugin_creole_emphasis'
        );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
        $this->eventhandler->addOnNotify(
            'insert',
            'header',
            'header',
            'open',
            'emphasis',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'found',
            'emptyline',
            null,
            'open',
            'emphasis',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'open',
            'list',
            null,
            'open',
            'emphasis',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'open',
            'table',
            null,
            'open',
            'emphasis',
            null,
            $this->onHeaderCallback(...)
        );
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        if ($this->eventhandler->queuedEventExists('open', 'emphasis', null) == false) {
            $state = DOKU_LEXER_ENTER;
        } else {
            $state = DOKU_LEXER_EXIT;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'emphasis', null, $pos, $match, $handler);
                $handler->addCall('emphasis_open', [], $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', [$match], $pos);
                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'emphasis', null, $pos, $match, $handler);
                $handler->addCall('emphasis_close', [], $pos);
                break;
        }
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }

    public function onHeaderCallback(SyntaxEvent $myEvent, $pos, $match, $handler)
    {
        $this->eventhandler->notifyEvent('close', 'emphasis', null, $pos, $match, $handler);
        $handler->addCall('emphasis_close', [], $pos);
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
