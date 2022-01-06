<?php

namespace Dcat\Admin\Widgets;

class Code extends Markdown
{
    /**
     * @var string
     */
    protected $lang = 'php';

    /**
     * @param  string  $content
     * @param  int  $start
     * @param  int  $end
     */
    public function __construct($content = '', int $start = 1, int $end = 1000)
    {
        if (is_array($content) || is_object($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } elseif (is_file($content)) {
            $this->readFileContent($content, $start, $end);
            $content = null;
        }

        parent::__construct($content);
    }

    /**
     * 设置语言.
     *
     * @param  string  $lang
     * @return $this
     */
    public function lang(string $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    public function javascript()
    {
        return $this->lang('javascript');
    }

    public function asHtml()
    {
        return $this->lang('html');
    }

    public function java()
    {
        return $this->lang('java');
    }

    public function python()
    {
        return $this->lang('python');
    }

    /**
     * 读取指定行上下区间文件内容.
     *
     * @param  string  $file
     * @param  int  $lineNumber
     * @param  int  $padding
     * @return $this
     */
    public function section($file, $lineNumber = 1, $context = 5)
    {
        return $this->readFileContent($file, $lineNumber - $context, $lineNumber + $context);
    }

    /**
     * 读取指定行文件内容.
     *
     * @param  string  $file
     * @param  int  $start
     * @param  int  $end
     * @return $this
     */
    public function readFileContent($file, $start = 1, $end = 10)
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

    protected function renderContent()
    {
        $content = parent::renderContent();

        return <<<EOF
```{$this->lang}
{$content}
```
EOF;
    }
}
