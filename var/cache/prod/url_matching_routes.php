<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/ads' => [
            [['_route' => 'app_ads_list', '_controller' => 'App\\Controller\\AdsController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'app_ads_create', '_controller' => 'App\\Controller\\AdsController::create'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/ads/([^/]++)(*:24)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        24 => [
            [['_route' => 'app_ads_view', '_controller' => 'App\\Controller\\AdsController::view'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
