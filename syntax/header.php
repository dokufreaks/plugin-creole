<?php
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
class syntax_plugin_creole_header extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getType() { return 'container'; }
    function getPType() { return 'block'; }
    function getSort() { return 49; }

    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'protected');
    }

    function preConnect() {
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
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        global $conf;

        // get level and title
        $title = trim($match);
        if (($this->getConf('precedence') == 'dokuwiki')
                && ($title[strlen($title) - 1] == '=')) { // DokuWiki
            $level = 7 - strspn($title, '=');
        } else {                                   // Creole
            $level = strspn($title, '=');
        }
        if ($level < 1) $level = 1;
        elseif ($level > 5) $level = 5;
        $title = trim($title, '=');
        $title = trim($title);

        $this->eventhandler->notifyEvent('insert', 'header', 'header', $pos, $match, $handler);

        if ($handler->getStatus('section')) $handler->addCall('section_close', array(), $pos);

        $handler->addCall('header', array($title, $level, $pos), $pos);

        $handler->addCall('section_open', array($level), $pos);
        $handler->setStatus('section', true) ;
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
