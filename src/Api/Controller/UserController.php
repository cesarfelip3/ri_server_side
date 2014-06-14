<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\Controller\BaseController;

use Model\User;
use Model\Todo;
use Model\Appointment;

class UserController extends BaseController
{

    public function __construct($request, $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = new Response();
    }

    // enable or disable remote push notification
    // if the app is going to background, then enable it
    // if the app is going to foreground, then disable it

    // when the user login, the push notification is disabled
    // only if the user is available, then it's enabled

    public function switchPushNotification ()
    {

        $user_uuid = $this->request->get("user_uuid", "");
        $disable = $this->request->get("disable", 0);

        $data["user_uuid"] = $user_uuid;
        $data["dev_token_disable"] = $disable;

        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        $user->updateUser($data);

    }

    // if the app user login, we will add user to push notification server
    // this user will be able to sync its todo and appointment in server

    public function addUser()
    {

        $email = $this->request->get("email", "");
        $username = $this->request->get("username", "");
        $dev_token = $this->request->get("dev_token", "");

        $data = array(
            "email" => $email,
            "username" => $username,
            "dev_token" => $dev_token
        );

        if (empty ($email)) {
            return $this->setFailed("invalid user email");
        }

        $user = new User();
        $uuid = "";
        $uuid = $user->userExistsByEmail($email);
        $userId = $uuid;

        if (empty($userId)) {

            $data["dev_token"] = $user->convertToken($data["dev_token"]);
            $uuid = $user->addUser($data);
            if (empty ($uuid)) {
                return $this->setFailed("add user wrong");
            }

            $userId = $uuid;
        } else {

            $data['user_uuid'] = $uuid;
            $data["dev_token"] = $user->convertToken($data["dev_token"]);
            $user->updateUser($data);
        }

        $user_uuid = $userId;

        $this->setSuccess("", array ("user_uuid"=>$user_uuid));
        return true;
    }

    // add todo for the app user

    public function addTodo ()
    {
        $user_uuid = $this->request->get("user_uuid", "");
        $name = $this->request->get("name", "");
        $description = $this->request->get("description", "");

        $todo_id = $this->request->get("todo_id", 0);
        $alert_id = $this->request->get("alert_id", 0);

        // alarm time
        $alarm = $this->request->get("alarm", 0);

        $latency_start = $this->request->get("latency_start", 0);
        $latency_end = time ();

        if (empty ($alarm)) {
            return $this->setFailed("Alarm time should not be empty");
        }

        if ($alarm - $latency_end <= 60) {

            return $this->setFailed("We are not able to send you remote notification #", array("latency_end"=>$latency_end, "latency_start"=>$latency_start, "alarm"=>$alarm));

        }

        $data["user_uuid"] = $user_uuid;
        $data["name"] = $name;
        $data["description"] = $description;
        $data["user_info"] = json_encode(array ("todo_id"=>$todo_id, "alert_id"=>$alert_id, "type"=>"todo"));

        $data["alarm"] = $alarm;

        $data["latency_start"] = $latency_start;
        $data["latency_end"] = $latency_end;


        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        $todo = new Todo();
        if ($todo->addTodo($data)) {

        } else {
            return $this->setFailed("Wrong db operation");
        }

        return true;
    }

    // add appointment for current user
    // under development

    public function addAppointment ()
    {
        $user_uuid = $this->request->get("user_uuid", "");
        $name = $this->request->get("name", "");
        $description = $this->request->get("description", "");

        $todo_id = $this->request->get("todo_id", 0);
        $alert_id = $this->request->get("alert_id", 0);

        // alarm time
        $alarm = $this->request->get("alarm", 0);

        $latency_start = $this->request->get("latency_start", 0);
        $latency_end = time ();

        if (empty ($alarm)) {
            return $this->setFailed("Alarm time should not be empty");
        }

        if ($alarm - $latency_end <= 60) {

            return $this->setFailed("We are not able to send you remote notification #", array("latency_end"=>$latency_end, "latency_start"=>$latency_start, "alarm"=>$alarm));

        }

        $data["user_uuid"] = $user_uuid;
        $data["name"] = $name;
        $data["description"] = $description;
        $data["user_info"] = json_encode(array ("todo_id"=>$todo_id, "alert_id"=>$alert_id, "type"=>"todo"));

        $data["alarm"] = $alarm;

        $data["latency_start"] = $latency_start;
        $data["latency_end"] = $latency_end;


        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        $todo = new Appointment();
        if ($todo->addAppointment($data)) {

        } else {
            return $this->setFailed("Wrong db operation");
        }

        return true;
    }

}