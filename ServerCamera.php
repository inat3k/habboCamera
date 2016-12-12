<?php

/**
 *      ,-. ,_, . . ,-. ,-. ,-. ,-. ,-,-. ,-. ,-. ,-.
 *      ,-|  /  | | |   |-' |   ,-| | | | |-' |   ,-|
 *      `-^ '"' `-^ '   `-' `-' `-^ ' ' ' `-' '   `-^
 * because a camera script need more than a good name.
 * @author Claudio A. Santoro Wunder
 * @version 1.0b
 * @copyright Sulake Corporation Oy
 */

/**
 * @about Azure Camera is a PhP class, that emulates Sulake's Habbo Hotel "in-game camera", (that applet that is used for Selfies)
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
 * Few Words:
 *
 *      "The ultimate tragedy is not the oppression and cruelty by the bad people but the silence over that by the good people."
 * - Martin Luther King
 *
 * Read more at http://www.brainyquote.com/quotes/authors/m/martin_luther_king_jr.html#zmow4kbu42Dpt9yH.99
 */

/*
 * Attention!
 * Is Required PhP 5.4x or Higher
 * Is Required Built-In GD Library
 * Is Recommended Safe Thread PhP Version
 * Is Recommended don't give CHMOD 777X for that file.
 * Is Recommended create a special user for that File.
 * Is Recommended Put Adobe's X-CORS Policy. Read in README.md
 * Any Dudes? Create Issues
 * And, if you are from Sulake.. Hello!
 */

/**
 * Class CameraGD
 * @package AzureCamera
 * Main Class to Generate Image
 */
final class CameraGD
{
    /** @var array|mixed */
    private $json = [];
    /** @var resource */
    private $image;
    /** @var resource */
    private $image_small;
    /** @var array */
    private $settings = [];

    /**
     * Do the Thing
     * @author sant0ro
     *
     * @param array $settings
     */
    function __construct($settings = [])
    {
        /* set settings variables */
        $this->settings = $settings;

        /* do an action */
        echo $this->trace_routers();
    }

    /**
     * # string manipulation functions #
     */

    /**
     * escape text to a safe string.
     *
     * @param array|mixed|string $text_string
     * @return array|mixed|string
     */
    static function escape_text($text_string)
    {
        /* if is array, return the self method for each array value */
        if (is_array($text_string))
            return array_map(__METHOD__, $text_string);

        /* if isn't empty and if is a valid string.. */
        if ((!empty($text_string)) && (is_string($text_string)))
            return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $text_string);

