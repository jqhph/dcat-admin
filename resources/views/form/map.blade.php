<style>
    .amap-icon img,
    .amap-marker-content img{
        width: 25px;
        height: 34px;
    }
</style>
<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @if($type === 'baidu' || $type === 'amap')
            <div class="row mb-1">
                <div class="col-md-5 col-md-offset-3">
                    <div class="input-group">
                        <input type="text" placeholder="{{ trans('admin.search') }}" class="form-control" id="{{ $searchId }}">
                        @if($type === 'baidu')
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="{{ $class }}">
            <div class="form-map" style="width: 100%;height: {{ $height }}"></div>
            <input type="hidden" class="form-lat" name="{{ $name['lat'] }}" value="{{ $value['lat'] ?? null }}" {!! $attributes !!} />
            <input type="hidden" class="form-lng" name="{{$name['lng']}}" value="{{ $value['lng'] ?? null }}" {!! $attributes !!} />
        </div>

        @include('admin::form.help-block')

    </div>
</div>
<script init="{!! $selector !!}">
    var lat = $this.find('.form-lat'),
        lng = $this.find('.form-lng'),
        container = $this.find('.form-map'),
        mapId = "_" + Dcat.helpers.random();

    container.attr('id', mapId);

    @if($type === 'google')
    function initGoogleMap() {
        var LatLng = new google.maps.LatLng(lat.val(), lng.val());

        var options = {
            zoom: 13,
            center: LatLng,
            panControl: false,
            zoomControl: true,
            scaleControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(container[0], options);

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

    initGoogleMap();
    @endif

    @if($type === 'tencent')
    function initTencentMap() {
        var center = new qq.maps.LatLng(lat.val(), lng.val());

        var map = new qq.maps.Map(container[0], {
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

    initTencentMap();
    @endif


    @if($type === 'yandex')
    function initYandexMap() {
        ymaps.ready(function(){
            var myMap = new ymaps.Map(mapId, {
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

    initYandexMap();
    @endif

    @if($type === 'baidu')
    function initBaiduMap() {
        var map = new BMap.Map(mapId);
        var point = new BMap.Point(lng.val(), lat.val());
        map.centerAndZoom(point, 15);
        map.enableScrollWheelZoom(true);

        var marker = new BMap.Marker(point);
        map.addOverlay(marker);
        marker.enableDragging();

        if (! lat.val() || ! lng.val()) {
            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function(e){
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    map.panTo(e.point);
                    marker.setPosition(e.point);

                    lat.val(e.point.lat);
                    lng.val(e.point.lng);

                } else {
                    console.log('failed'+this.getStatus());
                }
            },{enableHighAccuracy: true})
        }

        map.addEventListener("click", function(e) {
            marker.setPosition(e.point);
            lat.val(e.point.lat);
            lng.val(e.point.lng);
        });

        marker.addEventListener("dragend", function(e) {
            lat.val(e.point.lat);
            lng.val(e.point.lng);
        });
        var ac = new BMap.Autocomplete(
            {"input" : "{{ $searchId }}"
                ,"location" : map
            });
        var address;
        ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
            var _value = e.item.value;
            address = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            setPlace();
        });
        function setPlace() {
            function myFun() {
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

    initBaiduMap();
    @endif
    @if($type === 'amap')
    function initAmap(){
        var map = new AMap.Map(container[0], {
            resizeEnable: true,
            center: lng.val() && lat.val() ? [lng.val(), lat.val()] : null,
            zoom: 14
        });
        var marker = new AMap.Marker({
            position: new AMap.LngLat(lng.val(), lat.val()),
            draggable: true,
            map:map,
            icon:'//a.amap.com/jsapi_demos/static/demo-center/icons/poi-marker-red.png',
            zoom:15
        });
        if (!lng.val() || !lat.val()){
            var geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,
                zoomToAccuracy: true,
                buttonPosition: 'RB'
            })
            geolocation.getCurrentPosition(function (status,result){
                if (status === 'complete'){
                    var point = new AMap.LngLat(result.position.lng, result.position.lat);
                    map.setCenter(point);
                    map.setZoom(15);
                    marker.setPosition(point)
                    lat.val(result.position.lat);
                    lng.val(result.position.lng);
                }
            })
        }
        //输入提示
        var auto = new AMap.Autocomplete({
            input: "{{$searchId}}"
        });
        var placeSearch = new AMap.PlaceSearch({
            map: map
        });
        AMap.event.addListener(auto, "select", function (e){
            placeSearch.setCity(e.poi.adcode);
            placeSearch.search(e.poi.name);
        });
        AMap.event.addListener(placeSearch, "markerClick", function (e){
            let point = new AMap.LngLat(e.data.location.lng, e.data.location.lat);
            marker.setPosition(point)
            lat.val(e.data.location.lat);
            lng.val(e.data.location.lng);
        });
        marker.on('dragend',function (e){
            lat.val(e.lnglat.lat);
            lng.val(e.lnglat.lng);
        });
        map.on('click',function (e){
            if (e.type === 'click'){
                let point = new AMap.LngLat(e.lnglat.lng, e.lnglat.lat);
                marker.setPosition(point)
                lat.val(e.lnglat.lat);
                lng.val(e.lnglat.lng);
            }
        })
    }
    initAmap();
    @endif
</script>
