<?php
/**
 * Upgrade code for processor
 * (Made from Andrew Davis "custom" package)
 * 
 * @author Luciano Coggiola 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package message_miuniversidad
 */

function xmldb_message_miuniversidad_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017080100.00) {
        $processor = new stdClass();
        $processor->name  = 'miuniversidad';
        if (!$DB->record_exists('message_processors', array('name' => $processor->name)) ){
            $DB->insert_record('message_processors', $processor);
        }

        //my message processor savepoint reached
        upgrade_plugin_savepoint(true, 2017080100.00, 'message', 'miuniversidad');
    }

    return true;
}
