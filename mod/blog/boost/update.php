<?php

  /**
   * @author Matthew McNaney <mcnaney at gmail dot com>
   * @version $Id$
   */

function blog_update(&$content, $currentVersion)
{
    switch ($currentVersion) {
    case version_compare($currentVersion, '0.0.3', '<'):
        $result = blog_update_003();
        if (PEAR::isError($result)) {
            return $result;
        }
        $content[] = '+ added the author column';
        break;

    case version_compare($currentVersion, '0.0.4', '<'):
        $result = blog_update_004($content);
        if (PEAR::isError($result)) {
            return $result;
        }
        $content[] = '+ registered to rss';
        break;

    case version_compare($currentVersion, '0.0.5', '<'):
        $result = blog_update_005($content);
        if (PEAR::isError($result)) {
            return $result;
        }
        $content[] = '+ changed date column to create_date';
        $content[] = '+ added ability to turn comments on or off';
        break;

    }
    return TRUE;
}


function blog_update_003()
{
    $filename = PHPWS_SOURCE_DIR . 'mod/blog/boost/update_0_0_3.sql';
    $db = & new PHPWS_DB;
    return $db->importFile($filename);
}


function blog_update_004(&$content)
{
    PHPWS_Core::initModClass('rss', 'RSS.php');
    return RSS::registerModule('blog', $content);
}

function blog_update_005(&$content)
{
    $sql = 'ALTER TABLE blog_entries CHANGE date create_date';
    $db = & new PHPWS_DB('blog_entries');
    $result1 = $db->renameTableColumn('date', 'create_date');
    if (PEAR::isError($result1)) {
        return $result1;
    }

    $result2 = $db->addTableColumn('allow_comments', 'SMALLINT NOT NULL');
    if (PEAR::isError($result2)) {
        return $result2;
    }
    return TRUE;
}


?>