<?php

namespace phpws2\Http;

/**
 * Use this if redirecting back to GET after a POST.
 *
 * Ensures that the client browser will not alert the "are you sure you want to 
 * resubmit?" message if they refresh or click Back.
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */

class SeeOtherResponse extends RedirectResponse
{
    protected function getHttpResponseCode()
    {
        return 303;
    }
}
