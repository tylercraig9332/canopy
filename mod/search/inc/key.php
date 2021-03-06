<?php

/**
 * unregisters deleted keys from search
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 
 */


function search_unregister_key(\Canopy\Key $key)
{
    if (empty($key->id)) {
        return FALSE;
    }

    $db = new PHPWS_DB('search');
    $db->addWhere('key_id', (int)$key->id);
    return $db->delete();
}
