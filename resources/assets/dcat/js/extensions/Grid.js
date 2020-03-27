
let defaultName = '_def_';

export default class Grid {
    constructor(Dcat) {
        Dcat.grid = this;

        this.selectors = {};
    }

    // 添加行选择器对象
    addSelector(selector, name) {
        this.selectors[name || defaultName] = selector
    }

    // 获取行选择器选中的ID字符串
    selected(name) {
        return this.selectors[name || defaultName].getSelectedKeys()
    }

    // 获取行选择器选中的行
    selectedRows(name) {
        return this.selectors[name || defaultName].getSelectedRows()
    }
}
