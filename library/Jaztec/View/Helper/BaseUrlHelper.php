<?php
class Jaztec_View_Helper_BaseUrlHelper  
{  
    function baseUrlHelper()  
    {  
        $base_url = substr($_SERVER['PHP_SELF'], 0, -9);  
        return $base_url;  
    }  
} 