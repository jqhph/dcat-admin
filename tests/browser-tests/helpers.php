<?php

if (! function_exists('test_admin_path')) {
    function test_admin_path($path)
    {
        if (is_object($path)) {
            return $path;
        }

        return admin_base_path($path);
    }
}
