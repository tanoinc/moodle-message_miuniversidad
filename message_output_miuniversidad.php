<?php

/**
 * miuniversidad message processor
 * (Made from Andrew Davis "custom" package)
 * 
 * @author Luciano Coggiola 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package message_miuniversidad
 */
require_once($CFG->dirroot . '/message/output/lib.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/classes/MiUniversidad.php');
require_once('lib.php');

use miuniversidad\MiUniversidad;

class message_output_miuniversidad extends message_output
{

    const TYPE_MESSAGE = "instantmessage";
    const TYPE_POST = "posts";    
    protected $processable_events = [self::TYPE_MESSAGE];


    /**
     * Processes the message
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     */
    function send_message($eventdata)
    {
        /*
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        */
        if (!$this->is_configured() or !$eventdata->userto or $this->user_disabled($eventdata->userto) or !$this->is_processable($eventdata) ) {
            return true;
        }
        $user_status = $this->send($eventdata, $eventdata->userto->id );

        return ($user_status != null);
    }

    /**
     * Creates necessary fields in the messaging config form.
     * @param object $mform preferences form class
     */
    function config_form($preferences)
    {
        global $USER;
        $string = '';
        /*
          $string = get_string('url','message_miuniversidad').': <input size="30" name="miuniversidad_url" value="'.$preferences->miuniversidad_url.'" />';
          $string .= get_string('api_key','message_miuniversidad').': <input size="30" name="miuniversidad_api_key" value="'.$preferences->miuniversidad_api_key.'" />';
          $string .= get_string('api_secret','message_miuniversidad').': <input size="30" name="miuniversidad_api_secret" value="'.$preferences->miuniversidad_api_secret.'" />';
         */
        return $string;
    }

    /**
     * Parses the form submitted data and saves it into preferences array.
     * @param object $mform preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences)
    {
        /*
          if (isset($form->miuniversidad_url)) {
          $preferences['message_processor_miuniversidad_url'] = $form->miuniversidad_url;
          }
         */
    }

    /**
     * Loads the config data from database to put on the form (initial load)
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid)
    {
        //$preferences->miuniversidad_url = get_user_preferences( 'message_processor_miuniversidad_url', '', $userid);
    }

    function is_configured()
    {
        global $CFG;
        return (!empty($CFG->miuniversidad_app_name) && !empty($CFG->miuniversidad_api_key) && !empty($CFG->miuniversidad_api_secret) && !empty($CFG->miuniversidad_url));
    }

    protected function user_disabled($user)
    {
        return ($user->auth === 'nologin' or $user->suspended or $user->deleted);
    }

    protected function is_notifiable($eventdata)
    {
        return (in_array($eventdata->name, [static::TYPE_MESSAGE, static::TYPE_POST]));
    }
    
    protected function is_processable($eventdata)
    {
        return (in_array($eventdata->name, $this->processable_events));
    }    
    
    protected function get_content($eventdata)
    {
        if (in_array($eventdata->name, [static::TYPE_MESSAGE])) {
            return $eventdata->smallmessage;
        } elseif (in_array($eventdata->name, [static::TYPE_POST])) {
            return $eventdata->fullmessage;
        }
        return $eventdata->fullmessage;
    }
    
    protected function send($eventdata, $user = null, $context = null) {
        return MiUniversidad::send_raw_news($eventdata->subject, $this->get_content($eventdata), false, $user, $this->is_notifiable($eventdata), $context);
    }
}
