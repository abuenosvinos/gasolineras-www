/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../css/hamburgers.min.css');
require('../css/header.css');
require('../css/information.css');
require('../css/popup.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

$(document).ready(function () {
    $(".popupLink").click(function(){
        $('.popupInfo').show();
    });
    $('.popupInfo').click(function(){
        $('.popupInfo').hide();
    });
    $('.popupCloseButton').click(function(){
        $('.popupInfo').hide();
    });
    $('#header input[type=checkbox]').change(function() {
        infoWindow.close();

        ids = [];
        loadStations();
    });
    $('#header #list-gas span').click(function() {
        $this = $(this);
        $('#search-form #list-gas > span').removeClass('selected');
        $this.addClass('selected');
        gasSelected = $this.data('value');
        $('#search-form #field-gas > span').data( 'value', $this.data('value') );
        $('#search-form #field-gas > span').text( $this.text() );

        infoWindow.close();

        ids = [];
        loadStations();
    });
    $('#search-form #list-gas > span > a').on('click',function(e){
        e.preventDefault();
    });
    $('#search-form #field-gas').on('click', showListGas);
    $('#search-form #show-better').on('click', showGasStation);
    $("#search-form #locate").on('click',function(e){
        if (iAmAndroid) Android.getLatLng();
        else if (iAmSecure) geolocation();
    });

    $(".hamburger").on('click',function(e){
        $(this).toggleClass('is-active');
        $('.menu').toggleClass('desp');
        if ($('.menu').is(":visible")) {
            $('.menu').hide();
        } else {
            $('.menu').show();
        }
    });
});

var gasSelected = 'gas95';
var allMarkers = [];
var ids = [];
var map, errorWindow, infoWindow;
function geolocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords.latitude, position.coords.longitude);

        }, function(error) {
            switch( error.code ) {
                case 1:
                    msg = ('No tienes la localización activada o no has dado permiso al navegador para poder utilizarla.');
                    break;
                case 2:
                    msg = ('Se ha producido un error al intentar localizarte.');
                    break;
                case 3:
                    msg = ('Se ha agotado el tiempo de espera para intentar localizarte.');
                    break;
                default:
                    msg = ('Se ha producido un error al intentar localizarte (¿tienes la localización activada?).');
                    break;
            }
            handleLocationError(msg, errorWindow, map.getCenter());
        });
    } else {
        msg = ('Tu dispositivo o navegador no soporta la localización.');
        handleLocationError(msg, errorWindow, map.getCenter());
    }
}

function setLocation(lat, lng) {
    var pos = {
        lat: lat,
        lng: lng
    };
    map.setCenter(pos);
}
window.setLocation = setLocation;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: parseFloat(data_lat), lng: parseFloat(data_lng)},
        zoom: 12,
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false
    });
    infoWindow = new google.maps.InfoWindow();
    errorWindow = new google.maps.InfoWindow();

    if (iAmSecure && !iAmAndroid) geolocation();

    map.addListener('idle', function(e) {
        loadStations();
    });

    map.addListener('click', function(e) {
        infoWindow.close();
    });
}
window.initMap = initMap;

