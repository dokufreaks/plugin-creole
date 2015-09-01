<?php
/**
 * Creole Plugin, subscript component: Creole style subscripted text
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
class syntax_plugin_creole_subscript extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner, LarsDW223',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-04',
                'name'   => 'Creole Plugin, subscript component',
                'desc'   => 'Creole style subscripted text',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

    function getType() { return 'protected'; }
    function getSort() { return 102; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern(
                ',,',
                $mode,
                'plugin_creole_subscript'
                ); 
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
        $this->eventhandler->addOnNotify('insert', 'header', 'header',
                                         'open', 'subscript', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('found', 'emptyline', NULL,
                                         'open', 'subscript', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'list', NULL,
                                         'open', 'subscript', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'table', NULL,
                                         'open', 'subscript', NULL,
                                         array($this, 'onHeaderCallback'));
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        if ( $this->eventhandler->queuedEventExists ('open', 'subscript', NULL) == false ) {
            $state = DOKU_LEXER_ENTER;
        } else {
            $state = DOKU_LEXER_EXIT;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'subscript', NULL, $pos, $match, $handler);
                $handler->_addCall('subscript_open', array(), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->_addCall('cdata', array($match), $pos);
                //$handler->_addCall('unformatted', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'subscript', NULL, $pos, $match, $handler);
                $handler->_addCall('subscript_close', array(), $pos);
                break;
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }

    public function onHeaderCallback (creole_syntax_event $myEvent, $pos, $match, $handler) {
        $this->eventhandler->notifyEvent('close', 'subscript', NULL, $pos, $match, $handler);
        $handler->_addCall('subscript_close', array(), $pos);
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
