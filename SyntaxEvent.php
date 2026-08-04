<?php

namespace dokuwiki\plugin\creole;

/**
 * A single syntax event as announced by a Creole syntax component.
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  LarsDW223
 */
class SyntaxEvent
{
    /** @var string|null event state, e.g. 'open', 'close', 'found', 'insert' */
    protected $state;

    /** @var string|null name of the announcing syntax component, e.g. 'strong' */
    protected $clazz;

    /** @var string|null optional sub-type of the event, e.g. 'dw-monospace' */
    protected $tag;

    /**
     * Constructor.
     *
     * @param string|null $state event state
     * @param string|null $clazz component name
     * @param string|null $tag   event tag
     */
    public function __construct($state, $clazz, $tag)
    {
        $this->state = $state;
        $this->clazz = $clazz;
        $this->tag = $tag;
    }

    /**
     * Set the event state.
     *
     * @param string|null $state new state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get the event state.
     *
     * @return string|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the name of the announcing syntax component.
     *
     * @return string|null
     */
    public function getClazz()
    {
        return $this->clazz;
    }

    /**
     * Get the event tag.
     *
     * @return string|null
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Check if the given event matches this one.
     *
     * @param SyntaxEvent|null $other event to compare with
     * @return bool true if all non-NULL members are equal
     */
    public function equal($other)
    {
        if ($other == null) {
            return false;
        }
        $otherState = $other->getState();
        $otherClazz = $other->getClazz();
        $otherTag = $other->getTag();
        return $this->equalToValues($otherState, $otherClazz, $otherTag);
    }

    /**
     * Check if the given values match this event.
     *
     * @param string|null $otherState state to compare with
     * @param string|null $otherClazz component name to compare with
     * @param string|null $otherTag   tag to compare with
     * @return bool true if all non-NULL members are equal
     */
    public function equalToValues($otherState, $otherClazz, $otherTag)
    {
        if (
            $this->state != null && $otherState != null &&
             $this->state != $otherState
        ) {
            return false;
        }
        if (
            $this->clazz != null && $otherClazz != null &&
             $this->clazz != $otherClazz
        ) {
            return false;
        }
        if (
            $this->tag != null && $otherTag != null &&
             $this->tag != $otherTag
        ) {
            return false;
        }
        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