function loadStations() {
    var bounds = map.getBounds();
    var ne = bounds.getNorthEast(); // LatLng of the north-east corner
    var sw = bounds.getSouthWest(); // LatLng of the south-west corner
    var nw = new google.maps.LatLng(ne.lat(), sw.lng());
    //var se = new google.maps.LatLng(sw.lat(), ne.lng());
    var meters = getDistanceInMeters(map.getCenter(), nw);
    /*var m1 = getDistanceInMeters(map.getCenter(), se);
    if (m1 > meters) {
        meters = m1;
    }*/
    var gas = $('#search-form #field-gas > span').data('value');
    var opened = $('#opened').is(':checked');

    $.ajax({
        cache: true,
        url: data_url_search,
        dataType: "json",
        type: "get",
        data: 'lat=' + map.getCenter().lat() + '&lng=' + map.getCenter().lng() + '&radius=' + meters + '&gas=' + gas + '&opened=' + opened,
        success: function(response) {
            if (response.metadata.total == 0) {
                return true;
            }
            $('.min_low').html(response.metadata.media[gasSelected]['low']['max']);
            $('.min_medium').html(response.metadata.media[gasSelected]['medium']['min']);
            $('.max_medium').html(response.metadata.media[gasSelected]['medium']['max']);
            $('.min_high').html(response.metadata.media[gasSelected]['high']['min']);

            var oldMarkers = allMarkers.slice();
            $.each( response.data, function( i, val ) {

                if (jQuery.inArray(val.id, ids) > 0) {
                    return true;
                }

                var icon = {
                    path: 'M125 410 c-56 -72 -111 -176 -120 -224 -7 -36 11 -83 49 -124 76 -85 223 -67 270 31 28 60 29 88 6 150 -19 51 -122 205 -148 221 -6 3 -32 -21 -57 -54z m110 -175 c35 -34 33 -78 -4 -116 -35 -35 -71 -37 -105 -7 -40 35 -43 78 -11 116 34 41 84 44 120 7z',
                    fillColor: fillColorIcon(response.metadata.media[gasSelected], val[gasSelected]),
                    fillOpacity: 0.8,
                    scale: 0.05,
                    strokeColor: 'black',
                    strokeWeight: 1,
                    /*anchor: new google.maps.Point(185, 500)*/
                }

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(val.lat, val.lng),
                    icon: icon,
                    map: map,
                    metadata: val
                });

                marker.addListener('click', function() {
                    var center = map.getCenter()
                    var gas95 = (val.gas95 > 0) ? val.gas95 : '-';
                    var gas98 = (val.gas98 > 0) ? val.gas98 : '-';
                    var diesel = (val.diesel > 0) ? val.diesel : '-';
                    /*'&amp;origin='+center.lat()+','+center.lng()+'*/
                    var str = '<div id="information">'+
                        '<div class="label">'+val.label+'</div>'+
                        '<div class="address"><a href="https://maps.google.com/maps/dir/?api=1&amp;destination='+val.lat+','+val.lng+'" target="_blank">'+val.city+'. '+val.address+'</a></div>'+
                        '<div class="schedule">'+val.schedule+'</div>'+
                        '<div class="prices">'+
                        '<div class="gas95"><span>Sin Plomo 95</span><span class="price">'+gas95+'</span></div>'+
                        '<div class="gas98"><span>Sin Plomo 98</span><span class="price">'+gas98+'</span></div>'+
                        '<div class="diesel"><span>Diesel</span><span class="price">'+diesel+'</span></div>'+
                        '</div>'+
                        '</div>';
                    if (infoWindow) {
                        infoWindow.close();
                    }
                    infoWindow.setContent(str);
                    infoWindow.open(map, marker);
                });
                allMarkers.push(marker);
                ids.push(val.id);
            });

            $.each( oldMarkers, function( i, val ) {
                if (jQuery.inArray(val.metadata.id, ids) == -1) {
                    console.log('Borro: ' + val.metadata.id);
                    val.setMap(null);
                }
            });
        }
    })
}

function fillColorIcon(media, value) {
    if (value == 0) return "black";
    else if (value <= media['low']['max']) return "green";
    else if (value > media['low']['max'] && value <= media['medium']['max']) return "orange";
    else return "red";
}

function getDistanceInMeters(location1, location2) {
    var lat1 = location1.lat();
    var lon1 = location1.lng();

    var lat2 = location2.lat();
    var lon2 = location2.lng();

    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c; // Distance in km
    return (d * 1000);

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }
}

function showGasStation() {
    var baratos = [];
    var mejorPrecio = -1;
    var masCerca =  -1;
    $.each( allMarkers, function( i, marker ) {
        var ll = new google.maps.LatLng(marker.metadata.lat, marker.metadata.lng);
        var meters = getDistanceInMeters(map.getCenter(), ll);
        if (meters > 30000) return true;
        if (marker.metadata[gasSelected] == 0) return true;

        if (mejorPrecio == -1) mejorPrecio = marker.metadata[gasSelected];
        else if (marker.metadata[gasSelected] < mejorPrecio) mejorPrecio = marker.metadata[gasSelected];

    });
    $.each( allMarkers, function( i, marker ) {
        var ll = new google.maps.LatLng(marker.metadata.lat, marker.metadata.lng);
        var meters = getDistanceInMeters(map.getCenter(), ll);
        if (meters > 30000) return true;

        if (marker.metadata[gasSelected] == mejorPrecio) baratos.push(marker);
    });

    $.each( baratos, function( i, marker ) {
        var ll = new google.maps.LatLng(marker.metadata.lat, marker.metadata.lng);
        var meters = getDistanceInMeters(map.getCenter(), ll);

        if (masCerca == -1) masCerca = meters;
        else if (masCerca < meters) meters = masCerca;
    });

    $.each( baratos, function( i, marker ) {
        var ll = new google.maps.LatLng(marker.metadata.lat, marker.metadata.lng);
        var meters = getDistanceInMeters(map.getCenter(), ll);
        if (masCerca == meters) {
            google.maps.event.trigger(marker, 'click');
        }
    });
}

function showListGas(e) {
    var $radio = $('#search-form #field-gas');
    if( $radio.hasClass('desp') ) {
        $radio.removeClass('desp');
        $('body').off('click', showListGas);
    } else {
        e.stopPropagation();
        if( $('#search-form .select.desp').length )
            $('#search-form .select:not(#field-gas)').click();
        $radio.addClass('desp');
        $('body').on('click', showListGas);
    }
}

function handleLocationError(message, errorWindow, pos) {
    errorWindow.setPosition(pos);
    errorWindow.setContent(message);
    errorWindow.open(map);
}
