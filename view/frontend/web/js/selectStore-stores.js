/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Netstarter_StoreLocator
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
define([
    'ko',
    'jquery',
    'CameraHouse_StoreLocator/js/selectStoreMap',
    'postcode',
    'Netstarter_StoreLocator/js/dates',
    'mage/template',
    'jquery/ui'
], function (ko, $, map, postcode, dates, mageTemplate) {
    "use strict";

    function selectStoreInit(params) {
        var mapEle = $('#selectStore-stores-map-canvas'),
            infoEleToppanal = $('#selectStore-stores-info'),
            perPage = params.per_page,
            doh = params.oh, //default open hrs
            dho = params.ho, // default holidays
            paginator = null,
            idx = {},
            len = 0,
            template = mageTemplate('#selectStore-store-info-template');

        try {

            var options = {
                type: 'popup',
                responsive: true,
                "modalClass": "selectStorePopupWrap",
                "title": false,
                "buttons": []
            };

            var selectStorePopup = $('#selectStorePopup').modal(options);

            var favStoreLcData = localStorage.storeData_favorite;

            if (favStoreLcData !== undefined) { // Set open times on load if favorite store set on localstorage

                var favStoreLcDataJSON = jQuery.parseJSON(localStorage.storeData_favorite);
                if (favStoreLcDataJSON != null) {
                    var oh = favStoreLcDataJSON.o ? doh : favStoreLcDataJSON.oh;
                    var ho = favStoreLcDataJSON.h ? dho : favStoreLcDataJSON.ho;
                    var hrs = dates.openingHours(oh, ho);
                    var $storeBlock = $('.my-store-content');
                    var openTime = hrs.om;
                    $storeBlock.find('.is-open').html(openTime);
                }
            }


            var body = $('body');

            body.on('click', '.select-store-btn', function (e) {
                e.preventDefault();
                selectStorePopup.modal('openModal');
            });

            body.on('click', '.select-store-data .store-om', function () {
                $(this).stop().next().toggle();
            });

            body.on('click', '#selectStorePopup .selectStore-store-info', function () {

                // page is on /contact
                if ($('#contact-form').length > 0) {
                    $('#contact .select-store-data').html($(this).html());
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_checkout', $('.store-info-data', this).val());

                    body.trigger('setSelectedStoreEvent');
                }

                // page is on /price match
                if (body.hasClass('price-pricematch-index')) {

                    $('.price-pricematch-index .select-store-data').html($(this).html());
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_checkout', $('.store-info-data', this).val());

                    body.trigger('setSelectedStoreEvent');
                }

                // page is on /checkout
                if (body.hasClass('checkout-index-index')) {
                    $('.checkout-index-index .select-store-data').html($(this).html());
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_checkout', $('.store-info-data', this).val());
                    body.trigger('selectStoreSelected');
                }


                // page is on paypal/review/
                if (body.hasClass('braintree-paypal-review')) {
                    $('.braintree-paypal-review .select-store-data').html($(this).html());
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_checkout', $('.store-info-data', this).val());

                    body.trigger('selectStoreSelected');
                }

                //paypal-express-review
                if (body.hasClass('paypal-express-review')) {

                    $('.paypal-express-review .select-store-data').html($(this).html());
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_checkout', $('.store-info-data', this).val());


                    var data = $('.store-info-data').val();
                    if (data) {
                        data = JSON.parse(data);

                        /*
                         // set State
                         */
                        var stateabr = data.fa.rc;
                        var state = '';

                        if (stateabr) {
                            switch (stateabr) {
                                case 'ACT':
                                    state = "Australian Capital Territory";
                                    break;
                                case 'NSW':
                                    state = "New South Wales";
                                    break;
                                case 'NTE':
                                    state = "Northern Territory";
                                    break;
                                case 'NT':
                                    state = "Northern Territory";
                                    break;
                                case 'QUE':
                                    state = "Queensland";
                                    break;
                                case 'QLD':
                                    state = "Queensland";
                                    break;
                                case 'SAU':
                                    state = "South Australia";
                                    break;
                                case 'SA':
                                    state = "South Australia";
                                    break;
                                case 'TAS':
                                    state = "Tasmania";
                                    break;
                                case 'VIC':
                                    state = "Victoria";
                                    break;
                                case 'WAU':
                                    state = "Western Australia";
                                    break;
                                case 'WA':
                                    state = "Western Australia";
                            }
                        }

                        var region_id = $('#opc-new-shipping-address [name="region_id"]').find('[data-title="' + state + '"]').val();

                        jQuery('[name="store_pickup_id"]').val(data.i).change();
                        jQuery('[name="store_pickup"]').val(data.n).change();

                        var pickupAddress = {
                            'street1': data.fa.st,
                            'street2': data.fa.st2,
                            'city': data.fa.su,
                            'postcode': data.fa.po,
                            'region': state,
                            'region_code': data.fa.rc,
                            'region_id': region_id,
                            'phone': data.p
                        };

                        jQuery('[name="store_pickup_address"]').val(JSON.stringify(pickupAddress));

                        /* Set Revenue Store */
                        jQuery('[name="revenue_store_id"]').val(data.i).change();
                        jQuery('[name="revenue_store"]').val(data.n).change();
                    }

                    //body.trigger('selectStoreSelected');
                    //body.trigger('updateStorePickupAddress');
                    $('#shipping-method-form .ch_paypal_express_freeshipping').trigger("change");
                }


                if (body.hasClass('customer-account-index')) {
                    // set favorite store
                    // $('#contact .select-store-data').html($(this).html());
                    var selected = $('.store-info-data').val();
                    jQuery.parseHTML(selected);
                    /*save into local storage*/
                    window.localStorage.setItem('storeData_favorite', $('.store-info-data', this).val());
                    body.trigger('setSelectedStoreEvent');

                    // Set favorite store data

                    var store = jQuery.parseJSON(localStorage.storeData_favorite);
                    var storeId = jQuery.parseJSON(localStorage.storeData_favorite).i;

                    var favStoreKey = jQuery("#set-fav-store-url").data('post');

                    var storeAjaxUrl = favStoreKey.action + 'uenc/' + favStoreKey.data.uenc;

                    jQuery.ajax({
                        type: 'POST',
                        url: storeAjaxUrl,
                        data: {store_location: storeId}
                    });

                    var oh = store.o ? doh : store.oh;
                    var ho = store.h ? dho : store.ho;
                    var hrs = dates.openingHours(oh, ho);

                    var storeName = store.n;
                    var storeStreet = store.fa.st;
                    // var storeStreet2 = store.fa.st2;
                    var storeState = store.fa.su + " " + store.fa.rc + " " + store.fa.po;
                    var storeMap = 'https://maps.google.com?saddr=Current+Location&daddr=' + store.a;
                    var storePhone = store.p;
                    var openTime = hrs.om;
                    var storeUrl = window.location.origin + store.u;

                    var $storeBlock = $('.my-store-content');
                    $storeBlock.find('.store-name').html(storeName);
                    $storeBlock.find('.street:first').html(storeStreet);
                    // $storeBlock.find('.street:last').html(storeStreet2);
                    $storeBlock.find('.state').html(storeState);
                    $storeBlock.find(".direction ").children("a").attr("href", storeMap);
                    $storeBlock.find(".info-right").find('li').eq(0).html(storePhone);
                    $storeBlock.find('.is-open').html(openTime);

                    $storeBlock.find('.store-details-btn').children("a").attr("href", storeUrl);

                }

                selectStorePopup.modal('closeModal');


            });


            // set initial selected store
            var storeData_checkout, storeData_topStore, storeData_favorite;


            body.on('setSelectedStoreEvent', function () {
                /*
                 * get available storeData
                 * */
                try {
                    storeData_checkout = JSON.parse(window.localStorage.getItem('storeData_checkout'));
                    storeData_topStore = JSON.parse(window.localStorage.getItem('storeData_topStore'));
                    storeData_favorite = JSON.parse(window.localStorage.getItem('storeData_favorite'));
                } catch (e) {
                    console.log(e)
                }



                if (storeData_favorite) {

                    // set favorite store as selected
                    selectedStoreData(storeData_favorite);
                }

                if (storeData_topStore) {
                    // set top store as selected
                    selectedStoreData(storeData_topStore);
                }

                /*
                 * Select Store assign logic
                 * */
                if (storeData_checkout) {
                    // set checkout store as selected
                    selectedStoreData(storeData_checkout);
                }

                if(!storeData_checkout && $('#s_method_freeshipping_freeshipping').is(":checked")){
                    $('[name="is_store_pickup"]').val(0);
                }



                /*
                 * get avalble storeData
                 * */
                function selectedStoreData(storeData) {
                    var store = storeData;
                    var oh = store.o ? doh : store.oh;
                    var ho = store.h ? dho : store.ho;
                    var hrs = dates.openingHours(oh, ho);
                    var html = template({data: store, hrs: hrs, dis: 0, jsonData: JSON.stringify(store)});
                    $('.select-store-data').html(html);

                    $('#pricematch-form #store_locator_id').val(store.i);

                    // setTimeout(function () {
                    //     body.trigger('selectStoreSelected');
                    // }, 1000)
                    if ($('#contact-form').length > 0) {
                        $('#store_locator_id').val(store.i);
                    }

                    if (body.hasClass('customer-account-index')) {
                        console.log(store.i);
                    }

                }


            });


            body.on('selectStoreSelected', function (e, dataSet) {

                var data;
                if (dataSet) {
                    data = dataSet;
                } else {
                    data = $('.store-info-data').val();

                    if (data) {
                        data = JSON.parse(data);
                    }
                }

                try {

                    // save in address book issue
                    $('#shipping-save-in-address-book:checked').trigger('click');

                  //  jQuery('#customer-saved-address-select-box').val(jQuery('#customer-saved-address-select-box option:last').val());

                    //if(!JSON.parse(window.localStorage.getItem('mage-cache-storage')).customer){
                    ko.contextFor($('#shipping-new-address-form [name="city"]')[0]).$data.value(data.fa.su);
                    ko.contextFor($('#shipping-new-address-form [name="autocomplete"]')[0]).$data.value(data.fa.st);
                    ko.contextFor($('#shipping-new-address-form [name="street[0]"]')[0]).$data.value(data.fa.st);
                    if (data.fa.st2) {
                        ko.contextFor($('#shipping-new-address-form [name="street[1]"]')[0]).$data.value(data.fa.st2);
                    }
                    //ko.contextFor($('#shipping-new-address-form [name="autocompletepostcode"]')[0]).$data.value(data.fa.su);
                    ko.contextFor($('#shipping-new-address-form [name="postcode"]')[0]).$data.value(data.fa.po);

                    /**CHSE-1911*/
                    /*ko.contextFor($('#shipping-new-address-form [name="telephone"]')[0]).$data.value(data.p);*/
                    //}


                } catch (e) {
                    console.log(e);
                }


                $('.billing-address-same-as-shipping-block').hide();

                /*
                 // set State
                 */
                var stateabr = data.fa.rc;
                var state = '';

                if (stateabr) {
                    switch (stateabr) {
                        case 'ACT':
                            state = "Australian Capital Territory";
                            break;
                        case 'NSW':
                            state = "New South Wales";
                            break;
                        case 'NTE':
                            state = "Northern Territory";
                            break;
                        case 'NT':
                            state = "Northern Territory";
                            break;
                        case 'QUE':
                            state = "Queensland";
                            break;
                        case 'QLD':
                            state = "Queensland";
                            break;
                        case 'SAU':
                            state = "South Australia";
                            break;
                        case 'SA':
                            state = "South Australia";
                            break;
                        case 'TAS':
                            state = "Tasmania";
                            break;
                        case 'VIC':
                            state = "Victoria";
                            break;
                        case 'WAU':
                            state = "Western Australia";
                            break;
                        case 'WA':
                            state = "Western Australia";
                    }
                }

                var region_id = $('#opc-new-shipping-address [name="region_id"]').find('[data-title="' + state + '"]').val();

                ko.contextFor($('#shipping-new-address-form [name="region_id"]')[0]).$data.value(region_id);
                ko.contextFor($('#shipping-new-address-form [name="region_code"]')[0]).$data.value(data.fa.rc);


                /*
                 // set store data
                 */
                jQuery('[name="store_pickup_id"]').val(data.i);
                jQuery('[name="store_pickup"]').val(data.n);
                jQuery('[name="is_store_pickup"]').val(1);

                var pickupAddress = {
                    'street1': data.fa.st,
                    'street2': data.fa.st2,
                    'city': data.fa.su,
                    'postcode': data.fa.po,
                    'region': state,
                    'region_code': data.fa.rc,
                    'region_id': region_id,
                    'phone': data.p
                };

                jQuery('[name="store_pickup_address"]').val(JSON.stringify(pickupAddress));

                /* Set Revenue Store */
                jQuery('[name="revenue_store_id"]').val(data.i);
                jQuery('[name="revenue_store"]').val(data.n);

                //jQuery('body').trigger('saveShippingInfo');


            });

            /*==============================================*/

            body.on('click', '#current-location-btn', function () {
                if (map.hasBaseChange) {
                    postcode.clear();
                }
                map.currentLocation();
            });

            map.init('#selectStore-stores-map-canvas', params.map);
            postcode.init('#selectStore-suburb-postcode-txt', params.postcode, map.searchLocations.bind(map), map.resetView.bind(map));

            var showStoresFn = function (p, self) {
                var stores = self.sortData;
                var c = perPage * (p - 1);
                if (c < 0)return;
                var storeInfo = '';
                var len = stores.length;
                var bounds = new google.maps.LatLngBounds();

                for (var i = 0; i < perPage && c < len; i++, c++) {
                    var pos = stores[c][0];
                    var distance = stores[c][1] / 100;
                    storeInfo += prepareStoreInfo(pos, distance);
                    bounds.extend(stores[c][2]);
                }

                self.map.fitBounds(bounds);

                infoEleToppanal.html(storeInfo);
            };

            var prepareStoreInfo = function (pos, distance) {
                var store = map.storeLocatorData[pos];
                store.n = store.n.length > 20 ? store.n.substring(0, 20) + '.....' : store.n;
                store.pos = pos;
                var oh = store.o ? doh : store.oh;
                var ho = store.h ? dho : store.ho;
                var hrs = dates.openingHours(oh, ho);
                return template({data: store, hrs: hrs, dis: distance, jsonData: JSON.stringify(store)});
            };

            mapEle.on('setInfo', function (event, pos) {
                if (idx[0] !== undefined) {
                    if (len > perPage) {
                        var sortloc = idx[pos] + 1;
                        var page = Math.ceil(sortloc / perPage);
                        if (paginator) {
                            paginator.simplePaginator('changePage', page);
                        }
                    }
                    $('.store-info.selected').removeClass('selected');
                    $('#info-' + pos).addClass('selected');
                } else {
                    infoEleToppanal.html(prepareStoreInfo(pos));
                }
            });
            mapEle.on('clear', function () {
                infoEleToppanal.html('');
                idx = {};
                len = 0;
            });
            mapEle.on('reset', function () {
                if (map.hasCategory || map.hasBaseChange || map.isMobile) {
                    map.searchLocations(map.blat, map.blng)
                } else {
                    infoEleToppanal.html('');
                    // pageEle.hide();
                }
                idx = {};
                len = 0;
            });
            mapEle.on('searchlocations.selectStore', function (event, self) {
                len = self.storeLocatorData.length;
                var panPoint = new google.maps.LatLng(self.lat, self.lng);
                idx = self.prepareDistances(panPoint);
                showStoresFn(1, self);
            });

            map.getLocations(true);
        } catch (e) {
            console.log(e);
        }
    }

    return function (params) {
        selectStoreInit(params);
    };
});