        /* if not return something that can be something.. */
        return $text_string;
    }

    /**
     * # magic functions #
     */

    /**
     * set a specific variable to a specific value
     *
     * @param string $variable
     * @param string $value
     */
    function __set($variable = '', $value = '')
    {
        $this->$variable = self::escape_text($value);
    }

    /**
     * return a variable, if not exists, return null
     *
     * @param string $variable
     * @return array|mixed|null|string
     */
    function __get($variable = '')
    {
        return ((isset($this->$variable)) ? self::escape_text($this->$variable) : null);
    }

    /**
     * # routing functions #
     */

    /**
     * trace routers and do the actions
     * @author Claudio Santoro
     * @todo Improve this
     * @observation That is a Simple Router System.
     *
     * @return mixed
     */
    private function trace_routers()
    {
        /* trace routers by get */
        switch (((isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '')):
            case 'install':
                return $this->actions('install', true);
            case 'test':
                return $this->actions('test', true);
            case 'room':
                return $this->actions('room', false);
            default:
                return $this->actions('run', false);
        endswitch;
    }

    /**
     * # camera functions #
     */

    /**
     * Do the Camera Script
     * @author Claudio Santoro
     *
     * @param string $json
     * @param bool $show_image
     * @param int $run_mode
     * @return mixed
     */
    private function do_it($json = '', $show_image = false, $run_mode = 1)
    {
        /* let's get php input */
        if (empty($this->json = json_decode($json, true)))
            return $this->just_die('500', 'the php input, isn\'t a valid jSON data!', true);

        /* other prob y? */
        if ((!isset($this->json['roomid'])) || (!isset($this->json['timestamp'])))
            return $this->just_die('403', 'the jSON doesn\'t contains timestamp || roomid', true);

        /* set the user-defined variables */
        $this->set_variables($this->settings, ($this->settings['image-settings']['path-settings']['image-url']), ($this->settings['thumbnail-settings']['path-settings']['image-url']), ($this->settings['room-thumbnail-settings']['path-settings']['image-url']));

        /* i love this song */
        return $this->let_it_go($show_image, $run_mode);
    }

    /**
     * do system actions...
     * @author Claudio Santoro
     *
     * @param string $action_name
     * @param bool|false $only_white_list
     * @return string|void
     */
    private function actions($action_name = '', $only_white_list = false)
    {
        /* check if your ip is in white list */
        if (($only_white_list) && (!in_array($_SERVER['REMOTE_ADDR'], $this->settings['white-list'])))
            return $this->just_die('403', 'you have not authorization to execute that item', true);

        /* select an action */
        switch ($action_name):
            case 'run':
                header('Content-Type:text/html; charset=UTF-8');
                return $this->do_it(file_get_contents('php://input'), false, 1);
            case 'room':
                header('Content-Type:text/html; charset=UTF-8');
                return $this->do_it(file_get_contents('php://input'), false, 2);
            case 'install':
                header('Content-Type:text/html; charset=UTF-8');
                return $this->create_folders();
            case 'test_camera':
                header('Content-Type: image/png');
                return $this->test_camera(1);
            case 'test_room':
                header('Content-Type: image/png');
                return $this->test_camera(2);
            default:
                header('Content-Type:text/html; charset=UTF-8');
                return $this->just_die('001', 'this action doesn\'t exists', true);
        endswitch;
    }

    /**
     * do the script in test mode
     * @author Claudio Santoro
     * @observation Used a Default jSON of Habbo Camera
     *
     * @param int $camera_mode
     * @return mixed
     */
    private function test_camera($camera_mode = 1)
    {
        /* return the message with default mode */
        return $this->do_it('{ "planes": [ { "z": 2540.7555503285, "bottomAligned": false, "color": 0, "cornerPoints": [ { "x": 320, "y": 320 }, { "x": 0, "y": 320 }, { "x": 320, "y": 0 }, { "x": 0, "y": 0 } ], "texCols": [] }, { "z": 2410.9300623285, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 177, "y": 192 }, { "x": 145, "y": 176 }, { "x": 209, "y": 176 }, { "x": 177, "y": 160 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 1506.1493179876, "bottomAligned": false, "color": 10066329, "cornerPoints": [ { "x": 273, "y": 29 }, { "x": 265, "y": 25 }, { "x": 281, "y": 25 }, { "x": 273, "y": 21 } ], "texCols": [] }, { "z": 1505.9755480235, "bottomAligned": false, "color": 10066329, "cornerPoints": [ { "x": 465, "y": 125 }, { "x": 273, "y": 29 }, { "x": 473, "y": 121 }, { "x": 281, "y": 25 } ], "texCols": [] }, { "z": 1505.9705480233, "bottomAligned": false, "color": 10066329, "cornerPoints": [ { "x": 49, "y": 141 }, { "x": 41, "y": 137 }, { "x": 273, "y": 29 }, { "x": 265, "y": 25 } ], "texCols": [] }, { "z": 1502.4311487396, "bottomAligned": false, "color": 10066329, "cornerPoints": [ { "x": 49, "y": 141 }, { "x": -15, "y": 109 }, { "x": 57, "y": 137 }, { "x": -7, "y": 105 } ], "texCols": [] }, { "masks": [ { "name": "door_64", "flipH": false, "flipV": false, "location": { "x": 128, "y": 29 } } ], "z": 1005.8245539166, "bottomAligned": false, "color": 13421772, "cornerPoints": [ { "x": 273, "y": 176 }, { "x": 49, "y": 288 }, { "x": 273, "y": 29 }, { "x": 49, "y": 141 } ], "texCols": [{"assetNames": ["wall_texture_64_3_wall_color_jagged3"]}] }, { "z": 1005.8224287303, "bottomAligned": false, "color": 16777215, "cornerPoints": [ { "x": 465, "y": 240 }, { "x": 273, "y": 144 }, { "x": 465, "y": 125 }, { "x": 273, "y": 29 } ], "texCols": [{"assetNames": ["wall_texture_64_3_wall_color_jagged3"]}] }, { "z": 1002.2851546329, "bottomAligned": false, "color": 16777215, "cornerPoints": [ { "x": 49, "y": 288 }, { "x": -15, "y": 256 }, { "x": 49, "y": 141 }, { "x": -15, "y": 109 } ], "texCols": [{"assetNames": ["wall_texture_64_3_wall_color_jagged3"]}] }, { "z": 1001.9421546338, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": -79, "y": 352 }, { "x": -143, "y": 320 }, { "x": 49, "y": 288 }, { "x": -15, "y": 256 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 1001.4078447412, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 241, "y": 384 }, { "x": 49, "y": 288 }, { "x": 281, "y": 364 }, { "x": 89, "y": 268 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 1001.4038447411, "bottomAligned": false, "color": 13684944, "cornerPoints": [ { "x": 281, "y": 364 }, { "x": 89, "y": 268 }, { "x": 281, "y": 356 }, { "x": 89, "y": 260 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 1000.5319949204, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 113, "y": 448 }, { "x": -79, "y": 352 }, { "x": 241, "y": 384 }, { "x": 49, "y": 288 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 999.57783340867, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 281, "y": 356 }, { "x": 89, "y": 260 }, { "x": 289, "y": 352 }, { "x": 97, "y": 256 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 999.57383340859, "bottomAligned": false, "color": 13684944, "cornerPoints": [ { "x": 289, "y": 352 }, { "x": 97, "y": 256 }, { "x": 289, "y": 344 }, { "x": 97, "y": 248 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 997.74782207614, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 289, "y": 344 }, { "x": 97, "y": 248 }, { "x": 297, "y": 340 }, { "x": 105, "y": 244 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 997.74382207606, "bottomAligned": false, "color": 13684944, "cornerPoints": [ { "x": 297, "y": 340 }, { "x": 105, "y": 244 }, { "x": 297, "y": 332 }, { "x": 105, "y": 236 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 997.44642873053, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 305, "y": 320 }, { "x": 113, "y": 224 }, { "x": 465, "y": 240 }, { "x": 273, "y": 144 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 995.91781074361, "bottomAligned": false, "color": 15790320, "cornerPoints": [ { "x": 297, "y": 332 }, { "x": 105, "y": 236 }, { "x": 305, "y": 328 }, { "x": 113, "y": 232 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 995.91381074353, "bottomAligned": false, "color": 13684944, "cornerPoints": [ { "x": 305, "y": 328 }, { "x": 113, "y": 232 }, { "x": 305, "y": 320 }, { "x": 113, "y": 224 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] }, { "z": 995.20873088684, "bottomAligned": false, "color": 11579568, "cornerPoints": [ { "x": 465, "y": 248 }, { "x": 305, "y": 328 }, { "x": 465, "y": 240 }, { "x": 305, "y": 320 } ], "texCols": [{"assetNames": ["floor_texture_64_1_floor_tiles5"]}] } ], "sprites": [ { "name": "pixel_floor_silver_64_b_0_0", "x": -79, "color": 16777215, "y": 255, "z": 71.788064989959 }, { "name": "pixel_floor_silver_64_b_0_0", "x": -15, "color": 16777215, "y": 287, "z": 70.37389759669 }, { "name": "pixel_floor_silver_64_b_0_0", "x": -79, "color": 16777215, "y": 319, "z": 68.959745564013 }, { "name": "pixel_floor_silver_64_a_0_0", "x": -79, "color": 16777215, "y": 256, "z": 36.433433037376 }, { "name": "pixel_floor_silver_64_a_0_0", "x": -15, "color": 16777215, "y": 288, "z": 35.019265644107 }, { "name": "cubie_shelf_1_b_64_a_0_0", "x": 278, "color": 16777215, "y": 130, "z": 3.8992843786696 }, { "name": "cubie_shelf_1_b_64_b_0_4", "x": 284, "color": 16777215, "y": 149, "z": 3.8985772719254 }, { "name": "cubie_shelf_2_b_64_a_0_0", "x": 310, "color": 16777215, "y": 146, "z": 3.1922083617946 }, { "name": "cubie_shelf_2_b_64_b_0_1", "x": 316, "color": 16777215, "y": 170, "z": 3.1915012550504 }, { "name": "usva_shelf_64_sd_0_0", "color": 16777215, "y": 240, "z": 1.7780717322081, "x": 272, "alpha": 48 }, { "name": "usva_shelf_64_b_0_1", "x": 272, "color": 16777215, "y": 199, "z": 1.7780717321711 }, { "name": "h_std_sd_1_0_0", "color": 16777215, "y": 249, "z": 1.3638735741835, "x": 192, "alpha": 50 }, { "name": "pura_mdl2_64_d_0_0", "x": 208, "color": 16777215, "y": 192, "z": 1.0794425525807 }, { "name": "pixel_bed_blue_64_sd_0_0", "flipH": true, "y": 276, "z": 1.0780534903108, "x": -103, "alpha": 48, "color": 16777215 }, { "name": "pura_mdl2_64_a_0_0", "x": 208, "color": 16777215, "y": 219, "z": 1.0780283389074 }, { "name": "pura_mdl2_64_b_0_0", "x": 213, "color": 6204152, "y": 211, "z": 1.0773212321632 }, { "name": "usva_shelf_64_a_0_1", "x": 273, "color": 16777215, "y": 200, "z": 1.0709649509475 }, { "name": "pura_mdl2_64_c_0_0", "x": 214, "color": 6204152, "y": 205, "z": 1.0483298541715 }, { "name": "pura_mdl3_64_a_0_0", "x": 240, "color": 16777215, "y": 235, "z": 0.38226603171534 }, { "name": "pura_mdl3_64_b_0_0", "x": 239, "color": 6204152, "y": 224, "z": 0.38085181818997 }, { "name": "avatar_0", "x": 164, "color": 16777215, "y": 142, "z": 0.35287357414649 }, { "name": "h_std_bd_1_3_0", "x": 197, "color": 16763800, "y": 206, "z": 0.35287357414649 }, { "name": "h_std_sh_2044_3_0", "x": 199, "color": 12971494, "y": 254, "z": 0.35277357414649 }, { "name": "h_std_sh_2045_3_0", "x": 200, "color": 14540253, "y": 255, "z": 0.35267357414649 }, { "name": "h_std_lg_2129_3_0", "x": 198, "y": 235, "z": 0.35257357414649 }, { "name": "h_std_lg_2130_3_0", "x": 199, "color": 7572334, "y": 239, "z": 0.35247357414649 }, { "name": "h_std_ch_2126_3_0", "x": 202, "y": 208, "z": 0.35237357414649 }, { "name": "h_std_ch_2127_3_0", "x": 207, "color": 1973790, "y": 216, "z": 0.35227357414649 }, { "name": "h_std_ch_2128_3_0", "x": 198, "color": 13016945, "y": 206, "z": 0.35217357414649 }, { "name": "h_std_hd_3_3_0", "x": 196, "color": 16763800, "y": 182, "z": 0.35207357414649 }, { "name": "h_std_fc_1_3_0", "x": 206, "color": 16763800, "y": 204, "z": 0.35197357414649 }, { "name": "h_std_ey_1_3_0", "x": 202, "y": 198, "z": 0.35187357414649 }, { "name": "h_std_hr_2268_3_0", "x": 194, "color": 7816226, "y": 171, "z": 0.35177357414649 }, { "name": "h_std_hrb_2268_3_0", "x": 194, "color": 7816226, "y": 171, "z": 0.35167357414649 }, { "name": "h_std_lh_1_3_0", "x": 218, "color": 16763800, "y": 208, "z": 0.35157357414649 }, { "name": "h_std_ls_2128_3_0", "x": 218, "color": 13016945, "y": 206, "z": 0.35147357414649 }, { "name": "h_std_rh_1_3_0", "x": 193, "color": 16763800, "y": 208, "z": 0.35137357414649 }, { "name": "h_std_rs_2128_3_0", "x": 193, "color": 13016945, "y": 206, "z": 0.35127357414649 }, { "name": "pura_mdl3_64_c_0_0", "x": 240, "color": 6204152, "y": 218, "z": 0.34408226560527 }, { "name": "pura_mdl1_64_a_0_0", "x": 272, "color": 16777215, "y": 251, "z": -0.33612369450966 }, { "name": "pura_mdl1_64_b_0_0", "x": 271, "color": 6204152, "y": 240, "z": -0.33683080125385 }, { "name": "pixel_bed_blue_64_b_0_0", "color": 16777215, "y": 240, "z": -0.33686717895446, "x": -109, "flipH": true }, { "name": "pura_mdl1_64_c_0_0", "x": 272, "color": 6204152, "y": 234, "z": -0.3658221792455 }, { "name": "pura_mdl1_64_d_0_0", "x": 300, "color": 16777215, "y": 238, "z": -0.36652928598968 }, { "name": "pixel_bed_blue_64_c_0_0", "color": 16777215, "y": 274, "z": -0.69112767629192, "x": -78, "flipH": true }, { "name": "pixel_bed_blue_64_d_0_0", "color": 16777215, "y": 290, "z": -1.3989415642227, "x": -46, "flipH": true }, { "name": "chair_polyfon_64_a_2_0", "color": 16777215, "y": 313, "z": -3.1431911580272, "x": 171, "flipH": true } ], "modifiers": [], "filters": [], "roomid": 3, "status": 108, "timestamp": 1436635320404, "checksum": 6097 }', true, $camera_mode);
    }

    /**
     * create default folders...
     * @author Claudio Santoro
     *
     * @return string|void
     */
    private function create_folders()
    {
        /* check existence of masks folder */
        self::create_folder($this->settings['folder-settings']['masks-folder']);

        /* check existence of sprites folder */
        self::create_folder($this->settings['folder-settings']['sprites-folder']);

        /* check existence of thumbnail output folder */
        self::create_folder($this->settings['thumbnail-settings']['path-settings']['server-camera']);

        /* check existence of image output folder */
        self::create_folder($this->settings['image-settings']['path-settings']['server-camera']);

        /* show success message */
        return $this->just_show('Yes. Folders Created Successfully..', true);
    }

    /**
     * create folder first checking if everything is valid
     *
     * @param string $folder_name
     */
    static private function create_folder($folder_name = '')
    {
        /** create a secure folder (or not kidding) */
        if ((!empty($folder_name) && (!is_dir($folder_name))))
            mkdir($folder_name, 0755);
    }

    /**
     * define run-time global variables
     *
     * @param string $name
     * @param string $value
     */
    static private function set_variable($name = '', $value = '')
    {
        /* must check if is a valid string yeah? */
        if (((!empty($name)) && (!empty($value))) && ((is_string($name) && ((is_string($value)) || (is_numeric($value))))))
            defined($name) || define($name, $value);
    }

    /**
     * set the user defined variables of the CameraGD script.
     *
     * @param array $settings
     * @param string $image_url
     * @param string $image_small_url
     * @param string $image_room_url
     */
    private function set_variables($settings = [], $image_url = '', $image_small_url = '', $image_room_url = '')
    {
        /* let's do it now! */
        $image_url       = ((($settings['image-settings']['path-settings']['image-name']) == 'default') ? str_replace('[IMAGE_URL]', ($this->json['roomid'] . '-' . $this->json['timestamp']), $image_url) : (str_replace('[IMAGE_URL]', ($settings['image-settings']['path-settings']['image-name']), $image_url)));
        $image_small_url = ((($settings['thumbnail-settings']['path-settings']['image-name']) == 'default') ? str_replace('[IMAGE_SMALL_URL]', ($this->json['roomid'] . '-' . $this->json['timestamp'] . '_small'), $image_small_url) : (str_replace('[IMAGE_SMALL_URL]', ($settings['thumbnail-settings']['path-settings']['image-name']), $image_small_url)));

        /* define folder-path variables */
        self::set_variable('ROOT_DIR', __DIR__);

        /* variable for room_id */
        self::set_variable('ROOM_ID', ($this->json['roomid']));

        /* variable for time-stamp */
        self::set_variable('TIME_STAMP', ($this->json['timestamp']));

        /* camera main image sizes */
        self::set_variable('IMAGE_W', ($settings['image-settings']['size-settings']['image-width']));
        self::set_variable('IMAGE_H', ($settings['image-settings']['size-settings']['image-height']));

        /* camera thumbnail image sizes */
        self::set_variable('IMAGE_R_W', ($settings['room-thumbnail-settings']['size-settings']['image-width']));
        self::set_variable('IMAGE_R_H', ($settings['room-thumbnail-settings']['size-settings']['image-height']));

        /* camera room thumbnail image sizes */
        self::set_variable('IMAGE_S_W', ($settings['thumbnail-settings']['size-settings']['image-width']));
        self::set_variable('IMAGE_S_H', ($settings['thumbnail-settings']['size-settings']['image-height']));

        /* server-camera image root dir (output for generated images) */
        self::set_variable('SERVER_CAMERA', ($settings['image-settings']['path-settings']['server-camera']));

        /* server-camera thumbnail root dir (output for generated images) */
        self::set_variable('SERVER_CAMERA_S', ($settings['thumbnail-settings']['path-settings']['server-camera']));

        /* validate hotel requester image country */
        self::set_variable('HOTEL_COUNTRY', ((($settings['image-settings']['path-settings']['hotel-country']) == 'default') ? $this->visitor_country((@$_SERVER['HTTP_CLIENT_IP']), (@$_SERVER['HTTP_X_FORWARDED_FOR']), (@$_SERVER['REMOTE_ADDR'])) : strtolower($settings['image-settings']['path-settings']['hotel-country'])));

        /* validate hotel requester thumbnail country */
        self::set_variable('HOTEL_COUNTRY_S', ((($settings['thumbnail-settings']['path-settings']['hotel-country']) == 'default') ? $this->visitor_country((@$_SERVER['HTTP_CLIENT_IP']), (@$_SERVER['HTTP_X_FORWARDED_FOR']), (@$_SERVER['REMOTE_ADDR'])) : strtolower($settings['thumbnail-settings']['path-settings']['hotel-country'])));

        /* validate hotel requester room thumbnail country */
        self::set_variable('HOTEL_COUNTRY_R', ((($settings['room-thumbnail-settings']['path-settings']['hotel-country']) == 'default') ? $this->visitor_country((@$_SERVER['HTTP_CLIENT_IP']), (@$_SERVER['HTTP_X_FORWARDED_FOR']), (@$_SERVER['REMOTE_ADDR'])) : strtolower($settings['room-thumbnail-settings']['path-settings']['hotel-country'])));

        /**
         * you need Habbo avatars, furniture, effects, and pets sprites extracted manually from all SWF's.
         * you can get, extract all seeing the habbo-asset-extractor repository.
         * @link http://github.com/sant0ro/habbo-asset-extractor/
         *
         * @author Claudio Santoro
         * @package habbo-asset-extractor
         */
        self::set_variable('SPRITES_ROOT', ROOT_DIR . ($settings['folder-settings']['sprites-folder']));
        self::set_variable('MASKS_ROOT', ROOT_DIR . ($settings['folder-settings']['masks-folder']));

        /* camera main image url */
        self::set_variable('IMAGE_URL', str_replace('[SERVER_CAMERA]', SERVER_CAMERA, str_replace('[HOTEL_COUNTRY]', HOTEL_COUNTRY, $image_url)));

        /* camera thumbnail image url */
        self::set_variable('IMAGE_SMALL_URL', str_replace('[SERVER_CAMERA_S]', SERVER_CAMERA_S, str_replace('[HOTEL_COUNTRY_S]', HOTEL_COUNTRY_S, $image_small_url)));

        /* camera room thumbnail image url */
        self::set_variable('IMAGE_ROOM_URL', str_replace('[HOTEL_COUNTRY_R]', HOTEL_COUNTRY_R, str_replace('[ROOM_ID]', ROOM_ID, $image_room_url)));
    }

    /**
     * get visitors country code
     * @indeed visitors in this case need to bet the Habbo Client requesting the Camera..
     * @indeed If this not happen you have security Violation
     * @see Adobe's Flash X-CORS HTTP Request Policy (crossdomain.xml)
     * @link http://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html
     * @author Claudio Santoro
     *
     * @param string $client HTTP_CLIENT_IP
     * @param string $forward HTTP_X_FORWARDED_FOR
     * @param string $remote REMOTE_ADDR
     * @return string Country Code
     */
    private function visitor_country($client = '', $forward = '', $remote = '')
    {
        /* filter ip data, check if is valid, and get geoplugin ip data */
        $ip_data = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip=' . ((filter_var($client, FILTER_VALIDATE_IP)) ? $client : ((filter_var($forward, FILTER_VALIDATE_IP)) ? $forward : $remote))));

        /* return country code */
        return strtolower(((($ip_data) && ($ip_data->geoplugin_countryCode != null)) ? ($ip_data->geoplugin_countryCode) : 'us'));
    }

    /**
     * let it go
     * allocate image and call all image voids
     * @author Claudio Santoro
     *
     * @param bool $show_image
     * @param int $run_mode
     * @return mixed
     */
    private function let_it_go($show_image = false, $run_mode = 1)
    {
        /* let allocate everything */
        $this->image = imagecreatetruecolor((($run_mode == 1) ? IMAGE_W : IMAGE_R_W), (($run_mode == 1) ? IMAGE_H : IMAGE_R_H));

        /* render all jSON planes */
        $this->render_planes();

        /* render all jSON sprites */
        $this->render_sprites();

        /* create thumbnail image */
        ($run_mode != 1) || $this->smaller_image();

        /* save the image */
        $this->image_create($show_image, $run_mode);

        if ($run_mode == 2) return null;

        /* i must give back the url of main image because CLIENT need's */
        if ($show_image) return $this->image;

        /* if don't need to show image */
        return IMAGE_URL;
    }

    /**
     * Converts a Hex String into RGB
     * Based on
     * @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
     * @author Claudio Santoro, C.Bavota
     *
     * @param string $hex Input Hex
     * @return array RGB String
     */
    private function hex_to_rgb($hex = '')
    {
        // remove the hex identifier tag
        $hex = str_replace('#', '', $hex);

        // replaces the correspondent hex value
        $r = ((strlen($hex) == 3) ? hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)) : hexdec(substr($hex, 0, 2)));
        $g = ((strlen($hex) == 3) ? hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)) : hexdec(substr($hex, 2, 2)));
        $b = ((strlen($hex) == 3) ? hexdec(substr($hex, 2, 1) . substr($hex, 2, 1)) : hexdec(substr($hex, 4, 2)));

        // return rgb array
        return [$r, $g, $b];
    }

    /**
     * Colorize a image part set Image
     * @author Claudio Santoro
     *
     * @param resource $image Resource Link
     * @param integer $red R-GB
     * @param integer $green R-G-B
     * @param integer $blue RG-B
     */
    private function image_recolor(&$image, $red = 255, $green = 255, $blue = 255)
    {
        // get image width and height
        $width  = imagesx($image);
        $height = imagesy($image);

        // recolor every pixel
        for ($x = 0; $x < $width; $x++) for ($y = 0; $y < $height; $y++) self::part_recolor($image, $red, $green, $blue, $x, $y);
    }

    /**
     * recolor specific pixel of a given image
     * @author AntoineFR
     * @editor Claudio Santoro
     *
     * @param $image
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $x
     * @param int $y
     */
    static private function part_recolor(&$image, $red = 255, $green = 255, $blue = 255, $x = 0, $y = 0)
    {
        // get rgb of the pixel
        $rgb = imagecolorsforindex($image, (imagecolorat($image, $x, $y)));

        // put the rgb color
        $r = (($red / 255) * ($rgb['red']));
        $g = (($green / 255) * ($rgb['green']));
        $b = (($blue / 255) * ($rgb['blue']));

        // set the new pixel
        imagesetpixel($image, $x, $y, (imagecolorallocatealpha($image, $r, $g, $b, ($rgb['alpha']))));
    }

    /**
     * An Old but Needed function for the Actual Days of PhP
     * @source http://forums.devnetwork.net/viewtopic.php?f=1&t=103330#p553333
     * @author RedMonkey
     * @editor Claudio Santoro
     *
     * "@observation Sorry of this function need to be big. Is because the simpliest approaches found in internet"
     * "doesn't work as well that these approach.."
     * "i know oop need small functions, but sorry"
     *
     * @param resource $dst Destination Allocated Image
     * @param resource $src Source Allocated Image
     * @param int $dst_x Destination Position X
     * @param int $dst_y Destination Position Y
     * @param int $src_x Source Position X
     * @param int $src_y Source Position Y
     * @param int $w Width
     * @param int $h Height
     * @param int $pct Alpha Percent
     * @return null
     */
    private function image_copy_merge_with_alpha($dst, $src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $w = 0, $h = 0, $pct = 100)
    {
        /* yes divide */
        $pct /= 100;

        /* make sure opacity level is within range before going any further */
        $pct = max(min(1, $pct), 0);

        /* work out if we need to bother correcting for opacity */
        if ($pct < 1):
            /* we need a copy of the original to work from, only copy the cropped */
            /* area of src                                                        */
            $src_copy = imagecreatetruecolor($w, $h);

            /* attempt to maintain alpha levels, alpha blending must be *off* */
            imagealphablending($src_copy, false);
            imagesavealpha($src_copy, true);

            imagecopy($src_copy, $src, 0, 0, $src_x, $src_y, $w, $h);

            /* we need to know the max transparency of the image */
            $max_t = 0;

            for ($y = 0; $y < $h; $y++):
                for ($x = 0; $x < $w; $x++):
                    $src_c = imagecolorat($src_copy, $x, $y);
                    $src_a = (($src_c >> 24) & 0xFF);
                    $max_t = (($src_a > $max_t) ? $src_a : $max_t);
                endfor;
            endfor;

            /* src has no transparency? set it to use full alpha range */
            $max_t = (($max_t == 0) ? 127 : $max_t);

            /* $max_t is now being reused as the correction factor to apply based */
            /* on the original transparency range of  src                         */
            $max_t /= 127;

            /* go back through the image adjusting alpha channel as required */
            for ($y = 0; $y < $h; $y++):
                for ($x = 0; $x < $w; $x++):
                    $src_c = imagecolorat($src, $src_x + $x, $src_y + $y);
                    $src_a = (($src_c >> 24) & 0xFF);
                    $src_r = (($src_c >> 16) & 0xFF);
                    $src_g = (($src_c >> 8) & 0xFF);
                    $src_b = (($src_c) & 0xFF);

                    /* alpha channel compensation */
                    $src_a = ((($src_a + 127) - (127 * $pct)) * $max_t);
                    $src_a = (($src_a > 127) ? 127 : (int)$src_a);

                    /* get and set this pixel's adjusted RGBA colour index */
                    $rgba = imagecolorallocatealpha($src_copy, $src_r, $src_g, $src_b, $src_a);

                    /* @method /imagecolorclosestalpha returns -1 for PHP versions prior  */
                    /* to 5.1.3 when allocation failed */
                    if (($rgba === false) || ($rgba == -1)) $rgba = imagecolorclosestalpha($src_copy, $src_r, $src_g, $src_b, $src_a);

                    imagesetpixel($src_copy, $x, $y, $rgba);
                endfor;
            endfor;

            /* call image copy passing our alpha adjusted image as src */
            imagecopy($dst, $src_copy, $dst_x, $dst_y, 0, 0, $w, $h);

            /* cleanup, free memory */
            imagedestroy($src_copy);
            return null;
        endif;

        /* still here? no opacity adjustment required so pass straight through to */
        /* @method /imagecopy rather than @method /imagecopymerge to retain alpha channels          */
        imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $w, $h);
        return null;
    }

    /**
     * rotate_polygon
     * @BETA
     * This Function is a Experimental Function to try to Create a 2d Polygon Distortion
     * These Function Reassing Polygon |Corner Points| Coordinates
     * @link http://stackoverflow.com/a/29352686
     * @author Steve Burgess
     * @editor Claudio Santoro
     *
     * @param array $polygon Original Corner Points
     * @param int $angle
     * @param int $centre_x
     * @param int $centre_y
     * @param int $scale
     * @return array Reassigned Corner Points
     */
    static private function rotate_polygon($polygon = [], $angle = 0, $centre_x = 0, $centre_y = 0, $scale = 1)
    {
        /** I have negated the angle here so the function rotates in the same
         * direction as the imagerotate() function in PHP
         *
         * PHP Trigonometric Functions (e.g. cosine, sine) require the angle to
         * be in radians rather than degrees - hence the deg2rad() conversion.
         */

        $angle = deg2rad(-$angle);

        if ($scale <> 1):
            // Using the array_map() function performs the scaling for the entire array
            // in one line - rather than having to write code that loops through the array.

            $polygon_scaled = array_map("scale", $polygon, array_fill(0, count($polygon), $scale));
            $polygon        = $polygon_scaled;
            $centre_x       = ($centre_x * $scale);
            $centre_y       = ($centre_y * $scale);
        endif;

        $rotated = [];

        for ($i = 0; $i < count($polygon); $i = $i + 2):
            // Using the array map function to perform these transformations was beyond me.
            // If anyone has any bright ideas about this, please drop me a line

            // Original coordinates of each point
            $x = $polygon[$i];
            $y = $polygon[$i + 1];

            // As imagepolygon requires a 1 dimensional array, the new x and the new y
            // coordinates are simply added to the rotated array one after the other
            $rotated[] = ($centre_x + (($x - $centre_x) * cos($angle)) - (($y - $centre_y) * sin($angle)));
            $rotated[] = ($centre_y + (($x - $centre_x) * sin($angle)) + (($y - $centre_y) * cos($angle)));
        endfor;

        return $rotated;
    }

    /**
     * render specific tex_col
     *
     * @param array $tex_col
     * @param array $color_rgb
     * @param array $polygon_array
     * @param int $a CornerPoints [0]X
     * @param int $b CornerPoints [0]Y
     * @param int $c Plane Z:
     */
    private function render_tex_col($tex_col = [], $color_rgb = [], $polygon_array = [], $a = 0, $b = 0, $c = 0)
    {
        /* sure that happen? */
        if (empty($tex_col)) return;

        /* jingle */
        if ((isset($tex_col['flipH'])) && (stripos('_flipH', $tex_col['assetNames'][0] !== false))) $tex_col['assetNames'] = str_ireplace('_flipH', '', ($tex_col['assetNames'][0]));

        /* let's create a image.. */
        if (is_bool($tex_cols_asset = @imagecreatefrompng(MASKS_ROOT . $tex_col['assetNames'][0] . '.png'))) return;

        /* soo flip, soo flip, flip. */
        if (isset($tex_col['flipH']) && ($tex_col['flipH'] == 'true')) imageflip($tex_cols_asset, IMG_FLIP_HORIZONTAL);

        /**
         * # @BETA #
         * beta .. trying to code 3d image distortion
         * > sadly php has not 3d and 2d image distortion functions built-in..
         * > trying to create manually
         * > Here is the Void (Experimentally
         * $c is seated to 0 because we don't have a correct rotation value. And
         * rotate_polygon() only does 2d Rotation (360? degrees)
         */
        //$polygon_array = self::rotate_polygon($polygon_array, $c, $a, $b);
        $polygon_array = self::rotate_polygon($polygon_array, 0, $a, $b);

        /**
         * # @BETA #
         * beta .. trying to code 3d image distortion
         * > sadly php has not 3d and 2d image distortion functions built-in..
         * > trying to create manually
         */
        //$tex_cols_asset = imagerotate($tex_cols_asset, 155, imagecolorallocatealpha($tex_cols_asset, 0, 0, 0, 127));
        //$this->image = imagerotate($this->image, 155, imagecolorallocatealpha($this->image, 0, 0, 0, 127));

        /* really, tha color is really bad.. */
        $this->image_recolor($tex_cols_asset, $color_rgb[0], $color_rgb[1], $color_rgb[2]);
        imagesettile($this->image, $tex_cols_asset);

        /* the tex_cols must be putted back into original image, y? */
        imagefilledpolygon($this->image, $polygon_array, (count($polygon_array) / 2), IMG_COLOR_TILED);

        /** curiously that can avoid the pixelated tiles... to improve the quality of image. */
        //imagepolygon($this->image, $polygon_array, (count($polygon_array) / 2), IMG_COLOR_TILED);

        /* no garbage, sir */
        imagedestroy($tex_cols_asset);
    }

    /**
     * render specific mask
     *
     * @param array $mask
     * @param array $plane
     */
    private function render_mask($mask = [], $plane = [])
    {
        /* sure that happened? */
        if (empty($mask)) return;

        /* jingle */
        if ((isset($mask['flipH'])) && (stripos('_flipH', $mask['name'] !== false))) $mask['name'] = str_ireplace('_flipH', '', ($mask['name']));

        /* dingle bells.. */
        if (is_bool($mask_asset = @imagecreatefrompng(MASKS_ROOT . $mask['name'] . '.png'))) return;

        /* soo flip, soo flip, flip. */
        if (isset($mask['flipH']) && ($mask['flipH'] == 'true')) imageflip($mask_asset, IMG_FLIP_HORIZONTAL);

        /* copy me please, and put me into original! */
        imagecopy($this->image, $mask_asset, $plane['cornerPoints'][1]['x'] + $mask['location']['x'], (($mask['location']['x']) - ($mask['location']['y'])), 0, 0, imagesx($mask_asset), imagesy($mask_asset));

        /* no garbage, sir */
        imagedestroy($mask_asset);
    }

    /**
     * render all the planes of the fuckin xit ;)
     * @author TyrexFR
     * @author sant0ro
     * @todo: improve door math calculation
     * @bug: door position y is really bad.
     */
    protected function render_planes()
    {
        /* foreach each plane of the image */
        foreach ($this->json['planes'] as $plane):

            /* assign image assets resources */
            $color_rgb = $this->hex_to_rgb(dechex($plane['color']));
            $color     = imagecolorallocate($this->image, $color_rgb[0], $color_rgb[1], $color_rgb[2]);

            /* array with polygons */
            $polygon_array = [];

            /* put in the polygon array */
            array_push($polygon_array, $plane['cornerPoints'][0]['x'], $plane['cornerPoints'][0]['y'], $plane['cornerPoints'][1]['x'], $plane['cornerPoints'][1]['y'], $plane['cornerPoints'][3]['x'], $plane['cornerPoints'][3]['y'], $plane['cornerPoints'][2]['x'], $plane['cornerPoints'][2]['y']);

            /* is also a pokemon name. */
            imagefilledpolygon($this->image, $polygon_array, (count($polygon_array) / 2), $color);

            /* get tex_cols of every plane */
            if (array_key_exists('texCols', $plane)) foreach ($plane['texCols'] as $tex_col) $this->render_tex_col($tex_col, $color_rgb, $polygon_array, $plane['cornerPoints'][0]['x'], $plane['cornerPoints'][0]['y'], $plane['z']);

            /* get masks of every plane */
            if (array_key_exists('masks', $plane)) foreach ($plane['masks'] as $mask) $this->render_mask($mask, $plane);

            $polygon_array = null;

            /* adele says, this is the end (jingle) */
        endforeach;
    }

    /**
     * Render all Image Sprites for the Camera.
     * @author TyrexFR
     * @author sant0ro
     * @todo: make this xit better
     * @todo: better sprites positioning
     */
    protected function render_sprites()
    {
        /* every sprite... avatars, furniture, soo.. everything */
        foreach ($this->json['sprites'] as $sprite):

            /* get out of there */
            if (is_bool($tha_sprite = @imagecreatefrompng(SPRITES_ROOT . $sprite['name'] . '.png'))) continue;

            /* i'm the alpha and omega, no, just alpha. */
            $alpha = ((isset($sprite['alpha'])) ? ((int)$sprite['alpha']) : 100);

            /* soo flip, soo flip, flip. */
            if (isset($sprite['flipH']) && ($sprite['flipH'] == 'true')) imageflip($tha_sprite, IMG_FLIP_HORIZONTAL);

            /* @author Macklebee... really, why Habbo use bad TrueColor Codes? */
            if (array_key_exists('color', $sprite) && ($sprite['color'] != '16777215')):
                $color_rgb = $this->hex_to_rgb(dechex($sprite['color']));
                $this->image_recolor($tha_sprite, $color_rgb[0], $color_rgb[1], $color_rgb[2]);
            endif;

            /* really, that is good! */
            $this->image_copy_merge_with_alpha($this->image, $tha_sprite, $sprite['x'], $sprite['y'], 0, 0, imagesx($tha_sprite), imagesy($tha_sprite), $alpha);

            /* really, we don't wanna garbage! */
            imagedestroy($tha_sprite);
        endforeach;
    }

    /**
     * let's just die, okay?
     * @author Claudio Santoro
     * @todo: make this better
     *
     * @param int $error_code
     * @param string $error_message
     * @param bool|false $must_die
     * @return string|void
     */
    private function just_die($error_code = 1, $error_message = '', $must_die = false)
    {
        $r = '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"><br><br><br><div class="row"><div class="col-sm-2"></div><div class="col-sm-8"><div class="alert alert-danger"><h4><i>Oh No!</i> (#' . $error_code . ')</h4><b> We really sorry, but the following error happend:</b><br><hr><blockquote><h5>' . $error_message . '</h5></blockquote></div></div></div>';
        return (($must_die) ? die($r) : $r);
    }

    /**
     * let's just show and die, okay?
     * @author Claudio Santoro
     * @todo: make this better
     *
     * @param string $message
     * @param bool|false $must_die
     * @return string|void
     */
    private function just_show($message = '', $must_die = false)
    {
        $r = '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"><br><br><br><div class="row"><div class="col-sm-2"></div><div class="col-sm-8"><div class="alert alert-success"><h4><>OK!</h4><b> The system write a special message for you: :</b><br><hr><blockquote><h5>' . $message . '</h5></blockquote></div></div></div>';
        return (($must_die) ? die($r) : $r);
    }

    /**
     * this method save in a physical link, the allocated memory from the image
     * @uses image/png compression
     * @see https://en.wikipedia.org/wiki/Portable_Network_Graphics
     * also this method erases the allocated memory
     * @param bool $show_image
     * @param int $run_mode
     */
    private function image_create($show_image = false, $run_mode = 1)
    {
        /* check if the variables are really valid resources.. otherwise try to create from string.. */
        @$this->image = (((!is_resource($this->image)) && (is_string($this->image))) ? (string)imagecreatefromstring($this->image) : $this->image);
        @$this->image_small = (((!is_resource($this->image_small)) && (is_string($this->image_small))) ? (string)imagecreatefromstring($this->image_small) : $this->image_small);

        /* save image alpha blending (only if in other steps didn't that)  */
        imagesavealpha($this->image, true);

        /* save main camera image */
        if (!$show_image) if ($run_mode == 1) imagepng($this->image, IMAGE_URL); else imagepng($this->image, IMAGE_ROOM_URL); else imagepng($this->image);

        /* save the image thumbnail */
        if ($run_mode == 1) @imagepng($this->image_small, IMAGE_SMALL_URL);
    }

    /**
     * resize the camera image, for a smaller image
     * that will be the thumbnail
     * @author sant0ro
     */
    private function smaller_image()
    {
        /* why a function only with that? */
        $this->image_small = imagescale($this->image, IMAGE_S_W, IMAGE_S_H);
    }

    /**
     * destroy! all generated images
     */
    function __destruct()
    {
        /* because we love destroy memory */
        @imagedestroy($this->image_small);
        @imagedestroy($this->image);
    }
}

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

/* let sing a song */
new CameraGD($settings);
exit;