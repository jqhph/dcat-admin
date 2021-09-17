<?php

if (! function_exists('test_path')) {
    /**
     * @param  string  $file
     * @return string
     */
    function test_path($file = '')
    {
        return __DIR__.($file ? '/'.trim($file, '/') : '');
    }
}

if (! function_exists('test_resource_path')) {
    /**
     * @param  string  $file
     * @return string
     */
    function test_resource_path($file = '')
    {
        return test_path('resources'.($file ? '/'.trim($file, '/') : ''));
    }
}
