<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


defined('MOODLE_INTERNAL') || die();
$observers = array(
    array(
        'eventname' => '\mod_forum\event\discussion_created',
        'callback' => '\miuniversidad\Observer::post_created',
        'includefile' => '/message/output/miuniversidad/classes/Observer.php',
    ),
);