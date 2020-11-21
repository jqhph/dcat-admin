<?php

namespace Tests\Browser\Components\Form\Field;

use Dcat\Admin\Form\Field;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;

class Tree extends Component
{
    protected $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * 获取组件的 root selector.
     *
     * @return string
     */
    public function selector()
    {
        return '@container';
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser
            ->whenElementAvailable('@tree', 2)
            ->hasInput($this->name);
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@container' => (new Field($this->name))->getElementClassSelector(),
            '@tree'      => '.da-tree',
            '@input'     => sprintf('input[name="%s"][type="hidden"]', $this->name),
        ];
    }

    /**
     * 选中下拉选框.
     *
     * @param Browser $browser
     * @param mixed   $values
     *
     * @return Browser
     */
    public function choose(Browser $browser, $values)
    {
        $values = json_encode((array) $values);

        $browser->script(<<<JS
var tree = $('{$this->getTreeSelector($browser)}');        
        
tree.jstree("uncheck_all");
tree.jstree("select_node", {$values});
JS
        );

        return $browser;
    }

    /**
     * 展开所有选项.
     *
     * @param Browser $browser
     */
    public function expand(Browser $browser)
    {
        $browser->script(<<<JS
$('{$this->getTreeSelector($browser)}').jstree("open_all");        
JS
        );
    }

    /**
     * 选中所有.
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function checkAll(Browser $browser)
    {
        $browser->script(<<<JS
$('{$this->getTreeSelector($browser)}').jstree("check_all");        
JS
        );

        return $browser;
    }

    /**
     * 取消选中所有.
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function unCheckAll(Browser $browser)
    {
        $browser->script(<<<JS
$('{$this->getTreeSelector($browser)}').jstree("uncheck_all");        
JS
        );

        return $browser;
    }

    /**
     * @param \Laravel\Dusk\Browser $browser
     *
     * @return string
     */
    protected function getTreeSelector(Browser $browser)
    {
        return $this->formatSelector($browser, $this->elements()['@tree']);
    }
}
