<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

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
class syntax_plugin_creole_emptyline extends SyntaxPlugin
{
    /** @var helper_plugin_creole_eventhandler|null notifies sibling components of matches */
    public ?helper_plugin_creole_eventhandler $eventhandler = null;

    public function getInfo()
    {
        return [
                'author' => 'Gina Häußge, Michael Klier, Christopher Smith',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2015-08-30',
                'name'   => 'Creole Plugin (emptyline component)',
                'desc'   => 'Provide a notification if an empty line is detected.',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                ];
    }

    public function getType()
    {
        return 'substition';
    }
    public function getPType()
    {
        return 'block';
    }
    public function getSort()
    {
        return 99;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern(
            '\n(?=\n)',
            $mode,
            'plugin_creole_emptyline'
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
        if ($state == DOKU_LEXER_SPECIAL) {
            $this->eventhandler->notifyEvent('found', 'emptyline', null, $pos, $match, $handler);
            return true;
        }
        return false;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
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
