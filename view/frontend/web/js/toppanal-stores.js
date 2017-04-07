/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Netstarter_StoreLocator
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
define([
    'jquery',
    'CameraHouse_StoreLocator/js/topPanelMap',
    'postcode',
    'Netstarter_StoreLocator/js/dates',
    'mage/template',
    "jquery/ui"
], function ($, map, postcode, dates, mageTemplate) {
    "use strict";

    function toppanalInit(params) {
        var mapEle =  $('#toppanal-map-canvas');
        var infoEleToppanal =  $('#toppanal-stores-info');
        var perPage = params.per_page;
        var doh = params.oh; //default open hrs
        var dho = params.ho; // default holidays
        var paginator = null;
        var idx = {};
        var len = 0;
        var template = mageTemplate('#toppanal-store-info-template');
        var firstTime = false;

        try{
            map.init('#toppanal-map-canvas',params.map);
            postcode.init('#toppanal-suburb-postcode-txt', params.postcode, map.searchLocations.bind(map), map.resetView.bind(map));

            var showStoresFn = function(p, self) {
                var stores = self.sortData;
                var c =  perPage * (p - 1);
                if (c < 0)return;
                var storeInfo = '';
                var len = stores.length;
                var bounds = new google.maps.LatLngBounds();

                for (var i = 0; i < perPage && c < len;i++,c++) {
                    var pos = stores[c][0];
                    var distance = stores[c][1] / 100;
                    storeInfo += prepareStoreInfo(pos, distance);
                    bounds.extend(stores[c][2]);
                }
                
                self.map.fitBounds(bounds);

                infoEleToppanal.html(storeInfo);
            };

            var prepareStoreInfo = function(pos, distance){
                var store = map.storeLocatorData[pos];
                store.n = store.n;
                store.pos = pos;
                var oh = store.o?doh:store.oh;
                var ho = store.h?dho:store.ho;
                var hrs = dates.openingHours(oh, ho);
                // debugger;
                return template({data: store, hrs: hrs, dis: distance, jsonData: JSON.stringify(store)});
            };

            mapEle.on('setInfo',function(event, pos){
                if(idx[0] !== undefined){
                    if(len > perPage){
                        var sortloc = idx[pos]+1;
                        var page = Math.ceil(sortloc/perPage);
                        if(paginator){
                            paginator.simplePaginator('changePage', page);
                        }
                    }
                    $('.store-info.selected').removeClass('selected');
                    $('#info-'+pos).addClass('selected');
                }else{
                    infoEleToppanal.html(prepareStoreInfo(pos));
                }
            });
            mapEle.on('clear',function(){
                infoEleToppanal.html('');
                idx = {};
                len = 0;
            });
            mapEle.on('reset',function(){
                if(map.hasCategory || map.hasBaseChange || map.isMobile){
                    map.searchLocations(map.blat,map.blng)
                }else{
                    infoEleToppanal.html('');
                    // pageEle.hide();
                }
                idx = {};
                len = 0;
            });
            mapEle.on('searchlocations.top',function(event, self){
                len = self.storeLocatorData.length;
                var panPoint = new google.maps.LatLng(self.lat, self.lng);
                idx = self.prepareDistances(panPoint);
                showStoresFn(1, self);
            });

            map.getLocations(true);
        }catch (e){
            console.log(e);
        }
    }

    return function (params) {
        toppanalInit(params);
    };
});