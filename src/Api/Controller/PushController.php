<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\Controller\BaseController;

use Sly\NotificationPusher\PushManager,
    Sly\NotificationPusher\Adapter\Apns as ApnsAdapter,
    Sly\NotificationPusher\Collection\DeviceCollection,
    Sly\NotificationPusher\Model\Device,
    Sly\NotificationPusher\Model\Message,
    Sly\NotificationPusher\Model\Push;

use \Model\User;

class PushController extends BaseController
{

    public function __construct($request, $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = new Response();
    }

    // https://github.com/Ph3nol/NotificationPusher/blob/master/doc/apns-adapter.md
    public function push($certificate)
    {

        $page = $this->request->get ("page", 0);
        $pageSize = $this->request->get ("page_size", 25);

        $data = array (
            "page" => $page,
            "page_size" => $pageSize
        );

        // First, instantiate the manager
        // Example for production environment:
        // $pushManager = new PushManager(PushManager::ENVIRONMENT_PRODUCTION);
        // Development one by default (without argument).

        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        if (!file_exists($certificate)) {

            $this->setFailed("pem not exists # $certificate");
            return false;
        }

        // Then declare an adapter.
        $apnsAdapter = new ApnsAdapter(array(
            'certificate' => $certificate
        ));

        $user = new User();

        $result = $user->getAllNotification($data);

        //$this->debug($certificate, true, false);
        //$this->debug($result);

        foreach ($result as $notification) {

            $devToken = $notification["dev_token"];
            //$devToken = '111db24975bb6c6b63214a8d268052aa0a965cc1e32110ab06a72b19074c2222';

            //$this->debug($devToken);
            //$devToken = '72df6b2b4988cf8e5fea115a4814ba40eb9186e4a04e68440be98b18e6fb51bc';

            // Set the device(s) to push the notification to.
            $devices = new DeviceCollection(array(
                new Device($devToken)
            ));

            // Then, create the push skel.
            $message = new Message($notification["description"]);


            // Finally, create and add the push to the manager, and push it!
            $push = new Push($apnsAdapter, $devices, $message);

            $pushManager->add($push);
            $pushManager->push(); // Returns a collection of notified devices

        }


    }

}