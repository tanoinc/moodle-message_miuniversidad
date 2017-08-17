<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace miuniversidad;

require_once($CFG->dirroot . '/message/output/miuniversidad/classes/MiUniversidad.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/classes/News.php');
require_once($CFG->dirroot . '/lib/accesslib.php');

/**
 * Description of NewsForum
 *
 * @author lucianoc
 */
class NewsDiscussion extends News
{
    protected $forum;
   
    protected static function remove_tags($html)
    {
        $tags = array('</p>', '<br />', '<br>', '<hr />', '<hr>', '</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</h6>');
        return trim(strip_tags(str_replace($tags, "\n", $html)));
    }
    
    protected static function get_forum($id)
    {
        global $DB;
        return $DB->get_record('forum', array('id' => $id));
    }
    
    public function getForum()
    {
        if (!$this->forum) {
            $this->forum = static::get_forum($this->event->other['forumid']);
        }
        return $this->forum;
    }

    public function getContent()
    {
        $discussion = $this->event->get_record_snapshot('forum_discussions', $this->event->objectid);
        return static::remove_tags($discussion->message);
    }

    public function getContext()
    {
        return MiUniversidad::context_from_course_id($this->event->courseid);
    }

    public function getTitle()
    {
        $discussion = $this->event->get_record_snapshot('forum_discussions', $this->event->objectid);
        $title = $discussion->subject;

        return $this->getForum()->name.': '.$title;
    }

    public function getUsers()
    {
        if (!$this->isGlobal()) {
            $context = \context_course::instance($this->event->courseid);
            $users = get_enrolled_users($context);
            return array_keys($users);
        } 
        return array();
    }

    public function isGlobal()
    {
        return ($this->getForum()->course == 1);
    }

    public function isNotifiable()
    {
        return ($this->getForum()->type == 'news');
    }
    

}
