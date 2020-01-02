<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

/**
 * @method static string font(string $default = null)
 * @method static string black(string $default = null)
 * @method static string white(string $default = null)
 * @method static string white50(string $default = null)
 * @method static string danger(string $default = null)
 * @method static string dangerDark(string $default = null)
 * @method static string success(string $default = null)
 * @method static string warning(string $default = null)
 * @method static string info(string $default = null)
 * @method static string primary(string $default = null)
 * @method static string custom(string $default = null)
 * @method static string blue(string $default = null)
 * @method static string tear(string $default = null)
 * @method static string inverse(string $default = null)
 * @method static string purple(string $default = null)
 * @method static string purpleDark(string $default = null)
 * @method static string orangeLight(string $default = null)
 * @method static string orange(string $default = null)
 * @method static string pink(string $default = null)
 * @method static string primaryDark(string $default = null)
 * @method static string primaryLight(string $default = null)
 * @method static string primary90(string $default = null)
 * @method static string primary80(string $default = null)
 * @method static string primary70(string $default = null)
 * @method static string primary60(string $default = null)
 * @method static string primary50(string $default = null)
 * @method static string primary40(string $default = null)
 * @method static string primary30(string $default = null)
 * @method static string primary20(string $default = null)
 * @method static string primary10(string $default = null)
 * @method static string dark20(string $default = null)
 * @method static string dark30(string $default = null)
 * @method static string dark40(string $default = null)
 * @method static string dark50(string $default = null)
 * @method static string dark60(string $default = null)
 * @method static string dark70(string $default = null)
 * @method static string dark80(string $default = null)
 * @method static string dark90(string $default = null)
 * @method static string dark90half(string $default = null)
 */
class Color
{
    public static $theme = [
        'font'          => '#414750',
        'dark'          => '#22292f',
        'white'         => '#fff',
        'white50'       => 'hsla(0,0%,100%,.5)',
        'danger'        => '#ff5b5b',
        'danger-dark'   => '#bd4147',
        'success'       => '#21b978',
        'warning'       => '#ffcc80',
        'info'          => '#03a9f4',
        'custom'        => '#59a9f8',
        'blue'          => '#007ee5',
        'tear'          => '#26A69A',
        'inverse'       => '#505b6b',
        'purple'        => '#5b69bc',
        'purple-dark'   => '#5b69bc',
        'orange-light'  => '#ffcc80',
        'orange'        => '#F99037',
        'pink'          => '#ff8acc',
        'primary'       => '#4199de',
        'primary-dark'  => '#297ec0',
        'primary-light' => '#e8f5fb',
        'primary90'     => '#52a2e1',
        'primary80'     => '#62abe4',
        'primary70'     => '#73b4e7',
        'primary60'     => '#84bdea',
        'primary50'     => '#95c6ed',
        'primary40'     => '#c7e1f5',
        'primary30'     => '#d7eaf8',
        'primary20'     => '#e8f3fb',
        'primary10'     => '#f9fcfe',
        'dark20'        => '#f6fbff',
        'dark30'        => '#f4f7fa',
        'dark40'        => '#ebf0f3',
        'dark50'        => '#d3dde5',
        'dark60'        => '#bacad6',
        'dark70'        => '#b3b9bf',
        'dark80'        => '#7c858e',
        'dark90'        => '#252d37',
        'dark90half'    => '#5c7089',
    ];

    public static $default = [
        'green' => [
            'rgba(33,185,120, 1)',
            'rgba(33,185,120, 0.1)',
        ],
        'primary' => [
            'rgba(64,153,222, 1)',
            'rgba(64,153,222, 0.1)',
        ],
        'purple' => [
            'rgba(91, 105, 188, 1)',
            'rgba(91,105,188,0.1)',
        ],

        'red' => [
            'rgba(255,91,91, 1)',
            'rgba(255,91,91,0.1)',
        ],

        'custom' => [
            'rgba(89,169,248, 1)',
            'rgba(89,169,248,0.1)',
        ],

        'tear' => [
            'rgba(38,166,154, 1)',
            'rgba(38,166,154,0.1)',
        ],

        'blue' => [
            'rgba(0,126,229, 1)',
            'rgba(0,126,229,0.1)',
        ],
    ];

    public static $chartTheme = [
        'blue' => [
            'rgba(64,153,222,.5)', // primary
            'rgba(64,153,222,.85)', // primary
            '#007ee5', // blue
            '#59a9f8', // custom
            'rgba(121,134,203, 1)', // purple
            '#6474D7', // purple darker
            '#8FC15D', // green
            '#21b978', // success
            '#47C1BF', // tear
            '#F2CB22', // yellow
            '#F99037', // orange
            '#F5573B', // red
            '#9C6ADE', // another purple
            '#ff8acc', // pink
            '#297ec0', // primary darker
            '#483D8B', // blue darker
        ],

        'green' => [
            'rgba(64,153,222,.5)', // primary
            '#21b978', // success
            '#47C1BF', // tear
            '#8FC15D', // green
        ],

        'orange' => [
            'rgba(64,153,222,.5)', // primary
            '#F99037', // orange
            '#F5573B', // red
            '#F2CB22', // yellow
        ],

        'purple' => [
            'rgba(64,153,222,.5)', // primary
            'rgba(121,134,203, 1)', // purple
            '#6474D7', // purple darker
            '#9C6ADE', // another purple
        ],
    ];

    public static function get($key, $default = null)
    {
        return Arr::get(static::$theme, $key, $default);
    }

    public static function __callStatic($method, $arguments)
    {
        $key = Helper::slug($method);

        return static::$theme[$key] ?? ($arguments[0] ?? null);
    }
}
