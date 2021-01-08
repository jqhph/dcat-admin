<?php

namespace Dcat\Admin;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Color.
 *
 *
 * @method string primary(int $amt = 0)
 * @method string primaryDarker(int $amt = 0)
 * @method string link(int $amt = 0)
 * @method string info(int $amt = 0)
 * @method string success(int $amt = 0)
 * @method string danger(int $amt = 0)
 * @method string warning(int $amt = 0)
 * @method string indigo(int $amt = 0)
 * @method string blue(int $amt = 0)
 * @method string red(int $amt = 0)
 * @method string orange(int $amt = 0)
 * @method string green(int $amt = 0)
 * @method string cyan(int $amt = 0)
 * @method string purple(int $amt = 0)
 * @method string custom(int $amt = 0)
 * @method string pink(int $amt = 0)
 * @method string dark(int $amt = 0)
 * @method string white(int $amt = 0)
 * @method string white50(int $amt = 0)
 * @method string blue1(int $amt = 0)
 * @method string blue2(int $amt = 0)
 * @method string orange1(int $amt = 0)
 * @method string orange2(int $amt = 0)
 * @method string yellow(int $amt = 0)
 * @method string indigoDarker(int $amt = 0)
 * @method string redDarker(int $amt = 0)
 * @method string blueDarker(int $amt = 0)
 * @method string cyanDarker(int $amt = 0)
 * @method string gray(int $amt = 0)
 * @method string light(int $amt = 0)
 * @method string tear(int $amt = 0)
 * @method string tear1(int $amt = 0)
 * @method string dark20(int $amt = 0)
 * @method string dark30(int $amt = 0)
 * @method string dark35(int $amt = 0)
 * @method string dark40(int $amt = 0)
 * @method string dark50(int $amt = 0)
 * @method string dark60(int $amt = 0)
 * @method string dark70(int $amt = 0)
 * @method string dark80(int $amt = 0)
 * @method string dark90(int $amt = 0)
 * @method string dark90half(int $amt = 0)
 * @method string font(int $amt = 0)
 * @method string grayBg(int $amt = 0)
 * @method string border(int $amt = 0)
 * @method string inputBorder(int $amt = 0)
 * @method string background(int $amt = 0)
 * @method string darkModeBg(int $amt = 0)
 * @method string darkModeFont(int $amt = 0)
 * @method string darkModeColor(int $amt = 0)
 * @method string darkModeColor2(int $amt = 0)
 */
class Color
{
    use Macroable;

    const DEFAULT_COLOR = 'default';

    /**
     * 颜色.
     *
     * @var array
     */
    protected static $extensions = [
        'default' => [
            'colors' => [
                'primary'        => '#586cb1',
                'primary-darker' => '#4c60a3',
                'link'           => '#4c60a3',
            ],
        ],
        'blue-light' => [
            'colors' => [
                'primary'        => '#62a8ea',
                'primary-darker' => '#62a8ea',
                'link'           => '#62a8ea',
            ],
        ],
        'blue' => [
            'colors' => [
                'primary'        => '#6d8be6',
                'primary-darker' => '#6d8be6',
                'link'           => '#6d8be6',
            ],
        ],
        'green' => [
            'colors' => [
                'primary'        => '#4e9876',
                'primary-darker' => '#458769',
                'link'           => '#458769',
            ],
        ],
    ];

