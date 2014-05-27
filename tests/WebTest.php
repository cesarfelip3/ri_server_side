<?php

require __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

class WebTest extends WebTestCase {
    public function createApplication() {
        $app_env = 'test';
        return require __DIR__ . '/../web/index.php';
    }

    public function testAPI () {

        $client = $this->createClient();

        $client->request('GET','/gajeweb/testcase/user/add');

        //print_r ($client);
        print_r ($client->getResponse()->getContent());
        print_r ("\n");

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Upload form failed to load'
        );


    }
}