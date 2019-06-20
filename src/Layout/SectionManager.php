<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Htmlable;

class SectionManager
{
    /**
     * All of the finished, captured sections.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $sortedSections = [];

    /**
     * @var array
     */
    protected $defaultSections = [];

    /**
     * Inject content into a section.
     *
     * @param  string  $section
     * @param string|Renderable|Htmlable|callable $content
     * @param bool $append
     * @param int $priority
     * @return void
     */
    public function inject($section, $content, bool $append = true, int $priority = 10)
    {
        $this->put($section, $content, $append, $priority);
    }

    /**
     * @param  string  $section
     * @param string|Renderable|Htmlable|callable $content
     * @return void
     */
    public function injectDefault($section, $content)
    {
        if ($this->hasSection($section)) {
            return;
        }

        $this->defaultSections[$section] = &$content;
    }


    /**
     * Set content to a given section.
     *
     * @param string  $section
     * @param string|Renderable|Htmlable|callable $content
     * @param bool $append
     * @param int $priority
     * @return void
     */
    protected function put($section, $content, bool $append = false, int $priority = 10)
    {
        if (!$section) {
            throw new \InvalidArgumentException("Section cant not be empty.");
        }

        if (!isset($this->sections[$section])) {
            unset($this->defaultSections[$section]);

            $this->sections[$section] = [];
        }

        if (!isset($this->sections[$section][$priority])) {
            $this->sections[$section][$priority] = [];
        }

        $this->sections[$section][$priority][] = [
            'append' => $append,
            'value' => &$content,
        ];
    }

    /**
     * Get the string contents of a section.
     *
     * @param $section
     * @param string $default
     * @param array $options
     * @return string
     */
    public function yieldContent($section, $default = '', array $options = [])
    {
        $defaultSection = $this->defaultSections[$section] ?? null;

        if (!$this->hasSection($section) && $defaultSection === null) {
            return value($default);
        }

        $content = $this->getSections($section) ?: $defaultSection;

        return $this->resolveContent($section, $content, $options);

    }

    /**
     * Get all of the sections for a given name.
     *
     * @param  string  $name
     * @return array
     */
    public function getSections($name)
    {
        if (! isset($this->sortedSections[$name])) {
            $this->sortSections($name);
        }

        return $this->sortedSections[$name];
    }

    /**
     * Sort the listeners for a given event by priority.
     *
     * @param  string  $name
     * @return array
     */
    protected function sortSections($name)
    {
        $this->sortedSections[$name] = [];

        if (isset($this->sections[$name])) {
            krsort($this->sections[$name]);

            $this->sortedSections[$name] = call_user_func_array(
                'array_merge', $this->sections[$name]
            );
        }
    }

    /**
     * Check if section exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasSection($name)
    {
        return array_key_exists($name, $this->sections);
    }

    /**
     * Check if default section exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasDefaultSection($name)
    {
        return array_key_exists($name, $this->defaultSections);
    }

    /**
     * @param $name
     * @param $content
     * @param array $options
     * @return string
     */
    protected function resolveContent($name, &$content, array &$options)
    {
        if (is_string($content)) {
            return $content;
        }

        if (!is_array($content)) {
            $content = [['append' => true, 'value' => $content]];
        }

        $options = new Fluent($options);

        $options->previous = '';

        $result = '';
        foreach ($content as &$item) {
            $value  = Helper::render($item['value'] ?? '');
            $append = $item['append'] ?? false;

            if (!$append) {
                $result = '';
            }
            $result .= $value;
            $options->previous = $result;
        }

        $this->sections[$name] = [['value' => &$result]];

        return $result;
    }

    /**
     * Flush all of the sections.
     *
     * @return void
     */
    public function flushSections()
    {
        $this->sections = [];
        $this->defaultSections = [];
    }
}
