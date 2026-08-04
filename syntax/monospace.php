<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;
use dokuwiki\plugin\creole\SyntaxEvent;

/**
 * Creole Plugin, monospace component: Creole style monospaced text
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_monospace extends SyntaxPlugin
{
    /** @var helper_plugin_creole_eventhandler|null notifies sibling components of matches */
    public ?helper_plugin_creole_eventhandler $eventhandler = null;

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
        $this->Lexer->addSpecialPattern(
            '##',
            $mode,
            'plugin_creole_monospace'
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
            'monospace',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'found',
            'emptyline',
            null,
            'open',
            'monospace',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'open',
            'list',
            null,
            'open',
            'monospace',
            null,
            $this->onHeaderCallback(...)
        );
        $this->eventhandler->addOnNotify(
            'open',
            'table',
            null,
            'open',
            'monospace',
            null,
            $this->onHeaderCallback(...)
        );
    }

    public function handle($match, $state, $pos, Handler $handler)
    {
        global $conf;

        if ($this->eventhandler->queuedEventExists('open', 'monospace', null) == false) {
            $state = DOKU_LEXER_ENTER;
        } else {
            $state = DOKU_LEXER_EXIT;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                if ($this->getConf('monospace') == 'DokuWiki') {
                    $this->eventhandler->notifyEvent('open', 'monospace', 'dw-monospace', $pos, $match, $handler);
                    $handler->addCall('monospace_open', [], $pos);
                } else {
                    $this->eventhandler->notifyEvent('open', 'monospace', 'creole-monospace', $pos, $match, $handler);
                    return [$state];
                }
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', [$match], $pos);
                break;
            case DOKU_LEXER_EXIT:
                if ($this->getConf('monospace') == 'DokuWiki') {
                    $this->eventhandler->notifyEvent('close', 'monospace', 'dw-monospace', $pos, $match, $handler);
                    $handler->addCall('monospace_close', [], $pos);
                } else {
                    $this->eventhandler->notifyEvent('close', 'monospace', 'creole-monospace', $pos, $match, $handler);
                    return [$state];
                }
                break;
        }
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        [$state] = $data;
        match ($state) {
            DOKU_LEXER_ENTER => $renderer->doc .= '<tt>',
            DOKU_LEXER_EXIT => $renderer->doc .= '</tt>',
            default => true,
        };
        return true;
    }

    public function onHeaderCallback(SyntaxEvent $myEvent, $pos, $match, $handler)
    {
        $this->eventhandler->notifyEvent('close', 'monospace', 'dw-monospace', $pos, $match, $handler);
        switch ($myEvent->getTag() == 'dw-monospace') {
            case 'dw-monospace':
                $handler->addCall('monospace_close', [], $pos);
                break;
            case 'creole-monospace':
                $this->eventhandler->writeCall('creole_monospace', DOKU_LEXER_EXIT, null, null, $pos, $match, $handler);
                break;
        }
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