    /**
     * 默认颜色.
     *
     * @var array
     */
    protected static $allColors = [
        'info'    => 'blue',
        'success' => 'green',
        'danger'  => 'red',
        'warning' => 'orange',
        'indigo'  => '#5c6bc6',
        'blue'    => '#3085d6',
        'red'     => '#ea5455',
        'orange'  => '#dda451',
        'green'   => '#21b978',
        'cyan'    => '#7367f0',
        'purple'  => '#5b69bc',
        'custom'  => '#59a9f8',
        'pink'    => '#ff8acc',
        'dark'    => '#22292f',
        'white'   => '#fff',
        'white50' => 'hsla(0,0%,100%,.5)',

        // 其他蓝色
        'blue1' => '#007ee5',
        'blue2' => '#3d97dd',

        // 橘色
        'orange1' => '#ffcc80',
        'orange2' => '#F99037',

        // 黄色
        'yellow' => '#edc30e',

        'indigo-darker' => '#495abf',
        'red-darker'    => '#bd4147',
        'blue-darker'   => '#236bb0',
        'cyan-darker'   => '#6355ee',

        // 灰色
        'gray' => '#b9c3cd',
        // 轻灰
        'light' => '#f7f7f9',

        // 水鸭色
        'tear'  => '#01847f',
        'tear1' => '#00b5b5',

        // 深色
        'dark20' => '#f6fbff',
        'dark30' => '#f4f7fa',
        'dark35' => '#e7eef7',
        'dark40' => '#ebf0f3',
        'dark50' => '#d3dde5',
        'dark60' => '#bacad6',
        'dark70' => '#b3b9bf',
        'dark80' => '#7c858e',
        'dark85' => '#5c7089',
        'dark90' => '#252d37',

        // 文本通用颜色
        'font' => '#414750',

        // 灰色背景
        'gray-bg' => '#f1f1f1',

        // 边框颜色
        'border' => '#ebeff2',

        // 表单边框
        'input-border' => '#d9d9d9',

        // 背景色
        'background' => '#eff3f8',

        // 深色模式
        // 背景色
        'dark-mode-bg' => '#2c2c43',
        // 深色
        'dark-mode-color' => '#222233',
        'dark-mode-color2' => '#1e1e2d',
        'dark-mode-font' => '##a8a9bb',
    ];

    /**
     * 主题名称.
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $colors = [];

    /**
     * @var array
     */
    protected $realColors;

    /**
     * 获取主题色名称.
     *
     * @return string
     */
    public function getName()
    {
        if (! $this->name) {
            $this->name = config('admin.layout.color') ?: static::DEFAULT_COLOR;
        }

        return $this->name;
    }

    /**
     * 设置主题色名称.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * 获取颜色.
     *
     * @param string $colorName
     * @param string $default
     *
     * @return string
     */
    public function get(?string $colorName, ?string $default = null)
    {
        if ($this->realColors) {
            return $this->realColors[$colorName] ?? $default;
        }

        $colors = $this->getColors();

        $result = $colors[$colorName] ?? $default;

        if ($result && ! empty($colors[$result])) {
            return $this->get($result, $default);
        }

        return $result;
    }

    /**
     * 获取所有颜色.
     *
     * @return array
     */
    public function all()
    {
        if ($this->realColors === null) {
            $colors = $this->getColors();

            foreach ($colors as $key => &$color) {
                $color = $this->get($key);
            }

            $this->realColors = &$colors;
        }

        return $this->realColors;
    }

    /**
     * 颜色转亮.
     *
     * @param string $color
     * @param int    $amt
     *
     * @return string
     */
    public function lighten(?string $color, int $amt)
    {
        return Helper::colorLighten($this->get($color, $color), $amt);
    }

    /**
     * 颜色转暗.
     *
     * @param string $color
     * @param int    $amt
     *
     * @return string
     */
    public function darken(string $color, int $amt)
    {
        return Helper::colorDarken($this->get($color, $color), $amt);
    }

    /**
     * 颜色透明度转化.
     *
     * @param string       $color
     * @param float|string $alpha
     *
     * @return string
     */
    public function alpha(?string $color, $alpha)
    {
        return Helper::colorAlpha($this->get($color, $color), $alpha);
    }

    /**
     * @return array
     */
    protected function getColors()
    {
        if (! $this->colors) {
            $this->colors = array_merge(
                static::$allColors,
                static::$extensions[$this->getName()]['colors'] ?? []
            );
        }

        return $this->colors;
    }

    /**
     * 获取颜色.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return string
     */
    public function __call(string $method, array $arguments = [])
    {
        return $this->darken(
            Helper::slug($method),
            $arguments[0] ?? 0
        );
    }

    /**
     * 扩展颜色.
     *
     * @param string $name
     * @param array  $colors
     *
     * @return void
     */
    public static function extend(string $name, array $colors)
    {
        static::$extensions[$name] = [
            'colors' => $colors,
        ];
    }
}
