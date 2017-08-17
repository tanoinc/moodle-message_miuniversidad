<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace miuniversidad;

require_once($CFG->dirroot . '/message/output/miuniversidad/classes/MiUniversidad.php');
require_once($CFG->dirroot . '/lib/accesslib.php');


/**
 * Description of News
 *
 * @author lucianoc
 */
abstract class News
{
    protected $event;
    
    public function __construct(\core\event\base $event)
    {
        $this->event = $event;
    }
    
    public static function from_event(\core\event\base $event) {
        return new static($event);
    }

    public abstract function getTitle();
    public abstract function getContent();
    public abstract function isNotifiable();
    public abstract function isGlobal();
    public abstract function getUsers();
    public abstract function getContext();
}
