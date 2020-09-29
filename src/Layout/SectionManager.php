<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;

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
    protected $defaultSections = [];

    /**
     * Inject content into a section.
     *
     * @param string                              $section
     * @param string|Renderable|Htmlable|callable $content
     * @param bool                                $append
     * @param int                                 $priority
     *
     * @return void
     */
    public function inject($section, $content, bool $append = true, int $priority = 10)
    {
        $this->put($section, $content, $append, $priority);
    }

    /**
     * @param string                              $section
     * @param string|Renderable|Htmlable|callable $content
     *
     * @return void
     */
    public function injectDefault(string $section, $content)
    {
        if ($this->hasSection($section)) {
            return;
        }

        $this->defaultSections[$section] = &$content;
    }

    /**
     * Set content to a given section.
     *
     * @param string                              $section
     * @param string|Renderable|Htmlable|callable $content
     * @param bool                                $append
     * @param int                                 $priority
     *
     * @return void
     */
    protected function put(string $section, $content, bool $append = false, int $priority = 10)
    {
        if (! $section) {
            throw new RuntimeException('Section name is required.');
        }

        if (! isset($this->sections[$section])) {
            unset($this->defaultSections[$section]);

            $this->sections[$section] = [];
        }

        if (! isset($this->sections[$section][$priority])) {
            $this->sections[$section][$priority] = [];
        }

        $this->sections[$section][$priority][] = [
            'append' => $append,
            'value'  => &$content,
        ];
    }

    /**
     * Get the string contents of a section.
     *
     * @param string $section
     * @param mixed  $default
     * @param array  $options
     *
     * @return string
     */
    public function yieldContent(string $section, $default = '', array $options = [])
    {
        $defaultSection = $this->defaultSections[$section] ?? null;

        if (! $this->hasSection($section) && $defaultSection === null) {
            return Helper::render($default, [new Fluent()]);
        }

        $content = $this->getSections($section) ?: $defaultSection;

        return $this->resolveContent($section, $content, $options);
    }

    /**
     * Get all of the sections for a given name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getSections(string $name)
    {
        return $this->sortSections($name);
    }

    /**
     * Sort the listeners for a given event by priority.
     *
     * @param string $name
     *
     * @return array
     */
    protected function sortSections(string $name)
    {
        if (empty($this->sections[$name])) {
            return [];
        }
        krsort($this->sections[$name]);

        return call_user_func_array(
            'array_merge',
            $this->sections[$name]
        );
    }

    /**
     * Check if section exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSection(string $name)
    {
        return array_key_exists($name, $this->sections);
    }

    /**
     * Check if default section exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasDefaultSection(string $name)
    {
        return array_key_exists($name, $this->defaultSections);
    }

    /**
     * @param string $name
     * @param mixed  $content
     * @param array  $options
     *
     * @return string
     */
    protected function resolveContent(string $name, &$content, array &$options)
    {
        if (is_string($content)) {
            return $content;
        }

        if (! is_array($content)) {
            $content = [['append' => true, 'value' => $content]];
        }

        $options = new Fluent($options);

        $options->previous = '';

        $result = '';
        foreach ($content as &$item) {
            $value = Helper::render($item['value'] ?? '', [$options]);
            $append = $item['append'] ?? false;

            if (! $append) {
                $result = '';
            }
            $result .= $value;
            $options->previous = $result;
        }

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
