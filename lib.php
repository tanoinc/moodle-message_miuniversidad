<?php

/**
 * miuniversidad message processor - lib file
 * (Made from Andrew Davis "custom" package)
 * 
 * @author Luciano Coggiola 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package message_miuniversidad
 */
define('FORMAT_JSON', 'Content-Type: application/json');
define('FORMAT_URLENCODED', 'Content-Type: application/x-www-form-urlencoded');

define('METHOD_GET', 'GET');
define('METHOD_POST', 'POST');
define('METHOD_PUT', 'PUT');
define('METHOD_DELETE', 'DELETE');
define('HMAC_HASH_FUNCTION', 'sha256');

function miuniversidad_query_service($url, $api_key, $api_secret, $parameters = null, $method = METHOD_GET, $format = FORMAT_JSON)
{
    $session = curl_init($url);
    if ($parameters) {
        if ($format == FORMAT_JSON) {
            $string_parameters = json_encode($parameters);
        }
        elseif ($format == FORMAT_URLENCODED) {
            $string_parameters = http_build_query($parameters);
        }
        else {
            throw new \Exception('Formato desconocido: ' . $format);
        }
        curl_setopt($session, CURLOPT_POSTFIELDS, $string_parameters);
    }

    if ($method == METHOD_DELETE) {
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    elseif ($method == METHOD_PUT) {
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'PUT');
    }
    else {
        if ($parameters) {
            curl_setopt($session, CURLOPT_POST, 1);
        }
    }

    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);

    $headers = array();
    if ($format == FORMAT_JSON) {
        $headers[] = 'Content-Type: application/json;';
    }
    $headers[] = 'Authorization: APIKEY ' . $api_key . ':' . generate_hash(generate_content($url, $method, $parameters), $api_secret);
    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
    $response_raw = curl_exec($session);
    $headers = curl_getinfo($session);

    curl_close($session);
    if ($headers['http_code'] >= '200' and $headers['http_code'] <= 299) {
        $response = json_decode($response_raw);
        if ($response === null) {
            throw new \Exception($response_raw);
            return false;
        }
    }
    else {
        //echo 'curl -kv -X POST -H "Content-Type: application/json" -d "'. urldecode($parametros_str).'" '.$request; die();
        $response = json_decode($response_raw);
        if ($response !== null) {
            throw new \Exception($response_raw);
        }
        else {
            throw new \Exception($headers['http_code']);
        }
        return false;
    }

    return $response;
}

function generate_hash($content, $api_secret)
{
    return hash_hmac(HMAC_HASH_FUNCTION, $content, $api_secret);
}

function generate_content($url, $method, $output)
{
    $content = [
        'full_url' => $url,
        'method' => $method,
        'input' => ($output ? $output : []),
    ];
    return json_encode($content);
}

function miuniversidad_send_news($title, $message, $global, $recipients, $send_notification = true, $context = null)
{
    global $CFG;
    $parameters = array(
        'title' => $title,
        'content' => $message,
        'global' => ($global ? "1" : "0"),
        'send_notification' => ($send_notification ? "1" : "0"),
    );
    if (!empty($recipients)) {
        $parameters['users'] = $recipients;
    }
    if (!empty($context)) {
        $parameters['context_name'] = strtolower($context['name']);
        $parameters['context_description'] = trim($context['description']);
    }

    return miuniversidad_query_service($CFG->miuniversidad_url . 'api/v1/newsfeed', $CFG->miuniversidad_api_key, $CFG->miuniversidad_api_secret, $parameters, METHOD_POST);
}

function miuniversidad_send_event($name, $date, $duration, $description, $ubication, $global, $recipients, $send_notification = true, $context = null)
{
    global $CFG;

    $parameters = array(
        'event_name' => $name,
        'event_date' => $date,
        'event_duration' => $duration,
        'event_description' => $description,
        'event_location' => $ubication,
        'global' => ($global ? "1" : "0"),
        'context_name' => strtolower(utf8_encode($context['nombre'])),
        'context_description' => trim(utf8_encode($context['descripcion'])),
        'send_notification' => ($send_notification ? "1" : "0"),
    );

    if (!empty($recipients)) {
        if (is_array($recipients)) {
            $parameters['users'] = $recipients;
        }
        else {
            $parameters['users'] = [$recipients];
        }
    }

    return miuniversidad_query_service($CFG->miuniversidad_url . 'api/v1/calendar_event', $CFG->miuniversidad_api_key, $CFG->miuniversidad_api_secret, $parameters, METHOD_POST);
}

function miuniversidad_connect($id, $token, $userid)
{
    global $CFG;
    $parameters = array('external_id' => $userid);

    return miuniversidad_query_service($CFG->miuniversidad_url . 'api/v1/application/subscription/' . $id . '/' . $token, $CFG->miuniversidad_api_key, $CFG->miuniversidad_api_secret, $parameters, METHOD_PUT);
}

function miuniversidad_privileges($level = 'user')
{
    global $CFG;
    $privileges = miuniversidad_query_service($CFG->miuniversidad_url. "api/v1/privileges/granted", $CFG->miuniversidad_api_key, $CFG->miuniversidad_api_secret);
    $processed_privileges = array();
    foreach ($privileges as $privilege) {
        if ($level == $privilege->level) {
            $part = explode(':', $privilege->name);
            $processed_privileges['privilege.' . $part[0]]['privilege.' . $part[1]] = get_string($privilege->description,'message_miuniversidad');
        }
    }

    return $processed_privileges;
}
