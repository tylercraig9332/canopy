<?php

namespace phpws2;


/**
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @package phpws2
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class Body extends \Canopy\Data {
    /**
     * Array of sessions for display
     * @var array
     */
    /**
     * Constants that define the default location a content element should be
     * displayed in a theme.
     */

    const Body = 1;
    const Sub = 2;
    const Header = 3;
    const Footer = 4;

    private static $sections = null;
    private static $theme = 'bootstrap';

    public static function add($content, $key = 'default', $section_default = self::Body)
    {
        if (empty($content)) {
            throw new \Exception('Body:add expects a non-empty string.');
        }
        $key = md5($key);
        self::$sections[$section_default][$key][] = $content;
    }

    public static function show()
    {
        if (!empty(self::$sections)) {
            // @todo return null or error page? For now, returning a blank page
            foreach (self::$sections as $section_number => $content_array) {
                foreach ($content_array as $content) {
                    ${"section$section_number"} = implode('', $content);
                }
            }
        }
        $head = \Head::singleton();
        // must run after above
        $headers = $head->getHeadArray();
        extract($headers);
        include self::$theme;
    }

    public static function addJavascript($js)
    {
        $head = \Head::singleton();
        $head->addJavascript($js);
    }

}
