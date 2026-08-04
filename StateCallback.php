<?php

namespace dokuwiki\plugin\creole;

/**
 * A callback registered for a syntax event.
 *
 * A callback fires when the announced event matches $event *and* the
 * registering component has an event matching $ownEvent queued. This is how a
 * block construct (heading, blank line, list, table) forces still-open inline
 * markup closed.
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  LarsDW223
 */
class StateCallback
{
    /** @var SyntaxEvent|null event to listen for, NULL matches any event */
    protected $event;

    /** @var SyntaxEvent|null own event that must be queued, NULL matches any */
    protected $ownEvent;

    /** @var callable function to call when both events match */
    protected $callback;

    /**
     * Constructor.
     *
     * @param string|null $state    state of the event to listen for
     * @param string|null $clazz    component name of the event to listen for
     * @param string|null $tag      tag of the event to listen for
     * @param string|null $ownState state of the own queued event
     * @param string|null $ownClazz component name of the own queued event
     * @param string|null $ownTag   tag of the own queued event
     * @param callable    $callback function to call on a match
     */
    public function __construct($state, $clazz, $tag, $ownState, $ownClazz, $ownTag, $callback)
    {
        if ($state == null && $clazz == null && $tag == null) {
            $this->event = null;
        } else {
            $this->event = new SyntaxEvent($state, $clazz, $tag);
        }
        if ($ownState == null && $ownClazz == null && $ownTag == null) {
            $this->ownEvent = null;
        } else {
            $this->ownEvent = new SyntaxEvent($ownState, $ownClazz, $ownTag);
        }
        $this->callback = $callback;
    }

    /**
     * Check if the announced event matches the one this callback listens for.
     *
     * @param SyntaxEvent $compare announced event
     * @return bool
     */
    public function eventMatches(SyntaxEvent $compare)
    {
        return $this->event->equal($compare);
    }

    /**
     * Check if the given queued event matches this callback's own event.
     *
     * @param SyntaxEvent $compare queued event
     * @return bool
     */
    public function ownEventMatches(SyntaxEvent $compare)
    {
        return $this->ownEvent->equal($compare);
    }

    /**
     * Call the registered callback function.
     *
     * @param SyntaxEvent $queuedEvent the matched queued event
     * @param int         $pos         byte position of the match in the padded document
     * @param string      $match       matched syntax
     * @param mixed       $handler     the parser handler
     * @return void
     */
    public function execute($queuedEvent, $pos, $match, &$handler)
    {
        call_user_func($this->callback, $queuedEvent, $pos, $match, $handler);
    }

    /**
     * Get the own event this callback requires to be queued.
     *
     * @return SyntaxEvent|null
     */
    public function getOwnEvent()
    {
        return $this->ownEvent;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
