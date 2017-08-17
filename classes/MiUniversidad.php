<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace miuniversidad;

global $CFG;

require_once($CFG->dirroot . '/message/output/miuniversidad/lib.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/classes/NewsDiscussion.php');

/**
 * Description of MiUniversidad
 *
 * @author lucianoc
 */
class MiUniversidad
{
    protected static $instance = null;

    /**
     * gets the current "MiUniversidad" instance
     *
     * @return MiUniversidad
     */
    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new MiUniversidad();
        }
        return static::$instance;
    }

    public function send_news(News $news)
    {
        return static::send_raw_news($news->getTitle(), $news->getContent(), $news->isGlobal(), $news->getUsers(), $news->isNotifiable(), $news->getContext());
    }
    
    public static function send_raw_news($title, $message, $global, $recipients, $send_notification, $context )
    {
        if (!is_array($recipients)) {
            if ($recipients) {
                $recipients = array($recipients);
            } else {
                $recipients = array();
            }
        }
        
        $answer = miuniversidad_send_news($title, $message, $global, $recipients, $send_notification, $context );
        return ($answer != null);
    }
    
    
    public static function context_from_course_id($course_id)
    {
        global $DB;
        $course = $DB->get_record('course', array('id' => $course_id));
        if ($course) {
            return array('name' => 'course_'.$course->id, 'description' => $course->fullname);
        }
        return null;
    }
}
