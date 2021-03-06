<?php
/**
 * Creole Plugin, underline component: Creole style underline text
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_underline extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner, LarsDW223',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-29',
                'name'   => 'Creole Plugin, underline component',
                'desc'   => 'Creole style underline text',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

    function getType() { return 'protected'; }
    function getSort() { return 9; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern(
                '__',
                $mode,
                'plugin_creole_underline'
                ); 
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
        $this->eventhandler->addOnNotify('insert', 'header', 'header',
                                         'open', 'underline', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('found', 'emptyline', NULL,
                                         'open', 'underline', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'list', NULL,
                                         'open', 'underline', NULL,
                                         array($this, 'onHeaderCallback'));
        $this->eventhandler->addOnNotify('open', 'table', NULL,
                                         'open', 'underline', NULL,
                                         array($this, 'onHeaderCallback'));
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        if ( $this->eventhandler->queuedEventExists ('open', 'underline', NULL) == false ) {
            $state = DOKU_LEXER_ENTER;
        } else {
            $state = DOKU_LEXER_EXIT;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'underline', NULL, $pos, $match, $handler);
                $handler->addCall('underline_open', array(), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'underline', NULL, $pos, $match, $handler);
                $handler->addCall('underline_close', array(), $pos);
                break;
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }

    public function onHeaderCallback (creole_syntax_event $myEvent, $pos, $match, $handler) {
        $this->eventhandler->notifyEvent('close', 'underline', NULL, $pos, $match, $handler);
        $handler->addCall('underline_close', array(), $pos);
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
