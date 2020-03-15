/*=========================================================================================
    File Name: maps.js
    Description: google maps
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

// Gmaps Maps
// ------------------------------

$(window).on("load", function(){

    // Basic Map
    // ------------------------------

    map = new GMaps({
        div: '#basic-map',
        lat: 9.0820,
        lng: 8.6753,
        zoom: 7
    });
    map.addMarker({
        lat: 9.0765,
        lng: 7.3986,
        title: 'Marker1',
        draggable: true,
    });

    // Info Window
    // ------------------------------

    map = new GMaps({
        div: '#info-window',
        lat: 47.4073,
        lng: 7.7526,
        zoom: 7
    });
    map.addMarker({
        lat: 47.4073,
        lng: 7.76,
        title: 'Marker1',
        infoWindow: {
            content: '<p>Marker1</p>'
        }
    });
    map.addMarker({
        lat: 47.3769,
        lng: 8.5417,
        title: 'Marker2',
        infoWindow: {
            content: '<p>Marker2</p>'
        }
    });
    map.addMarker({
        lat: 46.9480,
        lng: 7.4474,
        title: 'Marker3',
        infoWindow: {
            content: '<p>Marker3</p>'
        }
    });

    // Street View Markers
    // ------------------------------

    map = GMaps.createPanorama({
      el: '#street-view',
      lat : 52.201272,
      lng: 0.118720,
    });

    // Random Value for street heading

    $(".street-heading").on("click", function(){
      map = GMaps.createPanorama({
        el: '#street-view',
        lat : 52.201272,
        lng: 0.118720,
        pov: { heading: Math.random() * 360, pitch: 5 }
      });
    });

    // Random Value for street Pitch

    $(".street-pitch").on("click", function(){
      map = GMaps.createPanorama({
        el: '#street-view',
        lat : 52.201272,
        lng: 0.118720,
        pov: { heading: 20, pitch: Math.random() * 180 - 90 }
      });
    });

    // Random Value for both street heading and street pitch

    $(".street-both").on("click", function(){
      map = GMaps.createPanorama({
        el: '#street-view',
        lat : 52.201272,
        lng: 0.118720,
        pov: { heading: Math.random() * 360, pitch: Math.random() * 180 - 90 }
      });
    });

});

