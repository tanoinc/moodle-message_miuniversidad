<?php

require('../../../config.php');

require_once($CFG->dirroot . '/message/output/lib.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/classes/MiUniversidad.php');
require_once($CFG->dirroot . '/message/output/miuniversidad/lib.php');

function miuniversidad_js_status($app, $status) {
    return html_writer::script('if (typeof(Storage) !== "undefined") { localStorage.setItem("'.$app.'_status", "'.$status.'"); }');
}

$id = required_param('id', PARAM_ALPHANUM); //Mi Universidad user hash id
$token = required_param('token', PARAM_ALPHANUM);  // Mi Universidad token

$PAGE->set_url(new moodle_url('/message/output/miuniversidad/requestaccess.php', array('id' => $id, 'token' => $token) ));
$PAGE->set_context(context_system::instance());

require_login();

$strheading = get_string('requestaccess', 'message_miuniversidad');
$PAGE->navbar->add(get_string('pluginname', 'message_miuniversidad'));
$PAGE->navbar->add($strheading);

$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

$output = "";

if (optional_param('confirm', null, PARAM_TEXT)) {
    
    try {
        miuniversidad_connect($id, $token, $USER->id);
        $output .= miuniversidad_js_status($CFG->miuniversidad_app_name, "CONNECTED");
        $output .= html_writer::tag('p', get_string('connectsuccess', 'message_miuniversidad'));
    } catch (\Exception $e) {
        $output .= miuniversidad_js_status($CFG->miuniversidad_app_name, "ERROR");
        $output .= html_writer::tag('p', get_string('connecterror', 'message_miuniversidad'));
    }
}
else {
    $privileges = miuniversidad_privileges();
    $output .= miuniversidad_js_status($CFG->miuniversidad_app_name, "CONNECTING");
    
    $output .= html_writer::tag('h2', get_string('privilegesheader', 'message_miuniversidad'));

    $output .= html_writer::start_tag('ul');
    foreach ($privileges as $key1 => $values) {
        foreach ($values as $key2 => $value) {
            $output .= html_writer::tag('li', $value);
        }
    }
    $output .= html_writer::end_tag('ul');

    $url = '';
    $output .= html_writer::start_tag('form', array('method' => 'POST', 'action' => $url));
    $output .= html_writer::empty_tag('input', array('name' => 'confirm', 'value' => get_string('accept_privileges', 'message_miuniversidad'), 'type' => 'submit'));
    $output .= html_writer::end_tag('form');
}

echo $OUTPUT->header();
echo $OUTPUT->box($output, 'generalbox');
echo $OUTPUT->footer();
die();
