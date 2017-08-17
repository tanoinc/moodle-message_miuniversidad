<?php
/**
 * Mi Universidad message configuration page
 *
 * @author Luciano Coggiola 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package message_miuniversidads
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('miuniversidad_app_name', get_string('app_name','message_miuniversidad'), get_string('config_app_name','message_miuniversidad'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('miuniversidad_url', get_string('url','message_miuniversidad'), get_string('config_url','message_miuniversidad'), '', PARAM_RAW));
    $settings->add(new admin_setting_configtext('miuniversidad_api_key', get_string('api_key','message_miuniversidad'), get_string('config_api_key','message_miuniversidad'), '', PARAM_RAW));
    $settings->add(new admin_setting_configpasswordunmask('miuniversidad_api_secret', get_string('api_secret','message_miuniversidad'), get_string('config_api_secret','message_miuniversidad'), ''));
}
