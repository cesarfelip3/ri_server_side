<?php

// How to use it to build website?
// How to use it to build REST API?

// routers ==> predefined
// initialize routers ==> Controller <==> Model <==> View

//

require_once(__DIR__ . "/../vendor/autoload.php");

$app = new Silex\Application();

// handling errors

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app->error(function (\Exception $e, $code) use ($app) {

    $message = $app->escape($e->getMessage());

    switch ($code) {
        case 404:
            $message = "404 : " . $message;
            break;
        case 500:
            $message = "500 : " . $message;
        default:
            $message = 'Unknow : ' . $message;
    }

    $error = array(
        "status" => "failure",
        "message" => $message
    );

    if ($code > 500) {
        $code = 500;
    }

    return $app->json($error, $code);
});

//use Symfony\Component\Debug\ErrorHandler;
//ErrorHandler::register();

//use Symfony\Component\Debug\ExceptionHandler;
//ExceptionHandler::register();

$os = php_uname();

if (strtolower(substr($os, 0, 3)) == "dar") {
    require_once(__DIR__ . "/config-test.php");
} else {
    require_once(__DIR__ . "/config.php");
}

// define global value for app

$app["debug"] = $config["debug"];
$app['upload.image.host'] = $config["upload.image.host"];
$app['upload.folder'] = $config["upload.folder"];
$app['upload.folder.image'] = $config["upload.folder.image"];

$app->register(new Silex\Provider\DoctrineServiceProvider(), $config["db"]);
$app->register(new Silex\Provider\HttpCacheServiceProvider(), $config["cache"]);

// modules ==> controller ==> model
// how to define modules ?

$basename = $config["basename"];
$api_v1 = $config["router_apiv1"];


use Model\Model;

Model::$DB = $app['db'];

//==========================
// API module routers
//==========================

use Api\Controller;

$api = $app["controllers_factory"];

// http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/
// http://www.faqs.org/rfcs/rfc2104.html
// client & server both has the private key, and client and server both has public key too
// client will use private key to encrypt the message to an token (HASH)
// server will use private key to decrypt the message to an token (HASH)

// API key, API secret (salt) ==> client & server both now, all in code
// public info == token = hash (info, API secret) + time() ==> its from who (API key) and what hash (info, API secret)

// string hash_hmac ( string $algo , string $data , string $key [, bool $raw_output = false ] )
// http://stackoverflow.com/questions/14516191/xcode-ios-hmac-sha-256-hashing

// app domain ==>

// there is no way to prevent API attack, if they copy everything in your network traffics...
// we have to know its from some client, all information are from network traffics....
// what if its same ? unless your used random API secret every time, and the network traffics changed every time...
// otherwise you can't stop it

$api->post("auth", function (Request $request) use ($app) {

    $controller = new Controller\AuthController($request, $app);

    $token = $request->get("token");
    $secrect = $request->get("secrect");
    $appdomain = $request->get("domain");

});

// tested

$api->post("user/add", function (Request $request) use ($app) {

    $controller = new Controller\UserController($request, $app);
    $ret = $controller->addUser();

    $status = 200;
    if ($ret) {
        $status = 200;
    } else {
        $status = 400;
    }

    return $app->json($controller->getError(), $status);
});

$api->post("user/image/latest", function (Request $request) use ($app) {

    $controller = new Controller\ImageController($request, $app);
    $ret = $controller->getLatestByUser();

    $status = 200;
    if ($ret) {
        $status = 200;
    } else {
        $status = 400;
    }

    return $app->json($controller->getError(), $status);
});

// image / upload
$api->post("image/upload", function (Request $request) use ($app) {

    $controller = new Controller\ImageController($request, $app);
    $ret = $controller->upload($app["upload.folder.image"]);

    $status = 200;
    if ($ret) {
        $status = 200;
    } else {
        $status = 400;
    }

    return $app->json($controller->getError(), $status);
});

