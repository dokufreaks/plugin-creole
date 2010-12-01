<?php
/**
 * Creole Plugin, header component: Creole style headers
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_header extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2010-12-01',
                'name'   => 'Creole Plugin, header component',
                'desc'   => 'Creole style headers',
                'url'    => 'http://wiki.splitbrain.org/plugin:creole',
                );
    }

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
    }

    function handle($match, $state, $pos, &$handler) {
        global $conf;

        // get level and title
        $title = trim($match);
        if (($this->getConf('precedence') == 'dokuwiki')
                && ($title{strlen($title) - 1} == '=')) { // DokuWiki
            $level = 7 - strspn($title, '=');
        } else {                                   // Creole
            $level = strspn($title, '=');
        }
        if ($level < 1) $level = 1;
        elseif ($level > 5) $level = 5;
        $title = trim($title, '=');
        $title = trim($title);

        if ($handler->status['section']) $handler->_addCall('section_close', array(), $pos);

        $handler->_addCall('header', array($title, $level, $pos), $pos);

        $handler->_addCall('section_open', array($level), $pos);
        $handler->status['section'] = true;
        return true;
    }

    function render($mode, &$renderer, $data) {
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
