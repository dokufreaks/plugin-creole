<?php
/**
 * Creole Plugin, emptyline component: notifies
 * other creole syntax components that an empty line was detected.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_emptyline extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getInfo() {
        return array(
                'author' => 'Gina HÃ¤uÃge, Michael Klier, Christopher Smith',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-30',
                'name'   => 'Creole Plugin (emptyline component)',
                'desc'   => 'Provide a notification if an empty line is detected.',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

    function getType() { return 'substition'; }
    function getPType() { return 'block'; }
    function getSort() { return 99; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern(
                '\n(?=\n)',
                $mode,
                'plugin_creole_emptyline'
                );
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        if ( $state == DOKU_LEXER_SPECIAL  ) {
            $this->eventhandler->notifyEvent('found', 'emptyline', NULL, $pos, $match, $handler);
            return true;
        }
        return false;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        /*if($mode == 'xhtml') {
            if ($data) {
                if ( $this->getConf('linebreak') == 'Linebreak' ) {
                    $renderer->doc .= "<br /><br />";
                } else {
                    $renderer->doc .= " ";
                }
            }
            return true;
        }*/
        return false;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
