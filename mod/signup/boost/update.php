<?php
/**
 * @version $Id$
 * @author Matthew McNaney <mcnaney at gmail dot com>
 */

function signup_update(&$content, $currentVersion)
{
    switch ($currentVersion) {
    case version_compare($currentVersion, '1.0.1', '<'):
        $content[] = '<pre>';

        $files = array('templates/slot_setup.tpl');
        signupUpdateFiles($files, $content);
        
        $content[] = '1.0.1 changes
----------------
+ Added ability to reset slot order should it come unraveled.
+ Fixed reroute link that was hard coded to go to sheet id 1.</pre>';
    }
    return true;
}

function signupUpdateFiles($files, &$content)
{
    if (PHPWS_Boost::updateFiles($files, 'signup')) {
        $content[] = '--- Updated the following files:';
    } else {
        $content[] = '--- Unable to update the following files:';
    }
    $content[] = "    " . implode("\n    ", $files);
}


?>