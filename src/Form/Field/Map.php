<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;

class Map extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];

    /**
     * Get assets required by this field.
     *
     * @return void
     */
    public static function requireAssets()
    {
        $keys = config('admin.map.keys');

        switch (static::getUsingMap()) {
            case 'tencent':
                $js = '//map.qq.com/api/js?v=2.exp&key='.($keys['tencent'] ?? env('TENCENT_MAP_API_KEY'));
                break;
            case 'google':
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='.($keys['google'] ?? env('GOOGLE_API_KEY'));
                break;
            case 'yandex':
                $js = '//api-maps.yandex.ru/2.1/?lang=ru_RU';
                break;
            case 'baidu':
                $js = '//api.map.baidu.com/api?v=2.0&ak='.($keys['baidu'] ?? env('BAIDU_MAP_API_KEY'));
                break;
            default:
                $js = '//api.map.baidu.com/api?v=2.0&ak='.($keys['baidu'] ?? env('BAIDU_MAP_API_KEY'));
        }

        Admin::js($js);
    }

    public function __construct($column, $arguments)
    {
        $this->column['lat'] = (string) $column;
        $this->column['lng'] = (string) $arguments[0];

        array_shift($arguments);

        $this->label = $this->formatLabel($arguments);

        /*
         * Google map is blocked in mainland China
         * people in China can use Tencent map instead(;
         */
        switch (static::getUsingMap()) {
            case 'tencent':
                $this->useTencentMap();
                break;
            case 'google':
                $this->useGoogleMap();
                break;
            case 'yandex':
                $this->useYandexMap();
                break;
            case 'baidu':
                $this->useBaiduMap();
                break;
            default:
                $this->useBaiduMap();
        }
    }

    protected static function getUsingMap()
    {
        return config('admin.map.provider') ?: config('admin.map_provider');
    }

    public function useGoogleMap()
    {
        $this->script = <<<JS
        (function() {
            function initGoogleMap(name) {
                var lat = $('#{$this->id['lat']}');
                var lng = $('#{$this->id['lng']}');
    
                var LatLng = new google.maps.LatLng(lat.val(), lng.val());
    
                var options = {
                    zoom: 13,
                    center: LatLng,
                    panControl: false,
                    zoomControl: true,
                    scaleControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
    
                var container = document.getElementById("map_"+name);
                var map = new google.maps.Map(container, options);
    
                var marker = new google.maps.Marker({
                    position: LatLng,
                    map: map,
                    title: 'Drag Me!',
                    draggable: true
                });
    
                google.maps.event.addListener(marker, 'dragend', function (event) {
                    lat.val(event.latLng.lat());
                    lng.val(event.latLng.lng());
                });
            }
    
            initGoogleMap('{$this->id['lat']}{$this->id['lng']}');
        })();
JS;
    }

    public function useTencentMap()
    {
        $this->script = <<<JS
        (function() {
            function initTencentMap(name) {
                var lat = $('#{$this->id['lat']}');
                var lng = $('#{$this->id['lng']}');
    
                var center = new qq.maps.LatLng(lat.val(), lng.val());
    
                var container = document.getElementById("map_"+name);
                var map = new qq.maps.Map(container, {
                    center: center,
                    zoom: 13
                });
    
                var marker = new qq.maps.Marker({
                    position: center,
                    draggable: true,
                    map: map
                });
    
                if( ! lat.val() || ! lng.val()) {
                    var citylocation = new qq.maps.CityService({
                        complete : function(result){
                            map.setCenter(result.detail.latLng);
                            marker.setPosition(result.detail.latLng);
                        }
                    });
    
                    citylocation.searchLocalCity();
                }
    
                qq.maps.event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latLng);
                });
    
                qq.maps.event.addListener(marker, 'position_changed', function(event) {
                    var position = marker.getPosition();
                    lat.val(position.getLat());
                    lng.val(position.getLng());
                });
            }
    
            initTencentMap('{$this->id['lat']}{$this->id['lng']}');
        })();
JS;
    }

    public function useYandexMap()
    {
        $this->script = <<<JS
        (function() {
            function initYandexMap(name) {
                ymaps.ready(function(){
        
                    var lat = $('#{$this->id['lat']}');
                    var lng = $('#{$this->id['lng']}');
        
                    var myMap = new ymaps.Map("map_"+name, {
                        center: [lat.val(), lng.val()],
                        zoom: 18
                    }); 
    
                    var myPlacemark = new ymaps.Placemark([lat.val(), lng.val()], {
                    }, {
                        preset: 'islands#redDotIcon',
                        draggable: true
                    });
    
                    myPlacemark.events.add(['dragend'], function (e) {
                        lat.val(myPlacemark.geometry.getCoordinates()[0]);
                        lng.val(myPlacemark.geometry.getCoordinates()[1]);
                    });                
    
                    myMap.geoObjects.add(myPlacemark);
                });
    
            }
            
            initYandexMap('{$this->id['lat']}{$this->id['lng']}');
        })();
JS;
    }

    public function useBaiduMap()
    {
        $this->script = <<<JS
        (function() {
            function initBaiduMap(name) {
                var lat = $('#{$this->id['lat']}');
                var lng = $('#{$this->id['lng']}');

                var map = new BMap.Map("map_"+name);
                var point = new BMap.Point(lng.val(), lat.val());
                map.centerAndZoom(point, 15);
                map.enableScrollWheelZoom(true);

                var marker = new BMap.Marker(point);
                map.addOverlay(marker);
                marker.enableDragging();

                if( ! lat.val() || ! lng.val()) {
                    var geolocation = new BMap.Geolocation();
                    geolocation.getCurrentPosition(function(e){
                        if(this.getStatus() == BMAP_STATUS_SUCCESS){
                            map.panTo(e.point);
                            marker.setPosition(e.point);

                            lat.val(e.point.lat);
                            lng.val(e.point.lng);

                        } else {
                            console.log('failed'+this.getStatus());
                        }
                    },{enableHighAccuracy: true})
                }

                map.addEventListener("click", function(e){
                    marker.setPosition(e.point);
                    lat.val(e.point.lat);
                    lng.val(e.point.lng);
                });

                marker.addEventListener("dragend", function(e){
                    lat.val(e.point.lat);
                    lng.val(e.point.lng);
                });
                var ac = new BMap.Autocomplete(
                    {"input" : "search-{$this->id['lat']}{$this->id['lng']}"
                    ,"location" : map
                });
                var address;
                ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
                    var _value = e.item.value;
                    address = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
                    setPlace();
                });
                function setPlace(){
                    function myFun(){
                        var pp = local.getResults().getPoi(0).point;
                        map.centerAndZoom(pp, 15);
                        marker.setPosition(pp);
                        lat.val(pp.lat);
                        lng.val(pp.lng);
                    }
                    var local = new BMap.LocalSearch(map, {
                        onSearchComplete: myFun
                    });
                    local.search(address);
                }
            }

            initBaiduMap('{$this->id['lat']}{$this->id['lng']}');
        })()
JS;
    }
}
