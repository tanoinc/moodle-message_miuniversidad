<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace miuniversidad;

require_once($CFG->dirroot . '/message/output/miuniversidad/classes/MiUniversidad.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/classes/NewsDiscussion.php');
require_once($CFG->dirroot . '/lib/accesslib.php');

class Observer
{

    /**
     * A post was created
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function post_created(\mod_forum\event\discussion_created $event)
    {
        return MiUniversidad::instance()->send_news( NewsDiscussion::from_event($event) );
    }

}
