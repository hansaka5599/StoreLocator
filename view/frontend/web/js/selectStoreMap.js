/**
 * Copyright © 2015 Netstarter. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/template',
    'module',
    'Netstarter_StoreLocator/js/markerclusterer'
], function ($, mageTemplate, module) {
    'use strict';

    return {
        imageUri:null,
        element:null,
        map: null,
        marker: null,
        infoBox: null,
        center: null,
        cluster: null,
        blat: null,
        blng: null,
        lat: null,
        lng: null,
        hasBaseChange:false,
        hasCategory:false,
        zoom: null,
        pinImg: null,
        isMobile: false,
        category: null,
        dataLength: 0,
        sortData: [],
        storeLocatorData: [],
        template: mageTemplate('#map-template'),

        init: function(ele, opt){
            this.element = $(ele);
            this.validateParams(opt);
            this.blat = opt.lat;
            this.blng = opt.lng;
            this.zoom = opt.zoom;
            this.pinImg = opt.pin_img;
            this.category = opt.category || this.category;
            var styles = opt.styles || null;
            var mom = opt.mom || false; // show map on mobile
            var mapOpt = {
                zoom: this.zoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mom:mom
            };
            if(module && module.uri){
                this.imageUri = module.uri+'/../../images/';
            }
            if(styles){
                mapOpt['styles'] = JSON.parse(styles);
            }
            this.initView(mapOpt);
        },

        getLocations: function(multi){
            if (multi) {
                this.getData({});
            } else {
                this.storeView()
            }
        },

        validateParams: function (opt) {
            if (!opt) {
                throw new Error('No Map options provided');
            }
            if (!opt.lat) {
                throw new Error('Please specify base latitude');
            }
            if (!opt.lng) {
                throw new Error('Please specify base longitude');
            }
            if (!opt.zoom) {
                throw new Error('Please specify default zoom level');
            }
        },

        initView: function (opt) {
            this.center = new google.maps.LatLng(this.blat, this.blng);
            opt['center'] = this.center;
            this.map = new google.maps.Map(this.element.get(0), opt);
            if(Math.max(document.documentElement.clientWidth, window.innerWidth || 0) <= 640){
                this.isMobile = true; //is mobile
            }
            if(this.isMobile && opt.mom != true){
                this.element.hide();
            }
        },

        getData: function (param) {
            var loadInfo = false;
            if (this.category) {
                param.category = this.category;
                this.hasCategory = true;
                loadInfo = true;
            }
            if(this.hasBaseChange)loadInfo = true;
            if(this.hasCategory)loadInfo = true;
            $.get('/stores/index/dataAjax',
                param, function (data) {
                    this.storeLocatorData = data;
                    this.dataLength = data.length;
                    this.clearView();
                    this.storesView();
                    if (loadInfo || this.isMobile) {
                        this.searchLocations(this.lat, this.lng);
                    } else {
                        this.map.setZoom(this.zoom);
                    }
                }.bind(this)
            );
        },

        storeView: function () {
            var latLng = new google.maps.LatLng(this.blat, this.blng);
            var options = {map: this.map,'position': latLng};
            if(this.pinImg){
                options['icon'] = this.pinImg;
            }
            new google.maps.Marker(options);
        },

        storesView: function () {
            if (this.dataLength) {
                this.infoBox = new google.maps.InfoWindow({
                    pixelOffset: new google.maps.Size(-0, -30)
                });
                this.markStores(this.center);
            }
        },

        setInfoWindow: function (map, store, infoBox, ele, pos, temp) {
            return function () {
                if (store !== undefined) {
                    var latLng = new google.maps.LatLng(store.l, store.g);
                    var infoCnt = temp({data: store});
                    infoBox.setContent(infoCnt);
                    infoBox.setPosition(latLng);
                    infoBox.open(map);
                    ele.trigger('setInfo', pos);
                }
            }
        },

        streetView: function (ele) {
            var e = document.getElementById(ele);
            if (e && this.lat && this.lng) {
                return new google.maps.StreetViewPanorama(e,{
                        position: {lat: this.lat, lng: this.lng},
                        pov: {heading: 165, pitch: 0},
                        zoom: 1
                    }
                );
            }
            return null;
        },

        panToPoint: function(lat,lng, zoom){
            var panPoint = new google.maps.LatLng(lat, lng);
            this.map.panTo(panPoint);
            if (this.map.getZoom() != zoom) {
                this.map.setZoom(zoom);
            }
        },

        clearView: function () {
            if (this.cluster) {
                this.cluster.clearMarkers();
            }
            this.element.trigger('clear', []);
        },

        resetView: function () {
            this.panToPoint(this.blat, this.blng, this.zoom);
            this.lat = this.lng = null;
            this.hasBaseChange = false;
            this.element.trigger('reset', []);
        },

        searchLocations: function (lat, lng, ext) {
            this.lat = lat || this.blat;
            this.lng = lng || this.blng;
            this.hasBaseChange = this.hasBaseChange || ext?true:false;
            this.element.trigger('searchlocations', [this]);
        },

        on: function(e, f){
            this.ele.on(e, f);
        },

        prepareDistances: function (center) {
            var len = this.dataLength;
            var spherical = google.maps.geometry.spherical;
            var stores = [];
            while (len--) {
                var sortStore = this.storeLocatorData[len];
                var sortLatLng = new google.maps.LatLng(sortStore.l, sortStore.g);
                var distance = spherical.computeDistanceBetween(sortLatLng, center);
                stores.push([len, Math.round(distance / 10),sortLatLng]);
            }
            stores.sort(function (a, b) {
                return  a[1] - b[1];
            });
            var idx = {};
            len = this.dataLength;
            while (len--) {
                idx[stores[len][0]] = len;
            }
            this.sortData = stores;
            return idx;
        },

        markStores: function () {
            var markers = [];
            var infoBox = this.infoBox;
            var map = this.map;
            var ele = this.element;
            //var start = new Date().getMilliseconds();
            var count = this.dataLength;
            var options = {};
            var template = this.template;
            if(this.pinImg){
                options['icon'] = this.pinImg;
            }
            while (count--) {
                var store = this.storeLocatorData[count];
                options['position'] = new google.maps.LatLng(store.l, store.g);
                var marker = new google.maps.Marker(options);
                google.maps.event.addListener(marker, 'click', this.setInfoWindow(map, store, infoBox, ele, count, template));
                markers.push(marker);
            }
            //var end = new Date().getMilliseconds();
            this.cluster = new MarkerClusterer(this.map, markers, {imagePath:this.imageUri+'/m'});
            //console.log(end - start);
        },

        currentLocation: function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    if (lat && lng) {
                        this.searchLocations(lat, lng);
                    }
                }.bind(this), function (error) {
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            alert("User denied the request for Geolocation.");
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert("Location information is unavailable.");
                            break;
                        case error.TIMEOUT:
                            alert("The request to get user location timed out.");
                            break;
                        case error.UNKNOWN_ERROR:
                            alert("An unknown error occurred.");
                            break;
                    }
                });
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        }
    }
});