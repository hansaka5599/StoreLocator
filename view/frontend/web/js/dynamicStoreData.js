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
    'ns-slick',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($, nsslick) {
    "use strict";

    var sliderInit = false,
        sliderDestroy = false,
        body = $('body');

    if (localStorage.getItem('storeData_topStore') === null) { // Generate slider if top store is not selected

        assignDynamicContent(0);
    }


    body.on('click', '.top-store-info-name', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // mobile bug fix
        $('.toppanal-suburb-postcode-txt').val('');
        $('#toppanal-stores-info').html('');


        $(this).closest('.panel.header').toggleClass('open');
        $('.topstore-locator').stop().toggleClass('active').slideToggle();
        $('.topstore-locator-overlay').toggleClass('active').slideToggle();
    });


    ///
    body.on('click', '.toppanal-store-info', function (e) {
        e.preventDefault();
        e.stopPropagation();
        assignDynamicContent(JSON.parse($(this).find('.store-info-data').val()));
        // jQuery('body').trigger('setSelectedStoreEvent'); // Update stores real time
    });

    body.on('click', '.selectStore-store-info', function (e) {
        e.preventDefault();
        e.stopPropagation();
        assignDynamicContent(JSON.parse($(this).find('.store-info-data').val()));

        var isPriceMatchPage = jQuery('body').hasClass('price-pricematch-index'); // Scroll to select store on price match after selecting a store

        if (isPriceMatchPage) {
            jQuery('html, body').delay(750).animate({
                scrollTop: jQuery(".price-pricematch-index .select-store-btn").offset().top - 10
            }, 500);
        }


    });

    body.on('click', '.topstore-locator-overlay', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('.top-store-info-name').click();
    });

    /*
     // Print shop relate menu links relate operations.
     */
    jQuery('.nav-print-shop-item-container a').click(function (e) {

        var self = this;

        jQuery('.do-rediraction').removeClass('do-rediraction');

        // to captur the rediraction after select the store
        jQuery(this).addClass('do-rediraction');

        // check this has a selected url
        if (jQuery(this).attr('href') == '#') {
            e.preventDefault();
            jQuery(window).scrollTop(0);
            $('.top-store-info-name').click();
        }
    });


    jQuery('[data-printshop-category]').click(function (e) {
        jQuery('.do-rediraction').removeClass('do-rediraction');
        // add redirection Class
        jQuery(this).addClass('do-rediraction');

    });


    /*
     // See all Print Shop service.
     */
    jQuery('.all-print-shop-link').click(function (e) {

        var self = this;


        // to captur the rediraction after select the store
        jQuery(this).addClass('do-rediraction');

        // check has, store is selected
        if (!localStorage.getItem('storeData_topStore') || localStorage.getItem('storeData_topStore')=='0') {
            e.preventDefault();
            jQuery(window).scrollTop(0);
            $('.top-store-info-name').click();
        }
    });

    /*
     // maga menu In store service link manage Click event
     */
    body.on('click', '.mega-menu-wrapper li:last a', function (e) {

        // to captur the rediraction after select the store
        jQuery(this).addClass('do-rediraction');

        // IN STORE SERVICES link
        if (jQuery(this).attr('href') == '#') {
            e.preventDefault();
            jQuery(window).scrollTop(0);
            $('.top-store-info-name').click();
        } else {
            window.location = this.href;
        }
    });

    printshopPagesLinksUpdate();
    /*
     // Print shop links on CMS page.
     */



    /*
     // set favorite store by link
     */
    body.on('click', '#bookmark', function (e) {

        // debugger
        e.preventDefault();


        /*
         // set as current store on store detail page when User not logged In
         */

        var currentFavStore;

        $.ajax({
            url: '/stores/index/dataAjax',
            cache: true,
            success: function (data) {

                data.map(function (k) {

                    var currentStorePage = k;

                    // match store url with all stores object
                    if (window.location.pathname == currentStorePage.u) {
                        assignDynamicContent(currentStorePage);

                        var isCustomerLoggedIn = (JSON.parse(localStorage.getItem('mage-cache-storage')).customer.firstname !== undefined);
                        if (isCustomerLoggedIn) { // Set fav store if user logged in
                            localStorage.setItem('storeData_favorite', JSON.stringify(currentStorePage));
                        }

                    }

                });
            }
        });

        if ($(this).data('post') != '') { // Submit form only if user has logged in
            var params = $(this).data('post'), action = params.action;
            if (params.data.uenc) {
                action += 'uenc/' + params.data.uenc;
            }
            var form = $(this).closest('form');
            form.attr('action', action).submit();

        }


    });


    checkLocalStorageStoreLocator();

    var subdomain = window.location.search.split('=')[1],
        storsToShow = [];

    if (window.location.search.split('=')[0] != '?subdomain') {
        return false;
    }

    /*define popup on domain multy select*/
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true
    };


    var subDomainMultyPopup = jQuery('#subdomainOptionalStors').modal(options);

    /*
     // user coming from a sub-domain
     */

    var url = window.location.origin + '/stores/index/dataAjax';

    $.ajax({
        url: url,
        cache: true,
        success: function (data) {
            data.map(function (k) {
                if (subdomain == k.sd) {
                    storsToShow.push(k);
                }
            });

            var storeDataToAssign;

            if (storsToShow.length == 1) {

                storeDataToAssign = storsToShow[0];
                assignDynamicContent(storeDataToAssign);

            } else {

                var stores = [];
                var storesCount = 9999;


                // Sort stores based on Priority set from backend

                var arrToSort, strObjParamToSortBy, sortAscending, sortedArray = {};

                arrToSort = storsToShow;
                strObjParamToSortBy = "sp";
                sortAscending = 0;


                sortedArray = arrToSort.sort(function (a, b) {
                    return (a[strObjParamToSortBy] < b[strObjParamToSortBy]);
                });

                sortedArray = sortedArray.reverse();

                jQuery.each(sortedArray, function (key, val) {

                    var street2 = "";

                    if (val.fa.st2 !== null) {
                        street2 = val.fa.st2;
                    }

                    // Append store data

                    var storeHtml = ' <div class="store-data-wrapper"> <div class="store-name"> ' + val.n + ' </div> <div class="store-content-wrapper"><div class="store-content-left"> <span class="store-address">' + val.fa.st + '</span> <span class="store-address">' + street2 + '</span> <span class="store-address">' + val.fa.su + ' ' + val.fa.rc + ' ' + val.fa.po + '</span> </div> <div class="store-content-right"> <a href="javascript:void(0);" class="store-url">Select this Store</a> </div></div>  </div>';
                    stores.push("<li data-storobj='" + JSON.stringify(val) + "'> " + storeHtml + " </li>");


                });


                $('#subdomainOptionalStors').append('<ul id="">' + stores.join(" ") + '</ul>');


                subDomainMultyPopup.modal('openModal');

                // Moving popup content & footer into a new wrapper called store-modal-wrapper

                jQuery("#subdomainOptionalStors").closest('.modal-popup').addClass('store-modal-popup');
                jQuery(".store-modal-popup .modal-content").after("<div class='store-modal-wrapper' ></div>");
                jQuery(".store-modal-wrapper").append(jQuery(".store-modal-popup .modal-content"));
                jQuery(".store-modal-wrapper").append(jQuery(".store-modal-popup .modal-footer"));

            }
        }
    });


    $('#subdomainOptionalStors').on('click', 'li', function (e) {
        assignDynamicContent($(this).data('storobj'));
        subDomainMultyPopup.modal('closeModal');
    });


    /*
     // user coming from a sub-domain
     */
    function assignDynamicContent(storeDataToAssign) {

        if (storeDataToAssign !== 0) {
            /*
             // Print shop links relate
             */
            body.off('click.printshop');


            /*
             //  top msg band
             */
            jQuery('.top-store-info-name').text(storeDataToAssign.n);
            jQuery('.top-store-address').text(storeDataToAssign.a);
            jQuery('.top-store-tel').text(storeDataToAssign.p);
            jQuery('.top-store-link').attr('href', storeDataToAssign.u);
            jQuery('.top-store-link').attr('title', storeDataToAssign.n);
            jQuery('.top-store-time').html(getShopOpenDayTime(storeDataToAssign.oh));
            jQuery('.topstore-data-wrapper').addClass('store-info');
            jQuery('.header.promo').fadeOut();
            jQuery('.top-store-info-wrap').fadeIn();
        }


        /** Start Home Page activities */

        if(jQuery('body').hasClass('cms-index-index')){
            /*
             // set home banners
             */
            try {

                jQuery("#home-slider-main").css({ // Hide until slider fully loads
                    "visibility": "hidden",
                    "display": "block"
                });


                var dynamicSlideData = null;
                var mergedArray = undefined;
                dynamicSlideData = JSON.parse(localStorage.getItem('home_slides'));

                if (dynamicSlideData === null) {
                    return;
                }

                if (storeDataToAssign.hb) { // check if store has banners
                    var changingData = storeDataToAssign.hb;
                    mergedArray = jQuery.merge(dynamicSlideData, storeDataToAssign.hb);

                } else {
                    mergedArray = dynamicSlideData; // use default banners if store does not have banners
                }

                var sortedArray = mergedArray.sort(function (a, b) { // Sorting stores based on priority

                    var aName = a.order_banner.toLowerCase();
                    var bName = b.order_banner.toLowerCase();
                    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));

                });

                var finalArray = [];

                jQuery.each(sortedArray, function (key, val) {

                    if (sortedArray[key + 1] !== undefined) {
                        if (sortedArray[key].order_banner !== sortedArray[key + 1].order_banner) {
                            finalArray.push(sortedArray[key]);
                        }

                    } else {
                        finalArray.push(sortedArray[key]);
                    }

                });

                var isSlickInited = jQuery('#home-slider-main > div').hasClass('slick-initialized');

                if (isSlickInited) {  // Unlick if slick is initialized
                    jQuery('#home-slider-main > div').slick('unslick');
                }

                jQuery('#home-slider-main .slide-items').remove();
                jQuery('#home-slider-main > div').slick({autoplay: true, autoplaySpeed: 3000, dots: true});

                var currentDate = new Date().toJSON().slice(0, 10).replace(/-/g, '-');
                var currentTime = new Date().toLocaleTimeString('en-GB', {hour: "numeric", minute: "numeric"});


                jQuery.each(finalArray, function (key, val) {

                    var startDateTime = val.start_time;
                    var endDateTime = val.end_time;
                    var hasValidDateTime = false;
                    var addToSlide = false;


                    if (startDateTime !== null && endDateTime !== null) {
                        hasValidDateTime = true;
                    }

                    if (hasValidDateTime) {

                        var currentDate = new Date();
                        var minDate = new Date(startDateTime);
                        var maxDate = new Date(endDateTime);
                        addToSlide = (currentDate >= minDate && currentDate <= maxDate);

                    } else {
                        addToSlide = true;
                    }


                    if (addToSlide) {


                        var href;
                        (val.click_url === null) ? href = "javascript:void(0)" : href = val.click_url;

                        var target;
                        (val.click_url === null) ? target = "" : target = val.target;

                        var altText;
                        (val.image_alt === null) ? altText = "" : altText = val.image_alt;


                        var mediaPath = localStorage.getItem('wysiwyg_url');
                        var imgData = JSON.parse(val.image_collection_JSON);
                        // var mobileImg = mediaPath + JSON.parse(val.image_collection_JSON)[0].path;


                        var customHtml;
                        (val.ns_banner_custom_html === null) ? customHtml = null : customHtml = val.ns_banner_custom_html;

                        var youtube;
                        (val.youtube === null || val.youtube === "" ) ? youtube = null : youtube = val.youtube;


                        // Generating slider related HTML content for slider

                        var sliderHTML = '<div class="slide-items">';
                        sliderHTML += '<figure>';
                        sliderHTML += '<a href="' + href + '" target="' + target + '" >';
                        sliderHTML += '<picture>';

                        if (val.as_bg == 0 && youtube === null && customHtml === null) {

                            sliderHTML += '<source srcset="' + mediaPath + imgData[0].path + '" media="' + imgData[0].media + '">';
                            sliderHTML += '<img alt="' + altText + '" srcset="' + mediaPath + imgData[1].path + '" />';

                        }

                        if (val.as_bg == 1 && youtube === null && customHtml === null) {
                            sliderHTML += '<div style="height: 500px;background-position: center;background-size: cover ; background-image: url(' + mediaPath + imgData[1].path + ');" class="img-as-bg"></div>';
                        }


                        if (customHtml !== null && youtube === null && val.as_bg == 0) {
                            sliderHTML += '<div class="custom-html-wrapper"> ' + customHtml + ' </div>';
                        }

                        if (youtube !== null) {
                            sliderHTML += '<div class="yt-players-wrapper" id="player-' + key + '" data-yt-id="' + youtube + '"></div>';
                        }


                        sliderHTML += '</picture>';
                        sliderHTML += '</a>';
                        sliderHTML += '</figure>';
                        sliderHTML += '</div>';


                        jQuery('#home-slider-main > div').slick('slickAdd', sliderHTML); // Add content to the slick slider

                    }


                    jQuery("#home-slider-main").css({ // Show slider once slider loaded
                        "visibility": "visible",
                        "display": "block"
                    });


                });

                setTimeout(function () { // Wait until slider loads
                    initYTPlayer(jQuery('#home-slider-main > div')); // Call youtube api if slider has youtube content
                }, 1500);

            } catch (e) {
                console.log(e);
            }

            if (storeDataToAssign === 0) {
                return
            }

            /*
             // assign spot lights
             */
            var spLight = jQuery('.home-page-spotlight');
            // console.log(storeDataToAssign.spl1.url);
            try {

                // 1 spotlight
                saveDefaultStoreData('spl1.url', spLight.find('.sp1').attr('href'), spLight);
                if (storeDataToAssign.spl1.url) {
                    spLight.find('.sp1').attr('href', storeDataToAssign.spl1.url);
                } else {
                    spLight.find('.sp1').attr('href', getDefaultStoreData('spl1.url'));
                }

                saveDefaultStoreData('spl1.alt', spLight.find('.sp1 img').attr('title'), spLight);
                if (storeDataToAssign.spl1.alt) {
                    spLight.find('.sp1 img').attr('title', storeDataToAssign.spl1.alt);
                } else {
                    spLight.find('.sp1 img').attr('title', getDefaultStoreData('spl1.alt'));
                }

                saveDefaultStoreData('spl1.img', spLight.find('.sp1 img').attr('src'), spLight);
                if (storeDataToAssign.spl1.img) {
                    spLight.find('.sp1 img').attr('src', storeDataToAssign.spl1.img);
                } else {
                    spLight.find('.sp1 img').attr('src', getDefaultStoreData('spl1.img'));
                }

                // 2 spotlight
                saveDefaultStoreData('spl2.url', spLight.find('.sp2').attr('href'), spLight);
                if (storeDataToAssign.spl2.url) {
                    spLight.find('.sp2').attr('href', storeDataToAssign.spl2.url);
                } else {
                    spLight.find('.sp2').attr('href', getDefaultStoreData('spl2.url'));
                }

                saveDefaultStoreData('spl2.alt', spLight.find('.sp2 img').attr('title'), spLight);
                if (storeDataToAssign.spl2.alt) {
                    spLight.find('.sp2 img').attr('title', storeDataToAssign.spl2.alt);
                } else {
                    spLight.find('.sp2 img').attr('title', getDefaultStoreData('spl2.alt'));
                }

                saveDefaultStoreData('spl2.img', spLight.find('.sp2 img').attr('src'), spLight);
                if (storeDataToAssign.spl2.img) {
                    spLight.find('.sp2 img').attr('src', storeDataToAssign.spl2.img);
                } else {
                    spLight.find('.sp2 img').attr('src', getDefaultStoreData('spl2.img'));
                }

                // 3 spotlight
                saveDefaultStoreData('spl3.url', spLight.find('.sp3').attr('href'), spLight);
                if (storeDataToAssign.spl3.url) {
                    spLight.find('.sp3').attr('href', storeDataToAssign.spl3.url);
                } else {
                    spLight.find('.sp3').attr('href', getDefaultStoreData('spl3.url'));
                }

                saveDefaultStoreData('spl3.alt', spLight.find('.sp3 img').attr('title'), spLight);
                if (storeDataToAssign.spl3.alt) {
                    spLight.find('.sp3 img').attr('title', storeDataToAssign.spl3.alt);
                } else {
                    spLight.find('.sp3 img').attr('title', getDefaultStoreData('spl3.alt'));
                }

                saveDefaultStoreData('spl3.img', spLight.find('.sp3 img').attr('src'), spLight);
                if (storeDataToAssign.spl3.img) {
                    spLight.find('.sp3 img').attr('src', storeDataToAssign.spl3.img);
                } else {
                    spLight.find('.sp3 img').attr('src', getDefaultStoreData('spl3.img'));
                }


            } catch (e) {
                console.log(e);
            }

        }

        /*
         // assign print shop links
         */
        try {
            //store_link_creations
            var store_link_creations = jQuery('.store-link-creations:not([data-printshop-category])');

            saveDefaultStoreData('ml.pc', store_link_creations.attr('href'), spLight);
            if (storeDataToAssign.ml.pc) {
                store_link_creations.attr('href', storeDataToAssign.ml.pc);
            } else {
                store_link_creations.attr('href', getDefaultStoreData('ml.pc'));
            }

            //store_link_prints
            var store_link_prints = jQuery('.store-link-prints:not([data-printshop-category])');
            saveDefaultStoreData('ml.dp', store_link_prints.attr('href'), spLight);
            if (storeDataToAssign.ml.dp) {
                store_link_prints.attr('href', storeDataToAssign.ml.dp);
            } else {
                store_link_prints.attr('href', getDefaultStoreData('ml.dp'));
            }

            //store_link_photography
            var store_link_photography = jQuery('.store-link-photography:not([data-printshop-category])');
            saveDefaultStoreData('ml.ep', store_link_photography.attr('href'), spLight);
            if (storeDataToAssign.ml.ep) {
                store_link_photography.attr('href', storeDataToAssign.ml.ep);
            } else {
                store_link_photography.attr('href', getDefaultStoreData('ml.ep'));
            }

            //store_link_offers
            var store_link_offers = jQuery('.store-link-offers:not([data-printshop-category])');
            saveDefaultStoreData('ml.lo', store_link_offers.attr('href'), spLight);
            if (storeDataToAssign.ml.lo) {
                store_link_offers.attr('href', storeDataToAssign.ml.lo);
            } else {
                store_link_offers.attr('href', getDefaultStoreData('ml.lo'));
            }

        } catch (e) {
            console.log(e);
        }



        //Assign selected store ID if in the customer account create page.
        if (jQuery('#store_id').length > 0) {
            jQuery('#store_id').val(storeDataToAssign.i);
        }

        saveToLocalStorageStoreLocator(storeDataToAssign);

        printshopPagesLinksUpdate(1);

        /*
         // This has to run after save to local storage
         // maga menu In store service link manage.
         */
        try {
            var storeUrl = JSON.parse(window.localStorage.getItem('storeData_topStore')).u;
            if (storeUrl) {
                // https://ch.dev/store/sydney/services
                $('.mega-menu-wrapper li:last a').attr('href', window.location.origin + storeUrl + '/services');
            }
        } catch (e) {
            console.log(e);
        }

        // hide store locator
        $('.topstore-locator').hide().removeClass('active');
        $('.topstore-locator-overlay').hide().removeClass('active');
        $('.panel.header').removeClass('open');


        // Set Print shop cart urls dynamically

        var prdData = JSON.parse(localStorage.getItem('storeData_topStore')).prd;
        var $cmsPrintShop = jQuery('.cms-print-shop-cards');
        var $shopCardsAnchors = jQuery('.cms-print-shop-cards li a');

        if ($cmsPrintShop.length) {

            $shopCardsAnchors.attr("href", "#");

            if (prdData !== undefined) {

                $shopCardsAnchors.each(function () {

                    var $this = jQuery(this);

                    var categoryData = $this.data("printshop-category");

                    if (categoryData !== undefined) {

                        var prd = JSON.parse(localStorage.getItem('storeData_topStore')).prd;

                        jQuery.each(prd, function (key, val) {

                            var sc = val.sc;

                            if (sc === categoryData) {
                                $this.attr("href", val.ru);
                            }


                        });

                    }


                });

            }

        }

        /*
         // redirect if link has clicked
         */
        if ($('.do-rediraction').length > 0) {
            if ($('.do-rediraction').attr('href')) {
                window.location = $('.do-rediraction').attr('href');
            }
        }


        try {
            // re init picturefill();
            picturefill();
        } catch (e) {
            console.log(e);
        }

    }

    function isInTimeRange(t, st, et) {

        t = t.split(/:/);
        st = st.split(/:/);
        et = et.split(/:/);
        return !(t[0] < st[0]
        || t[0] > et[0]
        || (t[0] == st[0] && t[1] < st[1])
        || (t[0] == et[0] && t[1] > et[1]));


    }


    function validateDateRange(dateFrom, dateTo, dateCheck) {


        var d1 = dateFrom.split("-");
        var d2 = dateTo.split("-");
        var c = dateCheck.split("-");

        var from = new Date(d1[2], parseInt(d1[1]) - 1, d1[0]);  // -1 because months are from 0 to 11
        var to = new Date(d2[2], parseInt(d2[1]) - 1, d2[0]);
        var check = new Date(c[2], parseInt(c[1]) - 1, c[0]);

        return check >= from && check <= to;


    }

    function initYTPlayer(slick) { // Initialize youtube

        var player = "";

        var $slickWrapper = slick;

        if (!$slickWrapper.length) {
            return
        }

        var playerInfoList = [];

        jQuery(".yt-players-wrapper").each(function (key, data) { // list of youtube videos

            var $self = jQuery(this);
            playerInfoList[key] = {
                id: $self.prop('id'),
                height: jQuery("#home-slider-main").height(),
                width: 'auto',
                videoId: $self.attr("data-yt-id")
            }

        });


        var players = new Array();


        function createPlayer(playerInfo) { // Generate players for each youtube video id
            return new YT.Player(playerInfo.id, {
                height: playerInfo.height,
                width: playerInfo.width,
                videoId: playerInfo.videoId,
                playerVars: {rel: 0},
                events: {
                    'onStateChange': function (event) {

                        if (event.data == YT.PlayerState.PLAYING) {
                            $slickWrapper.slick('slickPause');
                        } else {
                            $slickWrapper.slick('slickPlay');
                        }

                    }
                }
            });
        }


        for (var i = 0; i < playerInfoList.length; i++) {
            var curplayer = createPlayer(playerInfoList[i]);
            players[i] = curplayer;
        }

    }


    function getShopOpenDayTime(findOpenDay) {

        try {
            var d = new Date();
            var n = d.getDay();

            var weekday = new Array(7);
            weekday[0] = "Sun";
            weekday[1] = "Mon";
            weekday[2] = "Tue";
            weekday[3] = "Wed";
            weekday[4] = "Thu";
            weekday[5] = "Fri";
            weekday[6] = "Sat";

            var isOpened = n == 0 ? 'Closed Today ' : 'Open Today ';
            var wDay = '( ' + weekday[n] + ' ) ';
            var openTime = n == 0 ? '' : findOpenDay.split(',')[n].split('|')[1].replace('-', ' - ');

            return isOpened + wDay + openTime;
        } catch (e) {
            console.log(e);
        }

    }


    function checkLocalStorageStoreLocator() {

        var retrievedstoreData_topStore = localStorage.getItem('storeData_topStore');

        retrievedstoreData_topStore = JSON.parse(retrievedstoreData_topStore);

        if (retrievedstoreData_topStore) {

            $.ajax({
                url: '/stores/index/dataAjax',
                cache: false,
                success: function (data) {
                    data.map(function (gg) {
                        var currentStore = gg;
                        // match store url with all stores object
                        if (retrievedstoreData_topStore.i == currentStore.i) {
                            assignDynamicContent(currentStore);
                        }
                    });
                }
            });
        }


    }

    function printshopPagesLinksUpdate(donotclick) {
        try {

            if (JSON.parse(window.localStorage.getItem('storeData_topStore')) == null) {
                body.off('click.printshop');
                body.on('click.printshop', '[data-printshop-category]', function () {
                    if(!donotclick){
                        $('.top-store-info-name').click();
                    }
                    jQuery('.do-rediraction').removeClass('do-rediraction');
                    // add redirection Class
                    jQuery(this).addClass('do-rediraction');
                });
            }

            if (window.localStorage.getItem('storeData_topStore') !== null) {
                JSON.parse(window.localStorage.getItem('storeData_topStore')).prd.map(function (k) {
                    jQuery('[data-printshop-category]').off('click.printshop');
                    if (k.dp == 0) {
                        jQuery('[data-printshop-category="' + k.sc + '"]').attr('href', k.ru);
                    }
                    if (k.dp == 1) {
                        var tempStorePath = JSON.parse(window.localStorage.getItem('storeData_topStore')).u;
                        jQuery('[data-printshop-category="' + k.sc + '"]').attr('href', tempStorePath + '/' + k.ru);
                    }
                });
            }

        } catch (e) {
            console.log(e);
        }
    }


    function saveToLocalStorageStoreLocator(storeDataToAssign) {
        try {
            localStorage.setItem("storeData_topStore", JSON.stringify(storeDataToAssign));
        } catch (e) {
            console.log(e);
        }
    }

    function saveDefaultStoreData(name, value) {
        try {
            // check if default value is set
            if (typeof(getDefaultStoreData(name)) != "string") {
                localStorage.setItem(name, value);
            }
        } catch (e) {
            console.log(e);
        }
    }

    function getDefaultStoreData(getName) {
        try {
            return localStorage.getItem(getName);
        } catch (e) {
            console.log(e);
        }
    }


});