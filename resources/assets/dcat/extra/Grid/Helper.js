
export default class Helper {
    getChildren(all, parent) {
        let _this = this,
            arr = [],
            isBreak = false,
            firstTr;

        all.each(function (_, v) {
            // 过滤非tr标签
            if (! _this.isTr(v) || isBreak) return;

            firstTr || (firstTr = $(v));

            // 非连续的子节点
            if (firstTr && ! _this.isChildren(parent, firstTr)) {
                return;
            }

            if (_this.isChildren(parent, v)) {
                arr.push(v)
            } else {
                isBreak = true;
            }
        });

        return arr;
    }

    swapable(_o, depth) {
        if (
            _o
            && _o.length
            && depth === this.getDepth(_o)
        ) {
            return true
        }
    }

    sibling(all, depth) {
        let _this = this,
            next;

        all.each(function (_, v) {
            if (_this.getDepth(v) === depth && ! next && _this.isTr(v)) {
                next = $(v);
            }
        });

        return next;
    }

    isChildren(parent, child) {
        return this.getDepth(child) > this.getDepth(parent);
    }

    getDepth(v) {
        return parseInt($(v).data('depth') || 0);
    }

    isTr(v) {
        return $(v).prop('tagName').toLocaleLowerCase() === 'tr'
    }
}
