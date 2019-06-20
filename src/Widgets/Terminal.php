<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\StringOutput;
use Illuminate\Support\Facades\Artisan;

class Terminal extends Widget
{
    protected static $style = '<style>info{color: var(--success);}warning{color: var(--warning)}comment{color: rgba(255, 189, 74, .8);}error{color: var(--danger)}</style>';

    protected $content;

    public function __construct($content = null)
    {
        $this->content($content);

        $this->class('dump');
        $this->style('background:#333;color:#fff;');
    }

    /**
     * @param string $command
     * @param array $parameters
     * @return static
     */
    public static function call(string $command, array $parameters = [])
    {
        $output = new StringOutput;
        Artisan::call($command, $parameters, $output);

        return static::make($output);
    }

    public function content($content)
    {
        if ($content instanceof StringOutput) {
            $content = $content->getContent();
        }

        $this->content = &$content;

        return $this;
    }

    public function render()
    {
        $style = static::$style;

        static::$style = null;

        return <<<EOF
{$style}<pre {$this->formatAttributes()}>{$this->content}</pre>
EOF;

    }
}
