<?php

/**
 * Custom Helpers
 */

if (! function_exists('uniqStr')) {
    /**
     * Generate unique string
     *
     * @param  string  $string
     * @return string
     */
    function uniqStr($string = 'Chemist')
    {
        $uniqueString = md5($string . microtime());

        return $uniqueString;
    }
}

if (! function_exists('ranBg')) {
    /**
     * Generate unique string
     *
     * @param  string  $string
     * @return string
     */
    function ranBg($default = true)
    {
        $colors = $default ? collect(['danger', 'success', 'info', 'warning', 'primary']) : collect(['blue', 'aqua', 'green', 'red', 'maroon', 'orange', 'purple', 'navy', 'olive']);

        return $colors->random();
    }
}

if (! function_exists('flashData')) {
    /**
     * Get flash data
     *
     * @return collection
     */
    function flashData()
    {
        $res = ['present'=>false, 'type'=>'info', 'message'=>''];

        if (Session::has('flashMessage')) {
            list($message, $type) = explode('|', Session::get('flashMessage'));

            $type = ($type == 'error') ? 'danger' : $type;

            $res['present'] = true;
            $res['type']    = $type;
            $res['message'] = $message;
        }

        return $res;
    }
}
