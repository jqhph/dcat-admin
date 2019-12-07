<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Code extends Markdown
{
    /**
     * @var string
     */
    protected $lang = 'php';

    /**
     * @param string $content
     * @param int    $start
     * @param int    $end
     */
    public function __construct($content = '', int $start = 1, int $end = 10)
    {
        if (is_file($content)) {
            $this->read($content, $start, $end);
            $content = '';
        }

        parent::__construct($content);
    }

    /**
     * 设置语言
     *
     * @param string $lang
     *
     * @return $this
     */
    public function lang(string $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    public function javascript()
    {
        $this->lang = 'javascript';

        return $this;
    }

    public function html()
    {
        $this->lang = 'html';

        return $this;
    }

    public function java()
    {
        $this->lang = 'java';

        return $this;
    }

    public function python()
    {
        $this->lang = 'python';

        return $this;
    }

    /**
     * 读取指定行上下区间文件内容.
     *
     * @param string $file
     * @param int    $lineNumber
     * @param int    $padding
     *
     * @return $this
     */
    public function padding($file, $lineNumber = 1, $padding = 5)
    {
        return $this->read($file, $lineNumber - $padding, $lineNumber + $padding);
    }

    /**
     * 读取指定行文件内容.
     *
     * @param string $file
     * @param int    $start
     * @param int    $end
     *
     * @return $this
     */
    public function read($file, $start = 1, $end = 10)
    {
        if (! $file or ! is_readable($file) || $end < $start) {
            return $this;
        }

        $file = fopen($file, 'r');
        $line = 0;

        $source = '';
        while (($row = fgets($file)) !== false) {
            if (++$line > $end) {
                break;
            }

            if ($line >= $start) {
                $source .= htmlspecialchars($row, ENT_NOQUOTES, config('charset', 'utf-8'));
            }
        }

        fclose($file);

        return $this->content($source);
    }

    protected function build()
    {
        if ($this->content instanceof Renderable) {
            $this->content = $this->content->render();
        }

        return <<<EOF
<div {$this->formatHtmlAttributes()}><textarea style="display:none;">
```{$this->lang}
{$this->content}
```
</textarea></div>
EOF;
    }
}
