<?php

/**
 * Returns the basepath of the server
 *
 * @author Jasper van Herpt
 * @version <b>1.1</b> Class renamed<br>1.0
 */
class Jaztec_Framework_View_Helper_BaseUrlHelper
{
    public function baseUrlHelper()
    {
        $base_url = substr($_SERVER['PHP_SELF'], 0, -9);

        return $base_url;
    }
}
