<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

class action_plugin_creole extends DokuWiki_Action_Plugin {

    /**
     * register the eventhandlers
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TOOLBAR_DEFINE',
                'AFTER',
                $this,
                'define_toolbar',
                array());
    }

    /**
     * modifiy the toolbar JS defines
     *
     * @author  Esther Brunner  <wikidesign@gmail.com>
     */
    function define_toolbar(&$event, $param) {
        // return false;  
        if ($this->getConf('precedence') != 'creole') return false; // leave untouched

        $c = count($event->data);
        for ($i = 0; $i <= $c; $i++) {
            if ($event->data[$i]['type'] == 'format') {

                // headers
                if (preg_match("/h(\d)\.png/", $event->data[$i]['icon'], $match)) {
                    $markup = substr('======', 0, $match[1]);
                    $event->data[$i]['open']  = $markup." ";
                    $event->data[$i]['close'] = " ".$markup."\\n";

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
