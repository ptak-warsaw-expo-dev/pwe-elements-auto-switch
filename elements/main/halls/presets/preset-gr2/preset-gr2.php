<?php

if (!empty($all_halls)) {
    $output = '
    <div id="pweHalls" class="pwe-halls"
        data-all-items='. json_encode($json_data_all) .'
        data-active-items='. json_encode($json_data_active) .'>
        <div class="pwe-halls__wrapper">

            <div class="pwe-halls__title">
                <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Powierzchnia wystawiennicza', 'Exhibition space') .'</h4>
                <p>'. PWECommonFunctions::languageChecker('Największa powierzchnia wystawiennicza w Polsce', 'The largest exhibition space in Poland') .'</p>
            </div> 

            <div class="pwe-halls__container">
            
                <div class="pwe-halls__info">
                    <div class="pwe-halls__column logo">
                        <img src="'. PWECommonFunctions::languageChecker('/doc/logo-color.webp', '/doc/logo-color-en.webp') .'"/>
                        <p class="pwe-halls__date"><strong>'. PWECommonFunctions::languageChecker(do_shortcode('[trade_fair_date]'), do_shortcode('[trade_fair_date_eng]')) .'</strong></p>
                    </div>
                    <div class="pwe-halls__column info">
                        <div class="pwe-halls__information">
                            <p class="pwe-halls__letters">'. $halls_word .' '. $all_halls .'</p>
                            <p class="pwe-halls__time">10:00-17:00</p>
                            <p class="pwe-halls__parking">'. PWECommonFunctions::languageChecker('DARMOWY PARKING', 'FREE PARKING') .'</p>
                        </div>
                        <div class="pwe-halls__location">
                            <i class="fa fa-location2 fa-1x fa-fw"></i>
                            Al. Katowicka 62, 05-830 Nadarzyn
                        </div>
                    </div>
                </div>

                <div class="pwe-halls__model">
                    <svg id="pweHallsSvg" class="pwe-halls__svg" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="100 300 3000 1100">

                        <defs>
                            <style>
                                .pwe-halls__element.active {
                                    transform: translate(10px, -20px);
                                    filter: drop-shadow(-10px 20px 20px black);
                                    transition: .3s ease;
                                }
                                .pwe-halls__element.active:hover {
                                    transform: translate(0, 0);
                                    filter: drop-shadow(-10px 20px 20px transparent);
                                }
                                .pwe-halls__link {
                                    cursor: pointer;
                                    display: none;
                                }
                                .pwe-halls__element.active > .pwe-halls__link {
                                    pointer-events: none;
                                    display: block;
                                }
                                .pwe-halls__element.unactive > .pwe-halls__link {
                                    pointer-events: unset;
                                    display: block;
                                }
                                .pwe-halls__element-color {
                                    fill: transparent;
                                    opacity: .7;
                                }

                                .pwe-halls__element-logo image {
                                    width: 300px;
                                    height: 200px;
                                }
                                .pwe-halls__element-favicon image {
                                    width: 140px;
                                    height: 140px;
                                }

                                #A .pwe-halls__element-logo,
                                #B .pwe-halls__element-logo,
                                #C .pwe-halls__element-logo,
                                #D .pwe-halls__element-logo {
                                    transform: rotateX(55deg) rotateZ(56deg);
                                }
                                #A .pwe-halls__element-favicon,
                                #B .pwe-halls__element-favicon,
                                #C .pwe-halls__element-favicon,
                                #D .pwe-halls__element-favicon {
                                    transform: rotateX(55deg) rotateZ(-30deg);
                                }


                                #E .pwe-halls__element-logo,
                                #F .pwe-halls__element-logo {
                                    transform: rotateX(56deg) rotateZ(-27deg);
                                }
                                #E .pwe-halls__element-favicon,
                                #F .pwe-halls__element-favicon {
                                    transform: rotateX(55deg) rotateZ(-30deg);
                                }

                                .st0 {
                                    fill: #42ab36;
                                }
                                .st1 {
                                    fill: #9dc7b3;
                                }
                                .st2 {
                                    fill: url(#Gradient_bez_nazwy_34);
                                }
                                .st3 {
                                    fill: url(#Gradient_bez_nazwy_44);
                                }
                                .st4 {
                                    fill: #6b5330;
                                }
                                .st5 {
                                    fill: url(#Gradient_bez_nazwy_39);
                                }
                                .st6 {
                                    fill: url(#Gradient_bez_nazwy_14);
                                }
                                .st7 {
                                    fill: url(#Gradient_bez_nazwy_16);
                                }
                                .st8 {
                                    fill: #fff;
                                    font-size: 24px;
                                    font-weight: 500;
                                }
                                .st8, .st9 {
                                    isolation: isolate;
                                }
                                .st10 {
                                    fill: #003c28;
                                }
                                .st11 {
                                    fill: url(#Gradient_bez_nazwy_49);
                                }
                                .st12 {
                                    fill: url(#Gradient_bez_nazwy_19);
                                }
                                .st13 {
                                    fill: #9e5174;
                                }
                                .st14 {
                                    fill: url(#Gradient_bez_nazwy_35);
                                }
                                .st15 {
                                    fill: url(#Gradient_bez_nazwy_41);
                                }
                                .st16 {
                                    fill: url(#Gradient_bez_nazwy_37);
                                }
                                .st17 {
                                    fill: #338498;
                                }
                                .st18 {
                                    fill: url(#Gradient_bez_nazwy_25);
                                }
                                .st19 {
                                    fill: url(#Gradient_bez_nazwy_52);
                                }
                                .st20 {
                                    fill: url(#Gradient_bez_nazwy_38);
                                }
                                .st21 {
                                    fill: url(#Gradient_bez_nazwy_26);
                                }
                                .st22 {
                                    fill: none;
                                }
                                .st23 {
                                    fill: url(#Gradient_bez_nazwy_48);
                                }
                                .st24 {
                                    fill: #ba1a3b;
                                }
                                .st25 {
                                    fill: #ffa935;
                                }
                                .st26 {
                                    fill: url(#Gradient_bez_nazwy_11);
                                }
                                .st27 {
                                    fill: url(#Gradient_bez_nazwy_24);
                                }
                                .st28 {
                                    fill: url(#Gradient_bez_nazwy_13);
                                }
                                .st29 {
                                    fill: url(#Gradient_bez_nazwy_50);
                                }
                                .st30 {
                                    fill: #c65789;
                                }
                                .st31 {
                                    fill: url(#Gradient_bez_nazwy_3);
                                }
                                .st32 {
                                    fill: #c4c4c4;
                                }
                                .st33 {
                                    fill: #fec902;
                                }
                                .st34 {
                                    fill: url(#Gradient_bez_nazwy_15);
                                }
                                .st35 {
                                    fill: url(#Gradient_bez_nazwy_23);
                                }
                                .st36 {
                                    fill: #f15844;
                                }
                                .st37 {
                                    fill: url(#Gradient_bez_nazwy_28);
                                }
                                .st38 {
                                    fill: url(#Gradient_bez_nazwy_43);
                                }
                                .st39 {
                                    fill: url(#Gradient_bez_nazwy_10);
                                }
                                .st40 {
                                    fill: #e4091e;
                                }
                                .st41 {
                                    fill: url(#Gradient_bez_nazwy_32);
                                }
                                .st42 {
                                    fill: #4fae32;
                                }
                                .st43 {
                                    fill: url(#Gradient_bez_nazwy_4);
                                }
                                .st44 {
                                    fill: url(#Gradient_bez_nazwy_7);
                                }
                                .st45 {
                                    fill: url(#Gradient_bez_nazwy_6);
                                }
                                .st46 {
                                    fill: #76bad5;
                                }
                                .st47 {
                                    fill: url(#Gradient_bez_nazwy_36);
                                }
                                .st48 {
                                    fill: url(#Gradient_bez_nazwy_21);
                                }
                                .st49 {
                                    fill: url(#Gradient_bez_nazwy_40);
                                }
                                .st50 {
                                    fill: url(#Gradient_bez_nazwy_20);
                                }
                                .st51 {
                                    fill: url(#Gradient_bez_nazwy_31);
                                }
                                .st52 {
                                    fill: #f2c780;
                                }
                                .st53 {
                                    fill: url(#Gradient_bez_nazwy_42);
                                }
                                .st54 {
                                    fill: url(#Gradient_bez_nazwy_27);
                                }
                                .st55 {
                                    fill: url(#Gradient_bez_nazwy_30);
                                }
                                .st56 {
                                    fill: url(#Gradient_bez_nazwy_33);
                                }
                                .st57 {
                                    fill: url(#Gradient_bez_nazwy_17);
                                }
                                .st58 {
                                    fill: #aaa;
                                }
                                .st59 {
                                    fill: url(#Gradient_bez_nazwy_8);
                                }
                                .st60 {
                                    fill: #3c3c3b;
                                }
                                .st61 {
                                    fill: url(#Gradient_bez_nazwy_45);
                                }
                                .st62 {
                                    fill: url(#Gradient_bez_nazwy_12);
                                }
                                .st63 {
                                    fill: #ededed;
                                }
                                .st64 {
                                    fill: url(#Gradient_bez_nazwy_2);
                                }
                                .st65 {
                                    fill: #3583c5;
                                }
                                .st66 {
                                    fill: url(#Gradient_bez_nazwy_51);
                                }
                                .st67 {
                                    fill: #fab511;
                                }
                                .st68 {
                                    fill: url(#Gradient_bez_nazwy_46);
                                }
                                .st69 {
                                    fill: url(#Gradient_bez_nazwy_22);
                                }
                                .st70 {
                                    fill: url(#Gradient_bez_nazwy_47);
                                }
                                .st71 {
                                    fill: #9e284a;
                                }
                                .st72 {
                                    fill: #83b163;
                                }
                                .st73 {
                                    fill: url(#Gradient_bez_nazwy_29);
                                }
                                .st74 {
                                    fill: url(#Gradient_bez_nazwy_18);
                                }
                            </style>

                            <linearGradient id="Gradient_bez_nazwy_2" data-name="Gradient bez nazwy 2" x1="2538" y1="2274.3" x2="2460.7" y2="2449" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".5" stop-color="#cdcccc"/>
                                <stop offset=".9" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_3" data-name="Gradient bez nazwy 3" x1="2287" y1="1037.7" x2="2244.9" y2="979.6" gradientTransform="translate(0 1594) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".4" stop-color="#ddd"/>
                                <stop offset=".8" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_4" data-name="Gradient bez nazwy 4" x1="2346.7" y1="2217.3" x2="2267.3" y2="2396.8" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".5" stop-color="#cdcccc"/>
                                <stop offset=".9" stop-color="#9e9e9e"/>
                            </linearGradient>
                                <linearGradient id="Gradient_bez_nazwy_6" data-name="Gradient bez nazwy 6" x1="2665.3" y1="2111.9" x2="2692.5" y2="2012.6" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".2" stop-color="#ddd"/>
                                <stop offset=".8" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_7" data-name="Gradient bez nazwy 7" x1="2368.5" y1="2063.5" x2="2324.4" y2="2002.7" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#ddd"/>
                                <stop offset=".7" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_8" data-name="Gradient bez nazwy 8" x1="2465.9" y1="2052.2" x2="2492.8" y2="1954.2" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".2" stop-color="#ddd"/>
                                <stop offset=".7" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_10" data-name="Gradient bez nazwy 10" x1="1995.8" y1="2117.5" x2="1922.1" y2="2284" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".5" stop-color="#cdcccc"/>
                                <stop offset=".9" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_11" data-name="Gradient bez nazwy 11" x1="1757.9" y1="902.1" x2="1715.4" y2="843.6" gradientTransform="translate(0 1594) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ededed"/>
                                <stop offset=".4" stop-color="#ddd"/>
                                <stop offset=".8" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_12" data-name="Gradient bez nazwy 12" x1="1811.7" y1="2065.8" x2="1737.3" y2="2233.9" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".5" stop-color="#cdcccc"/>
                                <stop offset=".9" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_13" data-name="Gradient bez nazwy 13" x1="2108.6" y1="1962.8" x2="2129.1" y2="1870.4" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".2" stop-color="#ddd"/>
                                <stop offset=".7" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_14" data-name="Gradient bez nazwy 14" x1="1916.3" y1="1912.7" x2="1940.5" y2="1824.6" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".2" stop-color="#ddd"/>
                                <stop offset=".7" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_15" data-name="Gradient bez nazwy 15" x1="1823.1" y1="1935.7" x2="1783.3" y2="1880.8" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#ddd"/>
                                <stop offset=".7" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_16" data-name="Gradient bez nazwy 16" x1="1463.6" y1="2061" x2="1471.7" y2="2033" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ea155c"/>
                                <stop offset=".3" stop-color="#ee4b82"/>
                                <stop offset=".3" stop-color="#f37ea6"/>
                                <stop offset=".4" stop-color="#f593b4"/>
                                <stop offset=".6" stop-color="#ba1a3b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_17" data-name="Gradient bez nazwy 17" x1="1298.6" y1="2106.7" x2="1203" y2="1994.9" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_18" data-name="Gradient bez nazwy 18" x1="1193.3" y1="3150.6" x2="1114.2" y2="3052.3" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_19" data-name="Gradient bez nazwy 19" x1="1584.8" y1="1884.3" x2="1615.9" y2="1794.7" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_20" data-name="Gradient bez nazwy 20" x1="1477.6" y1="1952.1" x2="1383.8" y2="1842.4" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_21" data-name="Gradient bez nazwy 21" x1="1493.4" y1="-1354.7" x2="1519.7" y2="-1251.6" gradientTransform="translate(0 2188)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_22" data-name="Gradient bez nazwy 22" x1="1373.8" y1="2995.9" x2="1295" y2="2897.9" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_23" data-name="Gradient bez nazwy 23" x1="1256.9" y1="1959.2" x2="1260.9" y2="1959.2" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_24" data-name="Gradient bez nazwy 24" x1="1245.4" y1="1967.7" x2="1249.3" y2="1967.7" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_25" data-name="Gradient bez nazwy 25" x1="1208" y1="1990.8" x2="1215.7" y2="1964" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ea155c"/>
                                <stop offset=".3" stop-color="#ee4b82"/>
                                <stop offset=".3" stop-color="#f37ea6"/>
                                <stop offset=".4" stop-color="#f593b4"/>
                                <stop offset=".6" stop-color="#ba1a3b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_26" data-name="Gradient bez nazwy 26" x1="1002.5" y1="2018.6" x2="906.9" y2="1906.9" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_27" data-name="Gradient bez nazwy 27" x1="897.2" y1="3062.5" x2="818.1" y2="2964.2" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_28" data-name="Gradient bez nazwy 28" x1="1288.7" y1="1796.3" x2="1319.8" y2="1706.7" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_29" data-name="Gradient bez nazwy 29" x1="1181.5" y1="1864" x2="1087.9" y2="1754.5" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_30" data-name="Gradient bez nazwy 30" x1="1197.3" y1="-1266.7" x2="1223.6" y2="-1163.5" gradientTransform="translate(0 2188)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_31" data-name="Gradient bez nazwy 31" x1="1077.7" y1="2907.8" x2="998.9" y2="2809.9" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_32" data-name="Gradient bez nazwy 32" x1="958.2" y1="1873.3" x2="962.1" y2="1873.3" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_33" data-name="Gradient bez nazwy 33" x1="946.6" y1="1881.8" x2="950.6" y2="1881.8" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_34" data-name="Gradient bez nazwy 34" x1="911.4" y1="1900.8" x2="919" y2="1874.3" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ea155c"/>
                                <stop offset=".3" stop-color="#ee4b82"/>
                                <stop offset=".3" stop-color="#f37ea6"/>
                                <stop offset=".4" stop-color="#f593b4"/>
                                <stop offset=".6" stop-color="#ba1a3b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_35" data-name="Gradient bez nazwy 35" x1="705.5" y1="1931.5" x2="609.9" y2="1819.8" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_36" data-name="Gradient bez nazwy 36" x1="601.1" y1="2974.8" x2="522" y2="2876.5" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_37" data-name="Gradient bez nazwy 37" x1="991.8" y1="1709.2" x2="1022.9" y2="1619.6" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_38" data-name="Gradient bez nazwy 38" x1="884.6" y1="1777.1" x2="790.4" y2="1666.9" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_39" data-name="Gradient bez nazwy 39" x1="900.3" y1="-1179.6" x2="926.7" y2="-1076.4" gradientTransform="translate(0 2188)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_40" data-name="Gradient bez nazwy 40" x1="780.8" y1="2820.8" x2="701.9" y2="2722.8" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_41" data-name="Gradient bez nazwy 41" x1="663.5" y1="1784.5" x2="667.4" y2="1784.5" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_42" data-name="Gradient bez nazwy 42" x1="651.9" y1="1793" x2="655.8" y2="1793" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_43" data-name="Gradient bez nazwy 43" x1="616.5" y1="1816.6" x2="624.8" y2="1787.9" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ea155c"/>
                                <stop offset=".3" stop-color="#ee4b82"/>
                                <stop offset=".3" stop-color="#f37ea6"/>
                                <stop offset=".4" stop-color="#f593b4"/>
                                <stop offset=".6" stop-color="#ba1a3b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_44" data-name="Gradient bez nazwy 44" x1="412.4" y1="1845.7" x2="316.8" y2="1734" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_45" data-name="Gradient bez nazwy 45" x1="307.1" y1="2889.6" x2="228" y2="2791.3" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_46" data-name="Gradient bez nazwy 46" x1="693.3" y1="1629.4" x2="780.3" y2="1429.1" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_47" data-name="Gradient bez nazwy 47" x1="591.5" y1="1691.1" x2="497.7" y2="1581.4" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_48" data-name="Gradient bez nazwy 48" x1="607.2" y1="-1094" x2="633.6" y2="-990.4" gradientTransform="translate(0 2188)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset="0" stop-color="#ddd"/>
                                <stop offset=".4" stop-color="#9e9e9e"/>
                                <stop offset="1" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_49" data-name="Gradient bez nazwy 49" x1="487.6" y1="2735" x2="408.7" y2="2637" gradientTransform="translate(0 3782) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ededed"/>
                                <stop offset=".3" stop-color="#9e9e9e"/>
                                <stop offset=".5" stop-color="#1d1d1b"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_50" data-name="Gradient bez nazwy 50" x1="367.6" y1="1700.3" x2="371.5" y2="1700.3" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_51" data-name="Gradient bez nazwy 51" x1="356" y1="1708.8" x2="360" y2="1708.8" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#b2b2b2"/>
                                <stop offset="0" stop-color="#c4c4c4"/>
                                <stop offset=".2" stop-color="#dedede"/>
                                <stop offset=".2" stop-color="#f0f0f0"/>
                                <stop offset=".3" stop-color="#fbfbfb"/>
                                <stop offset=".4" stop-color="#fff"/>
                                <stop offset=".5" stop-color="#fafafa"/>
                                <stop offset=".6" stop-color="#eee"/>
                                <stop offset=".7" stop-color="#d8d8d8"/>
                                <stop offset=".8" stop-color="#bbb"/>
                                <stop offset=".9" stop-color="#959595"/>
                                <stop offset=".9" stop-color="#878787"/>
                            </linearGradient>
                            <linearGradient id="Gradient_bez_nazwy_52" data-name="Gradient bez nazwy 52" x1="365.2" y1="1740.8" x2="373.3" y2="1712.7" gradientTransform="translate(0 2688) scale(1 -1)" gradientUnits="userSpaceOnUse">
                                <stop offset=".2" stop-color="#ea155c"/>
                                <stop offset=".3" stop-color="#ee4b82"/>
                                <stop offset=".3" stop-color="#f37ea6"/>
                                <stop offset=".4" stop-color="#f593b4"/>
                                <stop offset=".6" stop-color="#ba1a3b"/>
                            </linearGradient>
                        </defs>

                        <g id="drogi">
                            <g>
                            <path class="st32" d="M2994.2,683.8l-57.4-48.9-259-217.4c-2.2-2.5-7.9-3.7-12.7-2.7l-489.1,133.2c-8.7-5.6-23.7-7.9-36.8-5.2-12.9,2.7-19.9,9.5-17.8,16.3-107.2,21.4-458,134.6-582.3,157.5l-261.5-221.6c-.6-.5-1.4-.9-2.4-1.1l-3.7-3.1L134.6,826.4c-4.4,1.3-5.8,4.3-3.1,6.6l417,354.3c2.8,2.3,8.7,3.2,13.2,1.9l238.9-69.4c10.8,6,27.9,8.4,43.1,5.5,48.3,28.3,105.3,21.8,174.9,13.9,18.6-2.1,38.5-5.6,58.2-9.7,54.9-11.4,108.3-27.8,129.5-34.5,6.3,7.2,11,13.6,13.3,18.5,9.1,19.2,12.9,42.4,10.1,50.7l-7.2,27.2,19.8-5.5,6.4-21.7c2.7-8.1,0-17-3.6-31.9-.8-4.2-1.2-11.1,3.6-12.4,6.6-1.9,21.4,11,22.7,12.1h0c13.7,12.1,25.9,22.6,26.8,23.4l11.8,10.8,17.5-5-12.9-10.5c-.4-.3-36.4-30.8-60-48.6-19.9-15.1-58.6-45.9-93.8-67.8-.7-.4-1.4-.9-2.1-1.3,7.4-5.3,9.2-12.5,3.4-19.1h0l232.5-64.1c10.9,4.3,25.6,5.7,38.7,2.9,15.8-3.3,24.4-11.4,22.3-19.8l288.7-79.4c5.8,3.6,14.1,5.8,22.9,6.1l143.6,134.4,15.4-4.9-140.6-131.8c4-1.3,7.2-3.1,9.5-5.1,87.5,14.8,245.7-14.7,336.4-21.4l2.6-.2c18.8-1.4,158.7-31.1,266.8-47.3l60,53,15.9-4.8-57.8-51c55.3-8.4,106.6-16.8,106.6-16.8,0,0,382.9-110.3,404.3-116,7.6,6.7,40.7,34.1,46.8,39.5M1273.1,501.9l255.7,216.6c-1.5.3-2.9.5-4.3.8l-4.1.7-254.8-215.9,7.6-2.2h0ZM675.3,678.1l408.4,346c.2.2.4.3.7.4l-231.3,67.3-404.8-346.8,227-66.9h0ZM791.3,1111l-231.5,67.3-407.1-345.8,230.5-68.3,407.2,345c.2.6.5,1.2.8,1.8h0ZM792,1099.8l-399.4-338.4,46.5-13.7,397,340.1c-7.6-.9-15.8-.8-23.4.8-10.2,2.1-17.3,6.3-20.6,11.2h0ZM1013.6,1129.6c-71.9,8.2-116.5,11.8-154.7-9.9,8.3-5.2,10.7-12.7,5.1-19.6l232.6-67.6c10.7,7.3,29.8,10.4,46.5,7,.3,0,.6-.2.9-.2,18.9,13.6,39,31.1,53.9,46.6-25.1,8.1-115.4,35.9-184.4,43.8h0ZM1111.1,1003c-12.6,2.6-20.6,8.4-22.2,14.9l-404.3-342.5,47.9-14.1,399.5,340.7c-6.9-.6-14.1-.3-20.9,1.1h0ZM1382.4,941.8l-232.5,64.1c-.4-.2-.8-.3-1.2-.4l-406.9-347,226.8-66.9,411.1,348.1c.5.4,1.1.7,1.8,1,.3.3.5.7.8,1h0ZM1401.4,916.2c-11.4,2.4-19.1,7.3-21.6,13l-402-340.4,45.9-13.5,401.7,340.1c-7.8-1-16.2-.9-24,.7h0ZM1448.5,923.6c-1.1-.8-2.3-1.5-3.6-2.2l-411.9-348.7,223.3-65.8,418.3,354.4-226.1,62.4h0ZM1684,858.6l-153.4-130,.9-.2c2.3-.4,4.8-.8,7.5-1.3l152.7,129.4-7.7,2.1h0ZM2381.8,776.9l-253.8,44.3c-147.6,10.8-267,32.3-329.1,21.5-.4-1.5-1.2-3.1-2.6-4.6-7.1-8.1-25.5-12-41.1-8.8-12.2,2.5-19,8.7-18.1,15.1l-35.9,9.6-151.8-128.6c129.2-23.9,475.8-136.3,580.7-157.2,8.9,4.8,34.9,4.1,34.9,4.1,0,0,18.9-8.6,18.1-14.9l486.4-131.6,251.2,213.9c-61.7,15.9-403.1,115.1-403.1,115.1l-135.8,22.1h0Z"/>
                            <rect class="st32" x="549.7" y="1032.6" width="2569.1" height="18.7" rx="9.3" ry="9.3" transform="translate(-215.3 542.4) rotate(-15.9)"/>
                            </g>
                            <rect class="st32" x="534.3" y="1013.1" width="2569.1" height="18.7" rx="9.3" ry="9.3" transform="translate(-210.5 537.4) rotate(-15.9)"/>
                        </g>

                        <g id="F"  class="pwe-halls__element full">
                            <g id="F2_F1" class="pwe-halls__element half">
                                <g id="F2" class="pwe-halls__element quarter">
                                    <g id="obiekt_F2">
                                        <polygon id="bok_F2" class="st58" points="2424.3 456.9 2518.8 529.7 2518.8 586.6 2424.3 511.8 2424.3 456.9"/>
                                        <polygon id="przod" class="st58" points="2518.8 586.6 2697.8 537 2697.9 474.6 2518.8 529.7 2518.8 586.6"/>
                                        <polygon id="sufit_F2" class="st64" points="2424.3 456.9 2610.7 403.8 2613.6 403.8 2697.8 474.5 2518.8 529.7 2424.3 456.9"/>
                                    </g>
                                    <polygon id="kolor_F2" class="pwe-halls__element-color" points="2613.6 403.8 2697.8 474.5 2697.8 525.2 2518.8 586.6 2424.3 517.3 2424.3 456.9 2610.7 403.8 2613.6 403.8"/>
                                    <path id="belka_F2" class="st65" d="M2697.8,474.5l-84.2-70.7h-3s3-7.9,8.2-4.7,85,72.2,85,72.2c0,0-.2,4.4-6.1,3.2h.1Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1820" y="1840"/>
                                    </a>
                                </g>

                                <g id="F1" class="pwe-halls__element quarter">
                                    <g id="obiekt_F1">
                                        <path id="bok_F1" class="st58" d="M2609.5,659l-90.7-72.4v-56.9l34.9,26.8,32.7,25.2,16.4,12.6,2,1.5s3.7,2.3,4.5,7.3.2,1.5.2,2.2v53.7h0Z"/>
                                        <path id="struktura_F1" class="st45" d="M2795.5,604.7l-186,54.3v-53.7s.5-5.4-4.6-9.4-86.1-66.2-86.1-66.2l179.2-55.2,94.8,85.6s4,3,3.9,8.9c.2,5.9-1.1,35.8-1.1,35.8h0Z"/>
                                    </g>
                                    <path id="kolor_F1" class="pwe-halls__element-color" d="M2796.6,569c0-5.8-3.9-8.9-3.9-8.9l-94.8-85.6-179.1,55.2s80.9,62.2,86,66.2l-86-66.2v56.9c.1,0,90.7,72.4,90.7,72.4l186-54.3s1.3-29.9,1.1-35.8h0ZM2609.4,605.3c0-2-.4-3.6-1-5,1.3,2.7,1,5,1,5ZM2608.1,599.8s.1.2.2.4c0,0-.1-.2-.2-.4Z"/>
                                    <path id="belka_F1" class="st65" d="M2795.5,604.7l3.7,2.7,4.8-2.7,1.2-38.1s0-5.6-4.3-10.1-96.9-85.1-96.9-85.1c0,0-5.9-1.7-6.1,3.2,3.5,3.2,71.7,64.9,71.7,64.9l23.1,20.7c2.8,2.4,3.9,5.9,3.9,8.9v9.9c-.1,0-1.1,25.9-1.1,25.9v-.2h0Z"/>
                                    <g id="numer_hali_F" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(2775.6 588.9)"><tspan x="0" y="0">F</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1840" y="2000"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="1750" y="1980"/>
                                </a>
                            </g>

                            <g id="F4_F3" class="pwe-halls__element half">
                                <g id="F4" class="pwe-halls__element quarter">
                                    <g id="obiekt_F4">
                                        <polygon id="bok_F4" class="st31" points="2301.9 643.1 2301.9 594.8 2227.5 531 2225.7 539.3 2227.5 579.2 2301.9 643.1"/>
                                        <polygon id="przod_wnetrze_F4" class="st58" points="2301.9 643.1 2518.9 586.6 2518.8 529.7 2301.9 594.8 2301.9 643.1"/>
                                        <polygon id="gora_F4" class="st43" points="2518.8 529.7 2424.3 456.9 2236.5 512.1 2227.5 531.1 2301.8 594.8 2518.8 529.7"/>
                                    </g>
                                    <polygon id="kolor_F4" class="pwe-halls__element-color" points="2424.3 456.9 2518.8 529.7 2518.8 586.6 2301.9 643.1 2227.6 579.3 2225.8 539.5 2227.5 531.1 2236.5 512.1 2424.3 456.9"/>
                                    <path id="belka_F4" class="st65" d="M2301.8,594.8l-74.3-63.8,9-19s.9-3.6-2.2-3.6-4.3,3.2-4.3,3.2l-11.1,22.6v42.3l4,4.4,4.6-1.7-1.8-39.9,1,.3,75,65.4.2-10.2h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1600" y="1840"/>
                                    </a>
                                </g>

                                <g id="F3" class="pwe-halls__element quarter">
                                    <g id="obiekt_F3">
                                        <path id="bok_F3" class="st44" d="M2388.3,718.4l-86.4-75.3v-48.2l90.7,77.6s5.2,4.5,5.2,10.9-9.4,35-9.4,35h-.1Z"/>
                                        <path id="struktura_F3" class="st59" d="M2396.5,721.5l1.2-36.2s.9-8-4.3-12-91.5-78.5-91.5-78.5l216.9-65.1,85.7,65.9s2.5,1.7,4,4.8,1,2.6,1,4.2v54.4l-213,62.5h0Z"/>
                                    </g>
                                    <path id="kolor_F3" class="pwe-halls__element-color" d="M2609.5,605.3c.2-6.6-4.6-9.4-4.6-9.4l-86.1-66.2-216.9,65.1v48.3l86.3,75.2,8.3,3.2,213-62.5v-53.8h0Z"/>
                                    <path id="belka_F3" class="st65" d="M2388,721.5l3.7,2.7,4.8-2.7,1.2-38.1s0-5.6-4.3-10.1-91.5-78.5-91.5-78.5c0,0-3.9,3.5-.2,10.2,3.5,3.2,60.5,51.2,60.5,51.2l23.1,20.7c2.8,2.4,3.9,5.9,3.9,8.9v9.9c-.1,0-1.1,25.9-1.1,25.9h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1620" y="2000"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="1530" y="1990"/>
                                </a>
                            </g>

                            <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link full">
                                <image class="pwe-halls__element-logo" href="" width="350" height="300" x="1620" y="1835"/>
                            </a>
                        </g>

                        <g id="E" class="pwe-halls__element full">
                            <g id="E2_E1" class="pwe-halls__element half">
                                <g id="E2" class="pwe-halls__element quarter">
                                    <g id="obiekt_E2">
                                        <polygon id="bok_E2" class="st58" points="1891 610.1 1970.5 678.7 1970.5 735.2 1891 660.2 1891 610.1"/>
                                        <polygon id="przod_E2" class="st58" points="1970.5 678.7 1970.5 735.2 2144.6 685.6 2144.6 630.1 1970.5 678.7"/>
                                        <polygon id="struktura_E2" class="st39" points="1970.5 678.7 2144.6 630.1 2081.1 573.5 2038.3 567.7 1891 610.1 1970.5 678.7"/>
                                    </g>
                                    <path id="kolor_E2" class="pwe-halls__element-color" d="M1970.5,678.7l174.1-48.5-63.5-56.6-42.8-5.8-147.3,42.4v49.1c0,1.2,79.4,75.9,79.4,75.9l174.2-49.6v-55.4c-.1,0-174.2,48.5-174.2,48.5h0Z"/>
                                    <path id="belka_E2" class="st36" d="M2144.6,630.1s6.1-.7,5.5-3.8c-2.7-2.3-58.7-51.8-64.6-57s-.7-.5-1.2-.5l-45.4-5.9s-8.3-.2-7.6,6.9l6.9-2,42.8,5.8,63.5,56.6h.1Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1210" y="1840"/>
                                    </a>
                                </g>

                                <g id="E1" class="pwe-halls__element quarter">
                                    <g id="obiekt_E1">
                                        <path id="bok_E1" class="st58" d="M2038.6,801.6l-68.1-66.4v-56.5l53.5,44.6s2.2,1.4,4.8,3.7,6,5.9,8.1,11.4,1.3,3.6,1.6,5.7.2,4.4.2,6.7v50.8h0Z"/>
                                        <path id="struktura_E1" class="st28" d="M2038.6,801.6l43.1-6.9,150.7-43.1,1.1-37.7v-1.3c-.2-1.1-.7-2.2-1.3-3l-1.1-1.5-86.5-78-174.1,48.6,40.1,33.4,13.3,11.1s7.9,5.3,10.3,9.8,3.8,7.1,4.3,11.7v56.6c0,0,0,0,0,0h0v.2Z"/>
                                    </g>
                                    <path id="kolor_E1" class="pwe-halls__element-color" d="M2233.4,712.7c-.1-1.1-.5-2.2-1.2-3l-1.1-1.5-86.5-78-174.1,48.5v56.5l68.1,66.4v-48.6,48.6l43.1-6.9,150.7-43.1,1.1-37.7v-1.3h-.1ZM2034.3,733.2s0,.2.2.3c-1.3-2-2.8-3.7-4.2-5.1,1.6,1.5,3.1,3.2,4,4.8ZM2038,741.6c.3,1,.5,2.1.6,3.3v.8c0-1.5-.3-2.9-.6-4.2h0Z"/>
                                    <path id="belka_E1" class="st36" d="M2144.6,630.1s.4-7.9,5.8-3.6,85.5,75.6,85.5,75.6c0,0,5.8,2.6,5.4,18.3s-.9,31.2-.9,31.2l-3.8,1.7-4.1-1.9,1.1-37.7s.6-3.1-7.4-10.3-81.5-73.5-81.5-73.5v.2h-.1Z"/>
                                    <g id="numer_hali_E" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(2215 735.4)"><tspan x="0" y="0">E</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1220" y="1990"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="1150" y="1930"/>
                                </a>
                            </g>

                            <g id="E4_E3" class="pwe-halls__element half">
                                <g id="E4" class="pwe-halls__element quarter">
                                    <g id="obiekt_E4">
                                        <polygon id="przod_wnetrze_E4" class="st58" points="1970.5 678.7 1970.5 735.2 1773.5 779.8 1773.5 732 1970.5 678.7"/>
                                        <polygon id="bok_E4" class="st26" points="1773.5 732 1773.5 779.8 1697.3 713.2 1697.3 666 1773.5 732"/>
                                        <polygon id="sufit_E4" class="st62" points="1891 610.1 1697.3 666 1773.5 732 1970.5 678.7 1891 610.1"/>
                                    </g>
                                    <polygon id="kolor_E4" class="pwe-halls__element-color" points="1970.5 735.1 1773.5 779.8 1697.3 713.2 1697.3 666 1891 610.1 1970.5 678.7 1970.5 735.1"/>
                                    <path id="belka_E4" class="st36" d="M1773.5,732v8.5l-76-65.2-.8.4,1.6,38.9-3.8,1.4-3.6-3.1-1.7-40.3s0-12.3,8.7-6,75.5,65.4,75.5,65.4h.1Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1000" y="1840"/>
                                    </a>
                                </g>

                                <g id="E3" class="pwe-halls__element quarter">
                                    <g id="struktura_E3">
                                        <path id="sciana_i_sufit_E3" class="st6" d="M1839.3,799.8l-1.4,30.5,200.7-28.7v-54.8s.2-8.6-6.9-17c-2-2.2-4.5-4.4-7.7-6.6-3.1-2.6-7.9-6.5-13.3-11.1-16.9-14.1-40.1-33.5-40.1-33.5l-197,53.5,58.1,50c5,4.6,7.7,11,7.7,17.8v-.2h0Z"/>
                                        <polygon id="SCIANA_BOK_E3" class="st34" points="1773.5 733.2 1773.5 779.8 1830.5 829.5 1832.3 788.1 1773.5 733.2"/>
                                    </g>
                                    <g id="kolor_E3">
                                        <path id="SUFIT_KOLOR_E3" class="pwe-halls__element-color" d="M2024,723.3c-12.8-10.7-53.5-44.6-53.5-44.6l-197,53.4,58.1,50c5,4.6,7.7,11,7.7,17.8l-1.4,30.5,200.7-28.6v-54.9s1-13-14.6-23.4v-.2h0Z"/>
                                        <polygon id="BOK_KOLOR_E3" class="pwe-halls__element-color" points="1773.5 779.8 1830.5 829.5 1832.3 788.1 1773.5 733.2 1773.5 779.8"/>
                                    </g>
                                    <path id="belka_bok_E3" class="st36" d="M1773.5,740.5l-1.5-4.5c-.4-1.2,0-2.6.9-3.5l.6-.5,58.9,49s8.6,6.1,6.8,19c0,13-.8,31-.8,31l-5.8,2.2-3.3-3.1,1.2-30.4c.2-4.7-1.7-9.3-5.2-12.5l-51.9-46.7h.1Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1020" y="1980"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="940" y="1920"/>
                                </a>
                            </g>

                            <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link full">
                                <image class="pwe-halls__element-logo" href="" width="330" height="290" x="1020" y="1830"/>
                            </a>
                        </g>

                        <g id="tunel_D">
                            <path class="st24" d="M1482.7,653.4l-5.2,1.3-8-7c-2.1-1.8-3.6-3-6.1-5.3l-.5-4.7,19.8-2.2v18h0Z"/>
                            <path id="Tunel_D" class="st7" d="M1481.3,632.2l-10.9-9.5c-.7-.6-1.6-.8-2.5-.6l-21,6.1,19.7,16.9,14-2.9c1.1-.3,1.9-1.3,2-2.5,0-1.7.2-4.1.2-5.2s-1.4-2.3-1.4-2.3h0Z"/>
                        </g>

                        <g id="D" class="pwe-halls__element full">
                            <g id="D3_D4" class="pwe-halls__element half">
                                <g id="D3" class="pwe-halls__element quarter">
                                    <g id="struktura_D3">
                                        <g>
                                        <path id="sufit_D3" class="st63" d="M1453,632.6l-89.9,23.6-175-150.6,82.6-24s2.7-.6,4.2-.4,3,.5,3.7,1.1c1.1.9,174.4,150.3,174.4,150.3h0Z"/>
                                        <polygon id="sciana_bok_D3" class="st57" points="1363.1 710.6 1190.1 558.2 1188.2 505.6 1363.1 656.2 1363.1 710.6"/>
                                        </g>
                                        <polygon id="sciana_przod_D3" class="st58" points="1453 632.6 1450.1 689.5 1363.1 710.6 1363.1 656.2 1453 632.6"/>
                                    </g>
                                    <path id="kolor_D3" class="pwe-halls__element-color" d="M1278.6,482.4l174.4,150.3-3,56.9-87,21.1h0l-173-152.4-1.9-52.6,82.5-23.9s2.5-.7,4.5-.5,2.2.4,3.4,1.1h0Z"/>
                                    <path id="belka_D3" class="st25" d="M1188.2,505.6l82.6-24s5-1.4,7.9.7c-.2-3.6,0-12.1-13.4-7.9s-77,22.5-77,22.5c0,0-4,2.1,0,8.7h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1480" y="-590"/>
                                    </a>
                                </g>

                                <g id="D4" class="pwe-halls__element quarter">
                                    <g id="Struktura_D4">
                                        <path id="przod_sciany_D4" class="st58" d="M1259.8,735.1l103.3-24.5v-54.4l-97.7,22.7s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,8.8.5,45.6.5,45.6h0Z"/>
                                        <path id="sciany_D4" class="st74" d="M1259.8,735.1l-.5-48.6s-.3-5.9,6.9-7.7c7.2-1.9,96.9-22.6,96.9-22.6l-174.8-150.4-106.8,31.8s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.8h0Z"/>
                                    </g>
                                    <path id="kolor_D4" class="pwe-halls__element-color" d="M1363.1,656.2s-56.9,13.3-83.9,19.5c26.4-6.2,83.9-19.6,83.9-19.5l-174.9-150.6-106.7,31.9s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.9h0c0,0,103.2-24.6,103.2-24.6v-54.3Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1460" y="-490"/>
                                    </a>
                                    <path id="Belka_D4" class="st25" d="M1070.7,575.7l5.3,4.5h4.4s-5.4-31.9-5.3-32.5-1.3-8.1,6.3-10.3,106.8-31.8,106.8-31.8c0,0,3.3-3.3,0-8.7-7.2,2-110.3,31.3-110.3,31.3,0,0-12.1,2.6-10.8,14.9s3.6,32.6,3.6,32.6Z"/>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="520" y="1430"/>
                                </a>
                            </g>

                            <g id="D1_D2" class="pwe-halls__element half">
                                <g id="D1" class="pwe-halls__element quarter">
                                    <g id="objekt_D1">
                                        <polygon id="sufit_D1" class="st63" points="1547.2 815.2 1636.8 791.8 1453 632.6 1363.1 656.2 1547.2 815.2"/>
                                        <polygon id="przod_D1" class="st12" points="1638 840.7 1548.1 867.1 1547.2 825.1 1638.7 798.1 1642 799.2 1640 839.7 1638 840.7"/>
                                        <polygon id="sciana_bok_D1" class="st50" points="1547.2 815.2 1547.2 825.1 1548.1 867.1 1363.2 710.6 1363.1 656.2 1547.2 815.2"/>
                                    </g>
                                    <path id="kolor_D1" class="pwe-halls__element-color" d="M1636.8,791.8l-89.7,23.5v2.4c-.2,1.1-.2,2.3,0,3.4v4l75.7-21.6,12.5-3.6s2.2-1.1,4.2-.8,1.4,3,1.4,3.4-3.8,38.5-3.8,38.5l-88.9,26.1-185-156.5v-54.4l90-23.5,183.8,159.2h-.2Z"/>
                                    <path id="belka_D1" class="st25" d="M1547.2,815.2l90.6-24.1s6.2-1.2,8.6,2.4,1.7,10.8,1.7,10.8l-2.8,36.3-3.6,1.7-4.8-1.2,3.9-39.4s1.2-4.6-5.6-1.8c-7.2,2.1-88.1,25.2-88.1,25.2l-.2-5v-4.8h.3Z"/>
                                    <g id="WEJSCIE_D1">
                                        <polygon class="st60" points="1548.1 867.1 1558.2 864 1564.6 849.2 1561.9 838.6 1552.8 835.5 1548 835.7 1547.4 837.4 1548.1 867.1"/>
                                        <path class="st25" d="M1547.3,831.8s10.2-2.7,15.3,3.2,6,13.7.9,22.7c-1.6,2.4-2.9,4.5-3.6,5.3s-2.6,1.3-3.6.3-.6-2.2,0-3.8,4.9-7.4,5.1-12.6c-.3-7.5-4-11.3-14-9.6v-5.6h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1790" y="-590"/>
                                    </a>
                                    <g id="numer_hali_D" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(1619.9 823.8)"><tspan x="0" y="0">D</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                </g>

                                <g id="D2" class="pwe-halls__element quarter">
                                    <g id="struktura_D2">
                                        <path id="przod_sciany_D2" class="st48" d="M1548.1,867.1l-5.8-17.6,5.1-17.6-.2-6.7-91,24.6s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,6.9,1.9,23.6,2.6,30.5.2,1.9.3,3.3.3,3.3l95.1-26.9h0Z"/>
                                        <path id="sciany_D2" class="st69" d="M1447.9,895.7l-5.2-30.2c-.8-4.5-.4-9.2,1.2-13.5s3.1-5.9,6.1-6.7c7.2-1.8,97.1-30.1,97.1-30.1l-184-159-35.2,8.2-59.5,13.8s-5.7.8-7.8,4.3c-2,2.7-1.5,7.2-1.4,11,0,8.7.5,41.4.5,41.4l188.1,160.7h.1Z"/>
                                    </g>
                                    <g id="kolor_D2">
                                        <path id="BOK_d2" class="pwe-halls__element-color" d="M1448.3,846c.3,0,1.4-.5,3.6-1.2,18.8-5.5,95.2-29.6,95.2-29.6h0l-184.1-159-94.6,22c-3.3.6-9.2,2.4-9.2,15.5.2,11.4.5,41.2.5,41.2l188.1,160.7-4.4-25.5c-.4-2.1-1.3-10.1-1-12.2.8-5.8,2.3-9,5.8-11.9h0Z"/>
                                        <path id="przod_D2" class="pwe-halls__element-color" d="M1542.3,849.5l5.1-17.6v-1.9l-.2-4.8s-86.8,23.4-91.7,24.8c-6.7,2.3-5.7,10.3-5.7,10.3h0c0,1.7,3.2,33.7,3.2,33.7l95.1-26.9-5.8-17.6Z"/>
                                    </g>
                                    <path id="belka_D2" class="st25" d="M1448.7,896.6c-1.5-1.5-2.9-2.5-3.7-3.4-.8-7.2-3.7-34-2-37.7.6-1.8,1.3-5.2,2.9-7.1s5.3-3.4,5.3-3.4l91.1-28.3c2.5-.8,5-1.5,5-1.5v9.9l-2.8.8-88.3,24c-.4,0-1.4.4-2.6,1.3-1.7,1.2-2.8,3.2-3.1,5.3,0,0-.4,2.4-.4,5.1l2.9,33c-2.4,1.1-4.3,2.1-4.3,2.1h0Z"/>
                                    <g id="Wejscie_D2">
                                        <path class="st60" d="M1547.4,837.4l.6,29.7-9.1,2.6s-9.4-10.9-7-18,7.3-13.1,11.2-14.6,4.2.2,4.2.2h.1Z"/>
                                        <path class="st25" d="M1539.5,869.5s-2.8,1.4-5.6-1.6-6.6-11-4-19c3.2-13,17.4-17.1,17.4-17.1v5.6c-1.5.5-8.1,4-11.2,9.7v.2c-3,5.6-2,12.4,2.1,17.3s2.9,3.2,1.4,4.9h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1780" y="-490"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="540" y="1750"/>
                                </a>
                            </g>

                            <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link full">
                                <image class="pwe-halls__element-logo" href="" width="350" height="200" x="1530" y="-580"/>
                            </a>
                        </g>

                        <g id="wejscie_bok_d">
                            <polygon class="st24" points="1272.2 745.6 1255.7 750.5 1241.6 738.3 1258.1 733.4 1272.2 745.6"/>
                            <polygon class="st60" points="1272.2 745.6 1257.9 733.3 1257.8 702.5 1272.1 714.4 1272.2 745.6"/>
                            <path class="st35" d="M1258.7,748.3c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5h0Z"/>
                            <path class="st27" d="M1247.1,739.8c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5h0Z"/>
                        </g>

                        <g id="tunel_c_d">
                            <path class="st24" d="M1256.5,721.5l13.5-4.4c1.3-.4,2.2-1.8,2.2-3.4l-.2-13.4v-9l-15.4.9v29.3h0Z"/>
                            <path class="st24" d="M1261.4,720.1l-78,23.5-13.9-12.1c-2.1-1.8-2-1.7-3.5-3l3.1-2.2v-7.9l92.2-27.7v29.4h.1Z"/>
                            <path class="st18" d="M1255,680.1l-108.4,31.7c7.4,6.4,19.3,16.7,19.3,16.7l106-31.1v-4.2c0-1.2.2-4-1.8-5.2l-15.2-7.9h0Z"/>
                        </g>

                        <g id="C" class="pwe-halls__element full">
                            <g id="C3_C4" class="pwe-halls__element half">
                                <g id="C3" class="pwe-halls__element quarter">
                                    <g id="struktura_C3">
                                        <g id="sufit_i_bok_C3">
                                        <path id="sufit_c3" class="st63" d="M1156.9,720.7l-89.9,23.7-175-150.7,82.6-24s2.7-.6,4.2-.4,3,.5,3.7,1.1c1.1.9,174.4,150.3,174.4,150.3h0Z"/>
                                        <polygon id="sciana_bok_C3" class="st21" points="1067 798.7 894 646.3 892.1 593.7 1067 744.4 1067 798.7"/>
                                        </g>
                                        <polygon id="sciana_przod_C3" class="st58" points="1156.9 720.7 1154 777.5 1067 798.7 1067 744.4 1156.9 720.7"/>
                                    </g>
                                    <path id="kolor_C3" class="pwe-halls__element-color" d="M982.5,570.4l174.4,150.3-3,56.9-87,21.1h0l-173-152.4-1.9-52.6,82.5-23.9s2.5-.7,4.5-.5,2.2.4,3.4,1.1h0Z"/>
                                    <path id="belka_C3" class="st0" d="M892.1,593.7l82.6-24s5-1.4,7.9.7c-.2-3.6,0-12.1-13.4-7.9s-77,22.5-77,22.5c0,0-4,2.1,0,8.7h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1450" y="-260"/>
                                    </a>
                                </g>

                                <g id="C4" class="pwe-halls__element quarter">
                                    <g id="Struktura_C4">
                                        <path id="przod_sciany_C4" class="st58" d="M963.7,823.2l103.3-24.5v-54.3l-97.7,22.6s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,8.8.5,45.6.5,45.6h0Z"/>
                                        <path id="sciany_C4" class="st54" d="M963.7,823.2l-.5-48.6s-.3-5.9,6.9-7.7,96.9-22.5,96.9-22.5l-174.8-150.5-106.8,31.8s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.8h-.1Z"/>
                                    </g>
                                    <path id="kolor_C4" class="pwe-halls__element-color" d="M1067,744.4l-174.9-150.7-106.7,31.9s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.9h0c0,0,103.2-24.6,103.2-24.6v-54.2h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1410" y="-160"/>
                                    </a>
                                    <path id="Belka_C4" class="st0" d="M774.6,663.8l5.3,4.5h4.4s-5.4-31.9-5.3-32.5-1.3-8.1,6.3-10.3,106.8-31.8,106.8-31.8c0,0,3.3-3.3,0-8.7-7.2,2-110.3,31.3-110.3,31.3,0,0-12.1,2.6-10.8,14.9s3.6,32.6,3.6,32.6Z"/>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="180" y="1420"/>
                                </a>
                            </g>

                            <g id="C1_C2" class="pwe-halls__element half">
                                <g id="C1" class="pwe-halls__element quarter">
                                    <g id="objekt_C1">
                                        <polygon id="sufit_C1" class="st63" points="1251.1 903.3 1340.7 879.8 1156.9 720.7 1067 744.4 1251.1 903.3"/>
                                        <polygon id="przod_C1" class="st37" points="1341.9 928.8 1252 955.1 1251.1 913.1 1342.6 886.1 1345.9 887.2 1343.9 927.8 1341.9 928.8"/>
                                        <polygon id="sciana_bok_C1" class="st73" points="1251.1 903.3 1251.1 913.1 1252 955.1 1067 798.6 1067 744.4 1251.1 903.3"/>
                                    </g>
                                    <path id="kolor_C1" class="pwe-halls__element-color" d="M1340.7,879.8l-89.7,23.5v2.4c-.2,1.1-.2,2.3,0,3.4v4l75.7-21.6,12.5-3.6s2.2-1.1,4.2-.8,1.4,3,1.4,3.4-3.8,38.5-3.8,38.5l-88.9,26.1-185-156.5v-54.2l90-23.7,183.8,159.2h-.2Z"/>
                                    <path id="belka_C1" class="st0" d="M1251.1,903.3l90.6-24.1s6.2-1.2,8.6,2.4,1.7,10.8,1.7,10.8l-2.8,36.3-3.6,1.7-4.8-1.2,3.9-39.4s1.2-4.6-5.6-1.8c-7.2,2.1-88.1,25.2-88.1,25.2l-.2-5v-4.8h.3Z"/>
                                    <g id="WEJSCIE_C1">
                                        <polygon class="st60" points="1252 955.1 1262.1 952 1268.5 937.2 1265.8 926.6 1256.7 923.5 1251.9 923.8 1251.3 925.4 1252 955.1"/>
                                        <path class="st0" d="M1251.2,919.9s10.2-2.7,15.3,3.2,6,13.7.9,22.7c-1.6,2.4-2.9,4.5-3.6,5.3s-2.6,1.3-3.6.3-.6-2.2,0-3.8,4.9-7.4,5.1-12.6c-.3-7.5-4-11.3-14-9.6v-5.6h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1760" y="-255"/>
                                    </a>
                                    <g id="numer_hali_C" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(1323.8 911.8)"><tspan x="0" y="0">C</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                </g>

                                <g id="C2" class="pwe-halls__element quarter">
                                    <g id="struktura_C2">
                                        <path id="przod_sciany_C2" class="st55" d="M1252,955.1l-5.8-17.6,5.1-17.6-.2-6.7-91,24.6s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,6.9,1.9,23.6,2.6,30.5.2,1.9.3,3.3.3,3.3l95.1-26.9h0Z"/>
                                        <path id="sciany_C2" class="st51" d="M1151.8,983.8l-5.2-30.2c-.8-4.5-.4-9.2,1.2-13.5s3.1-5.9,6.1-6.7c7.2-1.8,97.1-30.1,97.1-30.1l-184-158.9-47.9,11.1-46.6,10.8s-3,.5-5.1,1.6c-1.3.7-2.6,1.8-3.1,2.8-1.7,2.2-1.3,7.1-1.2,10.9,0,8.7.5,41.4.5,41.4l188.1,160.7h0Z"/>
                                    </g>
                                    <g id="kolor_C2">
                                        <path id="sciany_C21" class="pwe-halls__element-color" d="M1251.1,903.3l-184.1-158.9-94.6,21.8c-3.3.7-9.2,2.5-9.2,15.6.2,11.4.5,41.2.5,41.2l188.1,160.7-4.4-25.5c-.4-2.1-1.3-10.1-1-12.2.8-5.8,2.3-9,5.8-11.9.3,0,1.4-.5,3.6-1.2,18.8-5.5,95.2-29.6,95.2-29.6h0Z"/>
                                        <path id="przod_C2" class="pwe-halls__element-color" d="M1153.7,948.3c0,1.8,3.2,33.8,3.2,33.8l95.1-26.9-5.8-17.6,5.1-17.6v-1.9l-.2-4.8s-86.8,23.4-91.7,24.8c-6.7,2.3-5.7,10.3-5.7,10.3h0Z"/>
                                    </g>
                                    <path id="belka_C2" class="st0" d="M1152.6,984.7c-1.5-1.5-2.9-2.5-3.7-3.4-.8-7.2-3.7-34-2-37.7.6-1.8,1.3-5.2,2.9-7.1s5.3-3.4,5.3-3.4l91.1-28.3c2.5-.8,5-1.5,5-1.5v9.9l-2.8.8-88.3,24c-.4,0-1.4.4-2.6,1.3-1.7,1.2-2.8,3.2-3.1,5.3,0,0-.4,2.4-.4,5.1l2.9,33c-2.4,1.1-4.3,2.1-4.3,2.1h0Z"/>
                                    <g id="Wejscie_C2">
                                        <path class="st60" d="M1251.3,925.4l.6,29.7-9.1,2.6s-9.4-10.9-7-18,7.3-13.1,11.2-14.6,4.2.2,4.2.2h.1Z"/>
                                        <path class="st0" d="M1243.4,957.6s-2.8,1.4-5.6-1.6-6.6-11-4-19c3.2-13,17.4-17.1,17.4-17.1v5.6c-1.5.5-8.1,4-11.2,9.7v.2c-3,5.6-2,12.4,2.1,17.3s2.9,3.2,1.4,4.9h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1740" y="-155"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="200" y="1740"/>
                                </a>
                            </g>

                            <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link full">
                                <image class="pwe-halls__element-logo" href="" width="350" height="200" x="1480" y="-250"/>
                            </a>
                        </g>

                        <g id="wejscie_bok_c">
                            <polygon class="st24" points="973.4 831.5 956.9 836.3 942.8 824.1 959.3 819.3 973.4 831.5"/>
                            <polygon class="st60" points="973.4 831.5 959.1 819.2 959.1 788.4 973.3 800.3 973.4 831.5"/>
                            <path class="st41" d="M959.9,834.2c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5Z"/>
                            <path class="st56" d="M948.3,825.7c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5Z"/>
                        </g>

                        <g id="tunel_b_c">
                            <path class="st24" d="M957.8,812.4l13.5-4.4c1.3-.4,2.2-1.8,2.2-3.4l-.2-13.4v-9l-15.4.9v29.3h0Z"/>
                            <path class="st24" d="M962.7,811l-73.6,21.8-14.9-12.2c-2-1.8-2.1-1.8-3.5-3.2l-.2-.2v-7.9l92.2-27.7v29.4h0Z"/>
                            <path class="st2" d="M956.3,771l-104.2,29.9c7.4,6.4,18.2,16.3,18.2,16.3l102.9-28.9v-4.2c0-1.2.2-4-1.8-5.2l-15.2-7.9h0Z"/>
                        </g>

                        <g id="B" class="pwe-halls__element full">
                            <g id="B3_B4" class="pwe-halls__element half">
                                <g id="B3" class="pwe-halls__element quarter">
                                    <g id="struktura_B3">
                                        <g>
                                        <path id="sufit_B3" class="st63" d="M860,807.7l-90,23.7-174.9-150.7,82.6-24s2.7-.6,4.2-.4,3,.5,3.7,1.1c1.1.9,174.4,150.3,174.4,150.3h0Z"/>
                                        <polygon id="sciana_bok_B3" class="st14" points="770 885.8 597.1 733.3 595.1 680.8 770 831.4 770 885.8"/>
                                        </g>
                                        <polygon id="sciana_przod_B3" class="st58" points="860 807.7 857 864.6 770 885.8 770 831.4 860 807.7"/>
                                    </g>
                                    <path id="kolor_B3" class="pwe-halls__element-color" d="M685.6,657.5l174.4,150.3-3,56.9-87,21.1v-54.4,54.4l-173-152.4-1.9-52.6,82.5-23.9s2.5-.7,4.5-.5,2.2.4,3.4,1.1h0Z"/>
                                    <path id="belka_B3" class="st33" d="M595.1,680.8l82.6-24s5-1.4,7.9.7c-.2-3.6,0-12.1-13.4-7.9s-77,22.5-77,22.5c0,0-4,2.1,0,8.7h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1400" y="75"/>
                                    </a>
                                </g>

                                <g id="B4" class="pwe-halls__element quarter">
                                    <g id="Struktura_B4">
                                        <path id="przod_sciany_B4" class="st58" d="M666.7,910.3l103.3-24.5v-54.4l-97.7,22.7s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,8.8.5,45.6.5,45.6h0Z"/>
                                        <path id="sciany_B4" class="st47" d="M666.7,910.3l-.5-48.6s-.3-5.8,6.9-7.7c6.8-1.8,87.1-20.4,96.1-22.4s.8-.2.8-.2l-174.8-150.4-106.8,31.8s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.8h-.1Z"/>
                                    </g>
                                    <path id="kolor_B4" class="pwe-halls__element-color" d="M770,831.4l-174.9-150.6-106.7,31.9s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.9h0c0,0,103.2-24.6,103.2-24.6v-54.3h0ZM770,831.4s-19.3,4.5-40.8,9.4c21.4-5,40.7-9.5,40.7-9.4h0Z"/>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1380" y="175"/>
                                    </a>
                                    <path id="Belka_B4" class="st33" d="M477.6,750.9l5.3,4.5h4.4s-5.4-31.9-5.3-32.5-1.3-8.1,6.3-10.3,106.8-31.8,106.8-31.8c0,0,3.3-3.3,0-8.7-7.2,2-110.3,31.3-110.3,31.3,0,0-12.1,2.6-10.8,14.9s3.6,32.6,3.6,32.6Z"/>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="-150" y="1400"/>
                                </a>
                            </g>

                            <g id="B1_B2" class="pwe-halls__element half">
                                <g id="B1" class="pwe-halls__element quarter">
                                    <g id="objekt_B1">
                                        <polygon id="sufit_B1" class="st63" points="954.1 990.4 1043.8 966.9 860 807.7 770 831.4 954.1 990.4"/>
                                        <polygon id="przod_B1" class="st16" points="1044.9 1015.8 955 1042.2 954.1 1000.2 1045.6 973.2 1049 974.3 1047 1014.8 1044.9 1015.8"/>
                                        <polygon id="sciana_bok_B1" class="st20" points="954.1 990.4 954.1 1000.2 955 1042.2 769.8 885.8 770 831.4 954.1 990.4"/>
                                    </g>
                                    <path id="kolor_B1" class="pwe-halls__element-color" d="M1043.8,966.9l-89.7,23.5v2.4c-.2,1.1-.2,2.3,0,3.4v4l75.7-21.6,12.5-3.6s2.2-1.1,4.2-.8,1.4,3,1.4,3.4-3.8,38.5-3.8,38.5l-88.9,26.1-185-156.5-.2-54.3,90.2-23.6,183.8,159.2h-.2Z"/>
                                    <path id="belka_B1" class="st33" d="M954.1,990.4l90.6-24.1s6.2-1.2,8.6,2.4,1.7,10.8,1.7,10.8l-2.8,36.3-3.6,1.7-4.8-1.2,3.9-39.4s1.2-4.6-5.6-1.8c-7.2,2.1-88.1,25.2-88.1,25.2l-.2-5v-4.8h.3Z"/>
                                    <g id="WEJSCIE_B1">
                                        <polygon class="st60" points="955 1042.2 965.2 1039.1 971.5 1024.3 968.8 1013.7 959.7 1010.6 955 1010.9 954.4 1012.4 955 1042.2"/>
                                        <path class="st33" d="M954.3,1006.9s10.2-2.7,15.3,3.2,6,13.7.9,22.7c-1.6,2.4-2.9,4.5-3.6,5.3s-2.6,1.3-3.6.3-.6-2.2,0-3.8,4.9-7.4,5.1-12.6c-.3-7.5-4-11.3-14-9.6v-5.6h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1720" y="75"/>
                                    </a>
                                    <g id="numer_hali_B" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(1026.8 998.9)"><tspan x="0" y="0">B</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                </g>

                                <g id="B2" class="pwe-halls__element quarter">
                                    <g id="struktura_B2">
                                        <path id="przod_sciany_b2" class="st5" d="M955.1,1042.2l-5.9-17.6,5.1-17.6-.2-6.7-91,24.6s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,6.9,1.9,23.6,2.6,30.5.2,1.9.3,3.3.3,3.3l95.1-27h.1Z"/>
                                        <path id="sciany_b2" class="st49" d="M854.8,1070.9l-5.2-30.2c-.8-4.5-.4-9.2,1.2-13.5s3.1-5.9,6.1-6.7c7.2-1.8,97.1-30.1,97.1-30.1l-184-159-5.7,1.3-37,8.6-36.5,8.5-17.9,4.2s-3.7.9-5.3,3.5c-2,2.7-1.6,7.4-1.5,11.2,0,8.7.5,41.4.5,41.4l188.1,160.7h.1Z"/>
                                    </g>
                                    <g id="kolor_B2">
                                        <path id="sciany_B2" class="pwe-halls__element-color" d="M954.1,990.4l-184.1-159-94.6,21.9c-3.3.7-9.2,2.5-9.2,15.6.2,11.4.5,41.2.5,41.2l188.1,160.7-4.4-25.5c-.4-2.1-1.3-10.1-1-12.2.8-5.8,2.3-9,5.8-11.9.3,0,1.4-.5,3.6-1.2,18.8-5.5,95.2-29.6,95.2-29.6h0Z"/>
                                        <path id="przod_B2" class="pwe-halls__element-color" d="M856.8,1035.3c0,1.8,3.2,33.8,3.2,33.8l95-26.9-5.7-17.6,5.1-17.6v-1.9l-.2-4.8s-86.8,23.4-91.7,24.8c-6.7,2.3-5.7,10.3-5.7,10.3h0Z"/>
                                    </g>
                                    <path id="belka_b2" class="st33" d="M855.6,1071.8c-1.5-1.5-2.9-2.5-3.7-3.4-.8-7.2-3.7-34-2-37.7.6-1.8,1.3-5.2,2.9-7.1,2.2-2.6,5.3-3.4,5.3-3.4l91.1-28.3c2.5-.8,5-1.5,5-1.5v9.9l-2.8.8-88.3,24c-.4,0-1.4.4-2.6,1.3-1.7,1.2-2.8,3.2-3.1,5.3,0,0-.4,2.4-.4,5.1l2.9,33c-2.4,1.1-4.3,2.1-4.3,2.1h0Z"/>
                                    <g id="Wejscie_B2">
                                        <path class="st60" d="M954.4,1012.4l.7,29.8-9.2,2.6s-9.4-10.9-7-18,7.3-13.1,11.2-14.6,4.2.2,4.2.2h0Z"/>
                                        <path class="st33" d="M946.5,1044.6s-2.8,1.4-5.6-1.6-6.6-11-4-19c3.2-13,17.5-17.1,17.5-17.1v5.5c-1.5.5-8.2,4.1-11.3,9.8v.2c-3,5.6-2,12.4,2.1,17.3s2.9,3.2,1.4,4.9h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1710" y="175"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="-124" y="1730"/>
                                </a>
                            </g>

                            <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link full">
                                <image class="pwe-halls__element-logo" href="" width="350" height="200" x="1430" y="80"/>
                            </a>
                        </g>

                        <g id="wejscie_bok_b">
                            <polygon class="st24" points="678.7 920.3 662.2 925.1 648.1 912.9 664.6 908.1 678.7 920.3"/>
                            <polygon class="st60" points="678.7 920.3 664.4 908 664.3 877.2 678.6 889.1 678.7 920.3"/>
                            <path class="st15" d="M665.2,923c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5Z"/>
                            <path class="st53" d="M653.6,914.5c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5Z"/>
                        </g>

                        <g id="tunel_a_b">
                            <path class="st24" d="M663,896.2l13.5-4.4c1.3-.4,2.2-1.8,2.2-3.4l-.2-13.4v-9l-15.4.9v29.3h0Z"/>
                            <path class="st24" d="M667.9,894.8l-74.2,22-14.1-12.2-4-3.6v-7.9l92.2-27.7v29.4h0Z"/>
                            <path class="st38" d="M661.5,854.9l-104.7,30.1c7.4,6.4,21.1,18.2,21.1,18.2l100.7-31v-4.2c0-1.2.2-4-1.8-5.2l-15.2-7.9h0Z"/>
                        </g>

                        <g id="A" class="pwe-halls__element full">
                            <g id="A3_A4" class="pwe-halls__element half">
                                <g id="A3" class="pwe-halls__element quarter">
                                    <g id="struktura_A3">
                                        <g id="bok_i_sufit_A3">
                                            <path id="sufit_A3" class="st63" d="M566.8,893.6l-89.9,23.6-175-150.6,82.6-24s2.7-.6,4.2-.4,3,.5,3.7,1.1c1.1.9,174.4,150.3,174.4,150.3h0Z"/>
                                            <polygon id="sciana_bok_A3" class="st3" points="476.9 971.6 303.9 819.2 302 766.6 476.9 917.2 476.9 971.6"/>
                                        </g>
                                        <polygon id="sciana_przod_A3" class="st58" points="566.8 893.6 563.9 950.5 476.9 971.6 476.9 917.2 566.8 893.6"/>
                                    </g>
                                    <path id="kolor_A3" class="pwe-halls__element-color" d="M392.4,743.3l174.4,150.3-3,56.9-87,21.1v-54.4,54.4l-173-152.4-1.9-52.6,82.5-23.9s2.5-.7,4.5-.5,2.2.4,3.4,1.1h.1Z"/>
                                    <path id="belka_A3" class="st65" d="M302,766.6l82.6-24s5-1.4,7.9.7c-.2-3.6,0-12.1-13.4-7.9s-77,22.5-77,22.5c0,0-4,2.1,0,8.7h-.1Z"/>
                                </g>

                                <g id="A4" class="pwe-halls__element quarter">
                                    <g id="Struktura_A4">
                                        <path id="przod_sciany_A4" class="st58" d="M373.6,996.1l103.3-24.5v-54.4l-97.7,22.7s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,8.8.5,45.6.5,45.6h0Z"/>
                                        <path id="sciany_A4" class="st61" d="M373.6,996.1l-.5-48.6s-.3-5.9,6.9-7.7,96.9-22.6,96.9-22.6l-174.8-150.4-106.8,31.8s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.8h-.1,0Z"/>
                                    </g>
                                    <path id="kolor_A31" class="pwe-halls__element-color" d="M477,917.2s-92.7,21.4-97.8,22.6c-1.9.6-3.2,1.5-4.1,2.5,1.4-1.6,3-2.4,5.4-2.9,4.9-1,96-22.1,96-22.1h.4l-174.9-150.7-106.7,31.9s-8.3.9-6,12,5,30.7,5,30.7l179.4,154.9h0c0,0,103.3-24.5,103.3-24.5v-54.4h0Z"/>
                                    <path id="Belka_A4" class="st65" d="M184.5,836.7l5.3,4.5h4.4s-5.4-31.9-5.3-32.5-1.3-8.1,6.3-10.3,106.8-31.8,106.8-31.8c0,0,3.3-3.3,0-8.7-7.2,2-110.3,31.3-110.3,31.3,0,0-12.1,2.6-10.8,14.9s3.6,32.6,3.6,32.6Z"/>
                                </g>
                            </g>

                            <g id="A1_A2" class="pwe-halls__element half">
                                <g id="A1" class="pwe-halls__element quarter">
                                    <g id="objekt_A1">
                                        <polygon id="sufit_A1" class="st63" points="661 1076.2 750.6 1052.7 566.8 893.6 476.9 917.2 661 1076.2"/>
                                        <polygon id="przod_A1" class="st68" points="751.7 1101.7 661.8 1128.1 661 1086.1 752.4 1059.1 755.7 1060.2 753.7 1100.7 751.7 1101.7"/>
                                        <polygon id="sciana_bok_A1" class="st70" points="661 1076.2 661 1086.1 661.9 1128.1 476.9 971.5 476.9 917.2 661 1076.2"/>
                                    </g>
                                    <path id="kolor_A1" class="pwe-halls__element-color" d="M750.6,1052.7l-89.6,23.5v2.4c-.3,1.1-.3,2.3-.2,3.4v4l75.7-21.6,12.5-3.6s2.2-1.1,4.2-.8,1.4,3,1.4,3.4-3.8,38.5-3.8,38.5l-88.9,26.1-185-156.5v-54.3l90-23.6,183.8,159.2h-.1Z"/>
                                    <path id="belka_A1" class="st65" d="M661,1076.2l90.6-24.1s6.2-1.2,8.6,2.4,1.7,10.8,1.7,10.8l-2.8,36.3-3.6,1.7-4.8-1.2,3.9-39.4s1.2-4.6-5.6-1.8c-7.2,2.1-88,25.2-88,25.2l-.3-5v-4.8h.3,0Z"/>
                                    <g id="WEJSCIE_A1">
                                        <polygon class="st60" points="661.9 1128.1 671.9 1125.1 678.4 1110.2 675.7 1099.5 666.6 1096.4 661.8 1096.7 661.2 1098.2 661.9 1128.1"/>
                                        <path class="st65" d="M661.4,1092.7s9.9-2.6,15,3.3,6,13.7.9,22.7c-1.6,2.4-2.9,4.5-3.6,5.3s-2.6,1.3-3.6.3-.6-2.2,0-3.8,4.9-7.4,5.1-12.6c-.3-7.5-4-11.3-14-9.6v-5.5h.2,0Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1680" y="400"/>
                                    </a>
                                    <g id="numer_hali_A" class="st9">
                                        <g class="st9">
                                            <g class="st9">
                                                <text class="st8" transform="translate(733.7 1084.8)"><tspan x="0" y="0">A</tspan></text>
                                            </g>
                                        </g>
                                    </g>
                                </g>

                                <g id="A2" class="pwe-halls__element quarter">
                                    <g id="struktura_A2">
                                        <path id="przod_sciany_A2" class="st23" d="M661.9,1128.1l-5.8-17.6,5-17.7v-6.7l-91.1,24.7s-3.1.7-5,3.6c-1.4,2.4-1.1,3.9-1.1,6.9,0,6.9,1.9,23.6,2.6,30.5.2,1.9.3,3.3.3,3.3l94.8-26.9h.3Z"/>
                                        <path id="sciany_A2" class="st11" d="M561.7,1156.7l-5.2-30.2c-.8-4.5-.4-9.2,1.2-13.5s3.1-5.9,6.1-6.7c7.2-1.8,97.2-30.1,97.2-30.1l-184.1-159-94.7,22s-2,.3-3.9,1.1-2.9,1.4-3.5,2.4c-.4.5-.7.9-1,1.6s-.5,1.3-.7,2c-.5,2.6-.3,5.8-.2,8.2,0,8.7.5,41.4.5,41.4l188.1,160.7h.2Z"/>
                                    </g>
                                    <g id="kolor_A2">
                                        <path id="przod_A2" class="pwe-halls__element-color" d="M563.6,1121.2c0,1.8,3.2,33.8,3.2,33.8l95.1-26.9-5.8-17.6,5.1-17.6v-1.9l-.2-4.8s-86.8,23.4-91.7,24.8c-6.7,2.3-5.7,10.3-5.7,10.3h0Z"/>
                                        <path id="SUFIT_A2" class="pwe-halls__element-color" d="M661,1076.2l-96,29.8s-5.1,1.8-6.6,5.7-2.1,6.9-2.2,8.1.6,12.7.6,12.7l2.1,21.7-185.3-158.3-.4-48.8s.3-2.5,1.1-3.5,1.9-2.6,3.7-3.2c1.8-.6,5.6-1.5,5.6-1.5l15.1-3.5,78.4-18.1,184.1,159h-.2Z"/>
                                    </g>
                                    <path id="belka_A2" class="st65" d="M562.5,1157.6c-1.5-1.5-2.9-2.5-3.7-3.4-.8-7.2-3.7-34-2-37.7.6-1.8,1.3-5.2,2.9-7.1s5.3-3.4,5.3-3.4l91.1-28.3c2.5-.8,4.9-1.5,4.9-1.5v9.9l-2.7.8-88.3,24c-.4,0-1.4.4-2.6,1.3-1.7,1.2-2.8,3.2-3.1,5.3,0,0-.4,2.4-.4,5.1l2.9,33c-2.4,1.1-4.3,2.1-4.3,2.1h0Z"/>
                                    <g id="Wejscie_A2">
                                        <path class="st60" d="M661.2,1098.2l.7,29.9-9.2,2.6s-9.4-10.9-7-18,7.3-13.1,11.2-14.6,4.2.2,4.2.2h0Z"/>
                                        <path class="st65" d="M653.3,1130.5s-2.8,1.4-5.6-1.6-6.6-11-4-19c3.2-13,17.5-17.1,17.5-17.1v5.5c-1.5.5-8.2,4.1-11.3,9.8v.2c-3,5.6-2,12.4,2.1,17.3s2.9,3.2,1.4,4.9h-.1Z"/>
                                    </g>
                                    <a target="_blank" class="pwe-halls__link pwe-halls__element-logo-link quarter">
                                        <image class="pwe-halls__element-logo" href="" width="175" height="100" x="1670" y="500"/>
                                    </a>
                                </g>

                                <a target="_blank" class="pwe-halls__link pwe-halls__element-favicon-link half">
                                    <image class="pwe-halls__element-favicon" href="" width="140" height="140" x="-450" y="1700"/>
                                </a>
                            </g>

                        </g>

                        <g id="wejscie_bok_A">
                            <polygon class="st24" points="382.8 1004.5 366.3 1009.3 352.2 997.1 368.7 992.3 382.8 1004.5"/>
                            <polygon class="st60" points="382.8 1004.5 368.5 992.2 368.5 961.4 382.7 973.3 382.8 1004.5"/>
                            <path class="st29" d="M369.3,1007.2c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5h0Z"/>
                            <path class="st66" d="M357.7,998.7c-.6,0-1.7-.2-1.7-1.5v-37.5h3.9v37.5c0,1.3-1.4,1.6-2.2,1.5h0Z"/>
                        </g>

                        <g id="tunel_A">
                            <path class="st24" d="M363.1,982.4l4.1-1.1,13.6-3.8c1.1-.3,1.8-1.3,1.8-2.4v-18.4l-18.3-1.7v23.5c0,2.8-1.3,3.9-1.3,3.9h0Z"/>
                            <path class="st71" d="M361.8,982.1l-10-8.3c-.9-.8-1.5-1.9-1.5-3.1v-20.1c.3-3,4.2-2.8,5.1-3.7l7,6.4c1.2,1.1,2,2.7,2,4.3v23.3c0,1.3-1.6,2.1-2.6,1.2h0Z"/>
                            <path class="st19" d="M369.2,944.9l-11.5,1.2s-3.4.6-4,.8c-4,1.4-3.2,4.7-3.2,4.7,0,0,0-4,1.9-2.3.9.5,2.4,1.5,2.4,1.5l8.4,5.3c1.8,2,1.4,7,1.5,7.7v.3c0,1.8,1.8,3,3.5,2.5l14.6-5.7v-6.1c0-1,0-3.2-.9-3.7l-12.6-6.1h0Z"/>
                        </g>

                    </svg>
                </div>

            </div>

            

        </div>
    </div>';
    
} else { $output = '<style>.row-container:has(#pweHalls) {display: none !important;}</style>'; }

return $output;
