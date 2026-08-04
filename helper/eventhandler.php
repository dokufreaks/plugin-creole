<?php

use dokuwiki\Extension\Plugin;
use dokuwiki\plugin\creole\StateCallback;
use dokuwiki\plugin\creole\SyntaxEvent;

/**
 * Creole Helper Plugin, Eventhandler:
 * Notifies syntax components if a other component has match.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */
class helper_plugin_creole_eventhandler extends Plugin
{
    protected static $queue = [];
    protected static $callbacks = [];
    protected static $trace_dump = 'syntax_plugin_creole_base - Trace-Dump:<br/>';

    /**
     * Constructor.
     */
    public function __construct()
    {
        self::$queue = [];
        self::$callbacks = [];
        self::$trace_dump = 'syntax_plugin_creole_base - Trace-Dump:<br/>';
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return [[
                'name'   => 'getColorValue',
                'desc'   => 'returns the color value for a given CSS color name. '
                            . 'Returns "#000000" if the name is unknown',
                'params' => ['name' => 'string'],
                'return' => ['color value' => 'string'],
                ]];
    }

    /**
     * Register a callback that fires when $state/$clazz/$tag is announced while
     * an event matching $ownState/$ownClazz/$ownTag is queued.
     *
     * NULL matches anything for every one of these parameters.
     *
     * @param string|null $state    state of the event to listen for
     * @param string|null $clazz    component name of the event to listen for
     * @param string|null $tag      tag of the event to listen for
     * @param string|null $ownState state of the own queued event
     * @param string|null $ownClazz component name of the own queued event
     * @param string|null $ownTag   tag of the own queued event
     * @param callable    $callback function to call on a match
     * @return void
     */
    public function addOnNotify($state, $clazz, $tag, $ownState, $ownClazz, $ownTag, $callback)
    {
        self::$callbacks [] = new StateCallback(
            $state,
            $clazz,
            $tag,
            $ownState,
            $ownClazz,
            $ownTag,
            $callback
        );
    }

    /**
     * Announce a syntax match and call every callback registered for it.
     *
     * An 'open' event is pushed onto the queue, a 'close' event removes the
     * matching 'open' event from it.
     *
     * @param string      $state   event state, e.g. 'open', 'close', 'found', 'insert'
     * @param string      $clazz   name of the announcing syntax component
     * @param string|null $tag     optional sub-type of the event
     * @param int         $pos     byte position of the match in the padded document
     * @param string      $match   matched syntax
     * @param mixed       $handler the parser handler
     * @return void
     */
    public function notifyEvent($state, $clazz, $tag, $pos, $match, $handler)
    {
        $state = strtolower($state);
        $clazz = strtolower($clazz);
        $tag = strtolower($tag);

        $event = new SyntaxEvent($state, $clazz, $tag);

        // Callback all functions registered for this event,
        // if they have got an event in the queue.
        $q_max = count(self::$queue);
        for ($q_index = $q_max - 1; $q_index >= 0; $q_index--) {
            $counter = count(self::$callbacks);
            for ($cb_index = 0; $cb_index < $counter; $cb_index++) {
                if (
                    self::$queue[$q_index] != null &&
                     self::$callbacks [$cb_index]->eventMatches($event) &&
                     self::$callbacks [$cb_index]->ownEventMatches(self::$queue[$q_index])
                ) {
                    // Match found, call callback function.
                    self::$callbacks [$cb_index]->execute(self::$queue [$q_index], $pos, $match, $handler);
                }
            }
        }

        if ($state == 'open') {
            self::$queue [] = $event;
        }
        if ($state == 'close') {
            // Remove an matching open event from the queue.
            // Change state of this event to open and search for it.
            $event->setState('open');
            $counter = count(self::$queue);
            for ($q_index = 0; $q_index < $counter; $q_index++) {
                if ($event->equal(self::$queue [$q_index])) {
                    self::$queue [$q_index] = null;
                }
            }
        }
    }

    /**
     * Check if a matching event is currently queued.
     *
     * NULL matches anything for every one of these parameters.
     *
     * @param string|null $state event state to look for
     * @param string|null $clazz component name to look for
     * @param string|null $tag   event tag to look for
     * @return bool
     */
    public function queuedEventExists($state, $clazz, $tag)
    {
        $counter = count(self::$queue);
        for ($q_index = 0; $q_index < $counter; $q_index++) {
            if (
                self::$queue [$q_index] != null &&
                 self::$queue [$q_index]->equalToValues($state, $clazz, $tag)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * helper function to simplify writing plugin calls to the instruction list
     * first three arguments are passed to function render as $data
     *
     * @param string $class   plugin component name without the syntax_plugin_ prefix
     * @param mixed  $state   lexer state to pass to the component's render()
     * @param mixed  $data1   first payload for render()
     * @param mixed  $data2   second payload for render()
     * @param int    $pos     byte position of the match in the padded document
     * @param string $match   matched syntax
     * @param mixed  $handler the parser handler
     * @return void
     */
    public function writeCall($class, $state, $data1, $data2, $pos, $match, &$handler)
    {
        $handler->addPluginCall($class, [$state, $data1, $data2], $state, $pos, $match);
    }

    /**
     * Get the collected trace output.
     *
     * @return string
     */
    public function getTraceDump()
    {
        return self::$trace_dump;
    }

    /**
     * Append to the collected trace output.
     *
     * @param string $content trace message
     * @return void
     */
    public function addToTraceDump($content)
    {
        self::$trace_dump .= $content;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
