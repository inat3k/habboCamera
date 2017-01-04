<?php

if (!defined('RUNNING_DEFINED')) {
    die('Access Denied');
}

/**
 *            __   __   __   __              ___  __
 * |__|  /\  |__) |__) /  \ /  `  /\   |\/| |__  |__)  /\
 * |  | /~~\ |__) |__) \__/ \__, /~~\  |  | |___ |  \ /~~\
 * because a camera script need more than a good name.
 * @author Claudio A. Santoro Wunder
 * @version 1.0b
 * @copyright Sulake Corporation Oy
 */

/**
 * @about Habbo Camera is a PhP class, that emulates Sulake's Habbo Hotel "in-game camera", (that applet that is used for Selfies)
 * @about This script doesn't use any script or code-parts from Sulake Corporation Oy
 */

/*
    Azure Camera PhP GD API, a Graphical PhP Class to Generate Habbo`s Camera API Images.
    Copyright (C) 2015 Claudio Santoro
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

/**
 * available replaced variables:
 * [SERVER_CAMERA], [HOTEL_COUNTRY], [IMAGE_URL], [ROOM_ID], [TIME_STAMP]
 */

$settings = [
    'white-list' => [
        '127.0.0.1',
        'localhost',
        '0.0.0.0',
        'LOCALHOST'
    ],
    'image-settings' => [
        'size-settings' => [
            'image-width' => 320,
            'image-height' => 320
        ],
        'path-settings' => [
            'server-camera' => 'servercamera', // base folder
            'hotel-country' => 'default', // default will use get country code. you can set manually a country code.
            'image-name' => 'default', // using default will use the json-data for name, recommended use default.
            'image-url' => '[SERVER_CAMERA]/purchased/[HOTEL_COUNTRY]/[IMAGE_URL].png'
        ]
    ],
    'thumbnail-settings' => [
        'size-settings' => [
            'image-width' => 100,
            'image-height' => 100
        ],
        'path-settings' => [
            'server-camera' => 'servercamera', // base folder
            'hotel-country' => 'default', // default will use get country code. you can set manually a country code.
            'image-name' => 'default', // using default will use the json-data for name, recommended use default.
            'image-url' => '[SERVER_CAMERA_S]/purchased/[HOTEL_COUNTRY_S]/[IMAGE_SMALL_URL].png'
        ]
    ],
    'room-thumbnail-settings' => [
        'size-settings' => [
            'image-width' => 110,
            'image-height' => 110
        ],
        'path-settings' => [
            'hotel-country' => 'default', // default will use get country code. you can set manually a country code.
            'image-url' => 'navigator-thumbnail/[HOTEL_COUNTRY_R]/[ROOM_ID].png'
        ]
    ],
    'folder-settings' => [
        'masks-folder' => '/masks/',
        'sprites-folder' => '/sprites/'
    ]
];