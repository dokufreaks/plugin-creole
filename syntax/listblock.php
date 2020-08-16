<?php
/**
 * Creole Plugin, listblock component: Creole style ordered and unordered lists
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

use dokuwiki\Parsing\Handler\Lists;

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_listblock extends DokuWiki_Syntax_Plugin {
    var $eventhandler = NULL;

    function getType() { return 'container'; }
    function getPType() { return 'block'; }
    function getSort() { return 9; }

    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'protected');
    }

    function connectTo($mode) {
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

    function postConnect() {
        $this->Lexer->addExitPattern(
                '\n',
                'plugin_creole_listblock'
                );
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventhandler = plugin_load('helper', 'creole_eventhandler');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'list', NULL, $pos, $match, $handler);
                $ReWriter = new Doku_Handler_Creole_List($handler->getCallWriter());
                $handler->setCallWriter($ReWriter);
                $handler->addCall('list_open', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'list', NULL, $pos, $match, $handler);
                $handler->addCall('list_close', array(), $pos);
                $ReWriter = & $handler->getCallWriter();
                $ReWriter->process();
                $handler->setCallWriter($ReWriter->getCallWriter());
                break;
            case DOKU_LEXER_MATCHED:
                $handler->addCall('list_item', array($match), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->addCall('cdata', array($match), $pos);
                break;
        }
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}

/* ----- Creole List Call Writer ----- */

class Doku_Handler_Creole_List extends Lists {

    function interpretSyntax($match, &$type) {
        if (substr($match,-1) == '*') $type = 'u';
        else $type = 'o';
        $level = strlen(trim($match));  // Creole
        if ($level <= 1) {
            $c = count(explode('  ',str_replace("\t",'  ',$match)));
            if ($c > $level) $level = $c; // DokuWiki
        }
        return $level;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