$api->post("image/update", function (Request $request) use ($app) {

    $controller = new Controller\ImageController($request, $app);
    $ret = $controller->updateInfo();

    $status = 200;
    if ($ret) {
        $status = 200;
    } else {
        $status = 400;
    }

    return $app->json($controller->getError(), $status);
});

$api->post("image/latest", function (Request $request) use ($app) {

    $controller = new Controller\ImageController($request, $app);
    $ret = $controller->getLatest();

    $status = 200;
    if ($ret) {
        $status = 200;
    } else {
        $status = 400;
    }

    return $app->json($controller->getError(), $status);
});

$api->before(function (Request $request) {

    return null;
});

$app->mount($basename . "/" . $api_v1, $api);

//==================================
// test case
//==================================

// tests are here, but not right place I think

$test = $app["controllers_factory"];

$test->get("user/add", function () use ($app) {

    $file_name_with_full_path = realpath(__DIR__ . "/pi-512.png");
    $post = array(
        'email' => '123456@abc.com',
        'token' => 'bbad2323adfadsf'
    );

    $target_url = "http://localhost/gajeweb/api/v1/user/add";

    require_once __DIR__ . '/test/Curl.class.php';

    $curl = new Curl();


    $curl->post($target_url, $post);
    print_r (json_encode($curl->response));

    exit;
});

$test->get("image/upload/{userId}", function ($userId) use ($app) {

    $file_name_with_full_path = realpath(__DIR__ . "/pi-512.png");
    $post = array('user_uuid' => $userId, 'fileinfo' => '@' . $file_name_with_full_path);

    $target_url = "http://localhost/gajeweb/api/v1/image/upload";

    require_once __DIR__ . '/test/Curl.class.php';

    $curl = new Curl();

    $curl->post($target_url, $post);
    print_r (json_encode($curl->response));

    exit;
});


$test->get("user/image/latest/{userId}", function ($userId) use ($app) {

    $file_name_with_full_path = realpath(__DIR__ . "/pi-512.png");
    $post = array('page' => 0, 'page_size' => 50, 'user_uuid' => $userId);

    $target_url = "http://localhost/gajeweb/api/v1/user/image/latest";

    require_once __DIR__ . '/test/Curl.class.php';

    $curl = new Curl();

    $curl->post($target_url, $post);
    print_r (json_encode($curl->response));

    exit;
});

$test->get("image/latest", function () use ($app) {

    $post = array('page' => 0, 'page_size' => 50);

    $target_url = "http://localhost/gajeweb/api/v1/image/latest";

    require_once __DIR__ . '/test/Curl.class.php';

    $curl = new Curl();

    $curl->post($target_url, $post);
    print_r (json_encode($curl->response));

    exit;
});


$test->get("start", function () use ($app) {


    require_once __DIR__ . '/test/Curl.class.php';

    $curl = new Curl();

    $baseurl = "http://localhost/gajeweb/testcase/";

    // add user
    // upload image
    $user_add = "user/add";
    $image_upload = "image/upload/53739b9ca8183";

    // get latest from user
    // get latest image
    $user_image_latest = @"user/image/latest/53739b9ca8183";
    $image_latest = @"image/latest";

    $router = array(
        $user_add => 1,
        $image_upload => 1,
        $user_image_latest => 0,
        $image_latest => 0
    );

    foreach ($router as $route => $value) {

        $target_url = $baseurl . $route;

        $curl->get ($target_url);

        print_r("\nURL = ");
        print_r($target_url);
        print_r("\nRESULT = ");

        $result = $curl->response;
        $pretty = json_encode(json_decode($curl->response), JSON_PRETTY_PRINT);
        if ($pretty == false) {
            print_r($result);
        } else {
            print_r($pretty);
        }

        print_r("\n");

    }

    curl_close($ch);

    exit;
});

$app->mount($basename . "/" . $config["router_test"], $test);
$app->run();


