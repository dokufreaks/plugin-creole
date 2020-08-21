<?php
/**
 * Creole Plugin, monospace component: Creole style monospaced text
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
class syntax_plugin_creole_monospace extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern(
                '##',
                $mode,
                'plugin_creole_monospace'
                ); 
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
        $this->eventhandler->addOnNotify('insert', 'header', 'header',
                                         'open', 'monospace', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('found', 'emptyline', NULL,
                                         'open', 'monospace', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'list', NULL,
                                         'open', 'monospace', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'table', NULL,
                                         'open', 'monospace', NULL,
                                         array($this, 'onHeaderCallback'));
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        global $conf;

        if ( $this->eventhandler->queuedEventExists ('open', 'monospace', NULL) == false ) {
            $state = DOKU_LEXER_ENTER;
        } else {
            $state = DOKU_LEXER_EXIT;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                if ( $this->getConf('monospace') == 'DokuWiki' ) {
                    $this->eventhandler->notifyEvent('open', 'monospace', 'dw-monospace', $pos, $match, $handler);
                    $handler->addCall('monospace_open', array(), $pos);
                } else {
                    $this->eventhandler->notifyEvent('open', 'monospace', 'creole-monospace', $pos, $match, $handler);
                    return array($state);
                }
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                if ( $this->getConf('monospace') == 'DokuWiki' ) {
                    $this->eventhandler->notifyEvent('close', 'monospace', 'dw-monospace', $pos, $match, $handler);
                    $handler->addCall('monospace_close', array(), $pos);
                } else {
                    $this->eventhandler->notifyEvent('close', 'monospace', 'creole-monospace', $pos, $match, $handler);
                    return array($state);
                }
                break;            
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        list($state) = $data;
        switch ($state) {
            case DOKU_LEXER_ENTER :
                $renderer->doc .= '<tt>';
                break;
            case DOKU_LEXER_EXIT :
                $renderer->doc .= '</tt>';
                break;
        }
        return true;
    }

    public function onHeaderCallback (creole_syntax_event $myEvent, $pos, $match, $handler) {
        $this->eventhandler->notifyEvent('close', 'monospace', 'dw-monospace', $pos, $match, $handler);
        switch ($myEvent->getTag() == 'dw-monospace') {
            case 'dw-monospace':
                $handler->addCall('monospace_close', array(), $pos);
                break;
            case 'creole-monospace':
                $this->eventhandler->writeCall('creole_monospace', DOKU_LEXER_EXIT, NULL, NULL, $pos, $match, $handler);
                break;
        }
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
