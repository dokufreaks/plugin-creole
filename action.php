<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

class action_plugin_creole extends ActionPlugin
{
    /**
     * register the eventhandlers
     */
    public function register(EventHandler $controller)
    {
        $controller->register_hook(
            'TOOLBAR_DEFINE',
            'AFTER',
            $this,
            'defineToolbar',
            []
        );
    }

    /**
     * modifiy the toolbar JS defines
     *
     * @param \dokuwiki\Extension\Event $event TOOLBAR_DEFINE event, its data is the toolbar definition
     * @param mixed $param the empty parameter array passed on registration
     * @return bool false if the toolbar was left untouched, true otherwise
     * @author  Esther Brunner  <wikidesign@gmail.com>
     */
    public function defineToolbar(&$event, $param)
    {
        // return false;
        if ($this->getConf('precedence') != 'creole') return false; // leave untouched

        $c = count($event->data);
        for ($i = 0; $i < $c; $i++) {
            if ($event->data[$i]['type'] == 'format') {
                // headers
                if (preg_match("/h(\d)\.png/", $event->data[$i]['icon'], $match)) {
                    $markup = substr('======', 0, $match[1]);
                    $event->data[$i]['open']  = $markup . " ";
                    $event->data[$i]['close'] = " " . $markup . "\\n";

                    // ordered lists
                } elseif ($event->data[$i]['icon'] == 'ol.png') {
                    $event->data[$i]['open']  = "# ";

                    // unordered lists
                } elseif ($event->data[$i]['icon'] == 'ul.png') {
                    $event->data[$i]['open']  = "* ";
                }
            }
        }

        return true;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
