<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;

class Orderable extends AbstractDisplayer
{
    public function __construct($value, Grid $grid, Column $column, $row)
    {
        parent::__construct($value, $grid, $column, $row);

        if (! trait_exists('\Spatie\EloquentSortable\SortableTrait')) {
            throw new \Exception('To use orderable grid, please install package [spatie/eloquent-sortable] first.');
        }
    }

    public function display()
    {
        Admin::script($this->script());

        return <<<EOT

<div class="">
    <a href="javascript:void(0)" class=" font-14 {$this->grid->rowName()}-orderable" data-id="{$this->key()}" data-direction="1">
        <svg style="fill: currentColor" t="1582861402297" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="10589" width="15" height="15"><path d="M877.216 491.808" p-id="10590"></path><path d="M856.224 400.768 535.648 73.888c-5.12-5.248-11.744-8.064-18.656-8.8-1.248-0.16-2.464-0.16-3.68-0.192-1.248 0.032-2.464 0-3.712 0.192-6.912 0.736-13.536 3.584-18.656 8.8L170.368 400.768c-12.096 12.352-12.096 32.288 0 44.64 12.096 12.352 31.648 12.352 43.744 0l267.744-273.024 0 756.96c0 17.44 13.856 31.552 30.944 31.552 0.16 0 0.32-0.096 0.48-0.096 0.16 0 0.32 0.096 0.48 0.096 17.088 0 30.944-14.112 30.944-31.552L544.704 172.384l267.744 273.024c12.096 12.352 31.648 12.352 43.744 0C868.32 433.056 868.32 413.12 856.224 400.768z" p-id="10591"></path></svg>
    </a>&nbsp;
    <a href="javascript:void(0)" class=" font-14 {$this->grid->rowName()}-orderable" data-id="{$this->key()}" data-direction="0">
        <svg style="fill: currentColor" t="1582861442213" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="989" width="15" height="15"><path d="M877.216 533.952" p-id="990"></path><path d="M856.224 624.992 535.648 951.872c-5.12 5.248-11.744 8.064-18.656 8.8-1.248 0.16-2.464 0.16-3.68 0.192-1.248-0.032-2.464 0-3.712-0.192-6.912-0.736-13.536-3.584-18.656-8.8L170.368 624.992c-12.096-12.352-12.096-32.288 0-44.64 12.096-12.352 31.648-12.352 43.744 0l267.744 273.024L481.856 96.448c0-17.44 13.856-31.552 30.944-31.552 0.16 0 0.32 0.096 0.48 0.096 0.16 0 0.32-0.096 0.48-0.096 17.088 0 30.944 14.112 30.944 31.552l0 756.96 267.744-273.024c12.096-12.352 31.648-12.352 43.744 0C868.32 592.704 868.32 612.64 856.224 624.992z" p-id="991"></path></svg>
    </a>
</div>
EOT;
    }

    protected function script()
    {
        return <<<JS
(function () {
    var req = 0;
    
    $('.{$this->grid->rowName()}-orderable').off('click').on('click', function() {
        if (req) return;
        
        var key = $(this).data('id'),
            direction = $(this).data('direction'),
            row = $(this).closest('tr'),
            prevAll = row.prevAll(),
            nextAll = row.nextAll(),
            prev = row.prevAll('tr').first(),
            next = row.nextAll('tr').first(),
            level = getLevel(row);
        
        req = 1;
        LA.loading();
        
        function swapable(_o) {
            if (
                _o
                && _o.length 
                && level === getLevel(_o)
            ) {
                return true
            }
        }
        
        function isTr(v) {
            return $(v).prop('tagName').toLocaleLowerCase() === 'tr'
        }
        
        function getLevel(v) {
            return parseInt($(v).data('level') || 0);
        }
        
        function isChildren(parent, child) {
            return getLevel(child) > getLevel(parent);
        }
        
        function getChildren(all, parent) {
            var arr = [], isBreak = false, firstTr;
            all.each(function (_, v) {
                 // 过滤非tr标签
                 if (! isTr(v) || isBreak) return;
                
                 firstTr || (firstTr = $(v));
          
                 // 非连续的子节点
                 if (firstTr && ! isChildren(parent, firstTr)) {
                     return;
                 }
                
                 if (isChildren(parent, v)) {
                     arr.push(v)
                 } else {
                     isBreak = true;
                 }
            });
            
            return arr;
        }
        
        function sibling(all) {
            var next;
            
            all.each(function (_, v) {
                 if (getLevel(v) === level && ! next && isTr(v)) {
                     next = $(v);
                 }
            });
            
            return next;
        }
        
        $.ajax({
            type: 'POST',
            url: '{$this->resource()}/' + key,
            data: {_method:'PUT', _token:LA.token, _orderable:direction},
            success: function(data){
                LA.loading(false);
                req = 0;
                if (data.status) {
                    LA.success(data.message);
                    
                    if (direction) {
                        var prevRow = sibling(prevAll);
                        if (swapable(prevRow) && prev.length && getLevel(prev) >= level) {
                            prevRow.before(row);
                            
                            // 把所有子节点上移
                            getChildren(nextAll, row).forEach(function (v) {
                                prevRow.before(v)
                            });
                        }
                    } else {
                        var nextRow = sibling(nextAll),
                            nextRowChildren = nextRow ? getChildren(nextRow.nextAll(), nextRow) : [];
                        
                        if (swapable(nextRow) && next.length && getLevel(next) >= level) {
                            nextAll = row.nextAll();

                            if (nextRowChildren.length) {
                                nextRow = $(nextRowChildren.pop())
                            }
                            
                             // 把所有子节点下移
                             var all = [];
                            getChildren(nextAll, row).forEach(function (v) {
                                all.unshift(v)
                            });
                            
                            all.forEach(function(v) {
                                nextRow.after(v)
                            });
                            
                            nextRow.after(row);
                        }
                    }
                }
            },
            error: function (a, b, c) {
                req = 0;
                LA.loading(false);
                LA.ajaxError(a, b, c)
            }
        });
    
    });
})()
JS;
    }
}
