<?php

require '/usr/local/apache/share/bbcmicrocouk/predis/autoload.php';

$cachettl = 3600; // number of seconds before we invalidate the cache

$rediscache = new Predis\Client('tcp://127.0.0.1:6379');

function cache_obj_set($key, $obj)
{
    global $cachettl, $rediscache;

    $payload = [
        'type' => gettype($obj),
        'class' => is_object($obj) ? get_class($obj) : null,
        'data' => $obj
    ];

    $rediscache->setex(
        $key,
        $cachettl,
        json_encode($payload)
    );
}

function cache_obj_get($key)
{
    global $rediscache;

    $json = $rediscache->get($key);

    if($json === null) {
        return null;
    }

    $payload = json_decode($json, true);

    if(!is_array($payload) || !isset($payload['type'])) {
        return null;
    }

    switch($payload['type']) {

        case 'integer':
            return (int)$payload['data'];

        case 'double':
            return (float)$payload['data'];

        case 'boolean':
            return (bool)$payload['data'];

        case 'string':
            return (string)$payload['data'];

        case 'array':
            return (array)$payload['data'];

        case 'object':

            if(
                isset($payload['class']) &&
                class_exists($payload['class'])
            ) {

                $obj = new $payload['class'];

                foreach($payload['data'] as $k => $v) {
                    $obj->$k = $v;
                }

                return $obj;
            }

            return (object)$payload['data'];

        default:
            return $payload['data'];
    }
}

function cache_exists($key) {
  global $rediscache;
  return $rediscache->exists($key);
}

function cache_obj_expiry_get($key) {
  global $rediscache;
  return $rediscache->ttl($key);
}



