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

        // Then declare an adapter.
        $apnsAdapter = new ApnsAdapter(array(
            'certificate' => $certificate,
            'passPhrase' => 'example',
        ));

        // $this->debug($pushManager);

        // get devices from user table


        $user = new User();

        $result = $user->getAllNotification($data);

        //$this->debug($certificate, true, false);
        //$this->debug($result);

        foreach ($result as $notification) {

            $devToken = $notification["dev_token"];

            // Set the device(s) to push the notification to.
            $devices = new DeviceCollection(array(
                new Device($devToken, array('badge' => 1))
            ));

            // Then, create the push skel.
            $message = new Message('This is an example.', array(
                'badge' => 1,
                'sound' => 'example.aiff',

                'actionLocKey' => 'Action button title!',
                'locKey' => 'localized key',
                'locArgs' => array(
                    'localized args',
                    'localized args',
                    'localized args'
                ),
                'launchImage' => 'image.jpg',

                'custom' => array('custom data' => array(
                    'we' => 'want', 'send to app'
                ))
            ));

            // Finally, create and add the push to the manager, and push it!
            $push = new Push($apnsAdapter, $devices, $message);
            $pushManager->add($push);
            $pushManager->push(); // Returns a collection of notified devices

        }


    }

}