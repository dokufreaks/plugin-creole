<?php

/**
 * Creole Plugin, preformatted block component: Creole style preformatted text
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;
use dokuwiki\Parsing\Handler\Table;

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_creole_table extends SyntaxPlugin
{
    public $eventhandler;
    public function getType()
    {
        return 'container';
    }
    public function getSort()
    {
        return 59;
    }

    public function getAllowedTypes()
    {
        return ['formatting', 'substition', 'disabled', 'protected'];
    }

    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern('\n\|=', $mode, 'plugin_creole_table');
        $this->Lexer->addEntryPattern('\n\|', $mode, 'plugin_creole_table');
    }

    public function postConnect()
    {
        $this->Lexer->addPattern('\n\|=', 'plugin_creole_table');
        $this->Lexer->addPattern('\n\|', 'plugin_creole_table');
        $this->Lexer->addPattern('[\t ]+', 'plugin_creole_table');
        $this->Lexer->addPattern('\|=', 'plugin_creole_table');
        $this->Lexer->addPattern('\|', 'plugin_creole_table');
        $this->Lexer->addExitPattern('\n', 'plugin_creole_table');
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
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->eventhandler->notifyEvent('open', 'table', null, $pos, $match, $handler);
                    $ReWriter = new Table($handler->getCallWriter());
                    $handler->setCallWriter($ReWriter);
                    $handler->addCall('table_start', [], $pos);
                if (trim($match) == '|=') {
                    $handler->addCall('tableheader', [], $pos);
                } else {
                    $handler->addCall('tablecell', [], $pos);
                }

                break;
            case DOKU_LEXER_EXIT:
                $this->eventhandler->notifyEvent('close', 'table', null, $pos, $match, $handler);
                  $handler->addCall('table_end', [], $pos);
                  $handler->getCallWriter()->process();
                  $ReWriter = $handler->getCallWriter();
                  $handler->setCallWriter($ReWriter->getCallWriter());

                break;
            case DOKU_LEXER_UNMATCHED:
                if (trim($match) != '') {
                    $handler->addCall('cdata', [$match], $pos);
                }

                break;

            case DOKU_LEXER_MATCHED:
                if ($match == ' ') {
                    $handler->addCall('cdata', [$match], $pos);
                } elseif (preg_match('/\t+/', $match)) {
                    $handler->addCall('table_align', [$match], $pos);
                } elseif (preg_match('/ {2,}/', $match)) {
                    $handler->addCall('table_align', [$match], $pos);
                } elseif ($match == "\n|") {
                    $handler->addCall('table_row', [], $pos);
                    $handler->addCall('tablecell', [], $pos);
                } elseif ($match == "\n|=") {
                    $handler->addCall('table_row', [], $pos);
                    $handler->addCall('tableheader', [], $pos);
                } elseif ($match == '|') {
                    $handler->addCall('tablecell', [], $pos);
                } elseif ($match == '|=') {
                    $handler->addCall('tableheader', [], $pos);
                }
                break;
        }
        return true;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        return true;
    }
}
