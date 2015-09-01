<?php
/**
 * Creole Helper Plugin, Eventhandler:
 * Notifies syntax components if a other component has match.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class creole_syntax_event {
    protected $state = NULL;
    protected $clazz = NULL;
    protected $tag = NULL;
    
    /**
     * Constructor.
     */
    public function __construct($state, $clazz , $tag) {
        $this->state = $state;
        $this->clazz = $clazz;
        $this->tag = $tag;
    }

    public function setState($state) {
        $this->state = $state;
    }
    
    public function getState() {
        return $this->state;
    }

    public function getClazz() {
        return $this->clazz;
    }

    public function getTag() {
        return $this->tag;
    }
    
    public function equal ($other) {
        if ($other == NULL ) {
            return false;
        }
        $otherState = $other->getState();
        $otherClazz = $other->getClazz();
        $otherTag = $other->getTag();
        return $this->equalToValues ($otherState, $otherClazz, $otherTag);
    }

    public function equalToValues ($otherState, $otherClazz, $otherTag) {
        if ( $this->state != NULL && $otherState != NULL &&
             $this->state != $otherState ) {
            return false;
        }
        if ( $this->clazz != NULL && $otherClazz != NULL &&
             $this->clazz != $otherClazz ) {
            return false;
        }
        if ( $this->tag != NULL && $otherTag != NULL &&
             $this->tag != $otherTag ) {
            return false;
        }
        return true;
    }
}

class creole_state_callback {
    protected $event;
    protected $ownEvent;
    protected $callback;

    /**
     * Constructor.
     */
    public function __construct($state, $clazz, $tag, $ownState, $ownClazz, $ownTag, $callback) {
        if ( $state == NULL && $clazz == NULL && $tag == NULL ) {
            $this->event = NULL;
        } else {
            $this->event = new creole_syntax_event ($state, $clazz, $tag);
        }
        if ( $ownState == NULL && $ownClazz == NULL && $ownTag == NULL ) {
            $this->ownEvent = NULL;
        } else {
            $this->ownEvent = new creole_syntax_event ($ownState, $ownClazz, $ownTag);
        }
        $this->callback = $callback;
    }
    
    public function eventMatches (creole_syntax_event $compare) {
        return $this->event->equal($compare);
    }

    public function ownEventMatches (creole_syntax_event $compare) {
        return $this->ownEvent->equal($compare);
    }
    
    public function execute ($queuedEvent, $pos, $match, &$handler) {
        //call_user_func($this->callback, $this->ownEvent, $pos, $match, $handler);
        call_user_func($this->callback, $queuedEvent, $pos, $match, $handler);
    }

    public function getOwnEvent () {
        return $this->ownEvent;
    }
}

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class helper_plugin_creole_eventhandler extends DokuWiki_Plugin {
    protected static $queue = array();
    protected static $callbacks = array();
    protected static $trace_dump = 'syntax_plugin_creole_base - Trace-Dump:<br/>';

    /**
     * Constructor.
     */
    public function __construct() {
        self::$queue = array();
        self::$callbacks = array();
        self::$trace_dump = 'syntax_plugin_creole_base - Trace-Dump:<br/>';
    }

    /**
     * @return array
     */
    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'getColorValue',
                'desc'   => 'returns the color value for a given CSS color name. Returns "#000000" if the name is unknown',
                'params' => array('name' => 'string'),
                'return' => array('color value' => 'string'),
                );
        return $result;
    }

    public function addOnNotify ($state, $clazz, $tag, $ownState, $ownClazz, $ownTag, $callback) {
        self::$callbacks [] = new creole_state_callback
            ($state, $clazz, $tag, $ownState, $ownClazz, $ownTag, $callback);
    }

    public function notifyEvent ($state, $clazz, $tag, $pos, $match, $handler) {
        $state = strtolower($state);
        $clazz = strtolower($clazz);
        $tag = strtolower($tag);
        
        $event = new creole_syntax_event($state, $clazz, $tag);
        
        // Callback all functions registered for this event,
        // if they have got an event in the queue.
        $q_max = count(self::$queue);
        for ( $q_index = $q_max-1 ; $q_index >= 0 ; $q_index-- ) {
            for ( $cb_index = 0 ; $cb_index < count (self::$callbacks) ; $cb_index++ ) {
                if ( self::$queue[$q_index] != NULL &&
                     self::$callbacks [$cb_index]->eventMatches($event) &&
                     self::$callbacks [$cb_index]->ownEventMatches(self::$queue[$q_index]) ) {
                    // Match found, call callback function.
                    self::$callbacks [$cb_index]->execute(self::$queue [$q_index], $pos, $match, $handler);
                }
            }
        }
        
        if ( $state == 'open' ) {
            self::$queue [] = $event;
        }
        if ( $state == 'close' ) {
            // Remove an matching open event from the queue.
            // Change state of this event to open and search for it.
            $event->setState('open');
            for ( $q_index = 0 ; $q_index < count(self::$queue) ; $q_index++ ) {
                if ( $event->equal(self::$queue [$q_index]) ) {
                    self::$queue [$q_index] = NULL;
                }
            }
        }
    }

    public function queuedEventExists ($state, $clazz, $tag) {
        for ( $q_index = 0 ; $q_index < count(self::$queue) ; $q_index++ ) {
            if ( self::$queue [$q_index] != NULL &&
                 self::$queue [$q_index]->equalToValues($state, $clazz, $tag) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * helper function to simplify writing plugin calls to the instruction list
     * first three arguments are passed to function render as $data
     */
    public function writeCall($class, $state, $data1, $data2, $pos, $match, &$handler) {
        $handler->addPluginCall($class, array($state, $data1, $data2), $state, $pos, $match);
    }
    
    public function getTraceDump () {
        return self::$trace_dump;
    }
    
    public function addToTraceDump ($content) {
        self::$trace_dump .= $content;
    }    
}
// vim:ts=4:sw=4:et:enc=utf-8:
