<?php
// database


$config["db"] = array (

    "db.options" => array (
        "driver" => "pdo_mysql",
        "host" => "localhost",
        "port" => "3306",
        "user" => "root",
        "password" => "",
        "dbname" => "riapp",
        // "charset" => "",
    )
);

// cache

$config["cache"] = array (
    'http_cache.cache_dir' => __DIR__ . '/cache/',
);

// basename
// route prefix

$config["basename"] = "/riweb";
$config["router_apiv1"] = "api/v1/";
$config["router_test"] = "testcase/";

// upload
$config["debug"] = true;
$config["certificates.folder"] = __DIR__ . DIRECTORY_SEPARATOR . "certificates" . DIRECTORY_SEPARATOR;
$config['upload.image.host'] = 'http://localhost/image/';
$config['upload.folder'] = realpath(__DIR__ . "/../../upload/") . DIRECTORY_SEPARATOR;
$config['upload.folder.image'] = $config["upload.folder"] . "image" . DIRECTORY_SEPARATOR;