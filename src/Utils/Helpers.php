<?php

if (!function_exists('AoComments')) {

    /**
     * @return \AoComments\Utils\Tools
     */
    function AoComments()
    {
        return app('AoComments');
    }

}