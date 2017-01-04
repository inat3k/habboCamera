<?php

define('RUNNING_DEFINED', true);

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

@require_once('Settings.php');
@require_once('CameraGD.php');

/* let sing a song */
new CameraGD($settings);
exit;