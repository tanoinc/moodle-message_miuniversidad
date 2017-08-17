<?php
/*
 * (Made from Andrew Davis "custom" package)
 * 
 * @author Luciano Coggiola 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package message_miuniversidad
 */

function xmldb_message_miuniversidad_install() {
    global $DB;
    $result = true;

    $provider = new stdClass();
    $provider->name  = 'miuniversidad';
    $DB->insert_record('message_processors', $provider);
    return $result;
}
