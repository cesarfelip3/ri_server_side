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
            $data["dev_token_disable"] = 1;
            $user->updateUser($data);
        }

        // every time we registered update a dev token
        // we will have to remove all those alert entity

        $todo = new Todo();
        $todo->deleteTodoByUser($user_uuid);

        $user_uuid = $userId;
        $this->setSuccess("", array("user_uuid" => $user_uuid));
        return true;
    }


    // enable or disable remote push notification
    // if the app is going to background, then enable it
    // if the app is going to foreground, then disable it

    // when the user login, the push notification is disabled
    // only if the user is available, then it's enabled

    public function switchPushNotification()
    {

        $user_uuid = $this->request->get("user_uuid", "");
        $disable = $this->request->get("disable", 0);
        $user_info_list = $this->request->get("user_info_list", array ());

        $data["user_uuid"] = $user_uuid;
        $data["dev_token_disable"] = 0;

        if (empty ($user_info_list)) {

            return $this->setFailed("Empty user info");
        }

        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        //return $this->setSuccess("", array ("a"=>json_decode($user_info_list), "j"=>$user_info_list));

        $user->updateUser($data);
        $user_info_list = json_decode($user_info_list, true);

        if (empty ($user_info_list)) {
            return $this->setFailed("empty user info list");
        }

        return $this->setSuccess("" . var_export($user_info_list, true));

        $info = array ();
        foreach ($user_info_list as $user_info) {

            $user_info["user_uuid"] = $user_uuid;
            //return $this->setSuccess("", var_export($user_info, true));

            if ($user_info["type"] == "todo") {
                $this->addTodo($user_info);
            }

            if ($user_info["type"] == "appointment") {
                $this->addAppointment($user_info);
            }
        }

        return $this->setSuccess("");

    }

    // add todo for the app user

    protected function addTodo($user_info)
    {
        $user_uuid = $user_info["user_uuid"];
        $name = "";
        $description = $user_info["description"];

        $todo_id = $user_info["todo_id"];
        $alert_id = $user_info["alert_id"];
        $type = $user_info["type"];
        $alarm = $user_info["alarm"];

        $latency_start = $user_info["latency_start"];
        $latency_end = time();

        if (empty ($alarm)) {
            return $this->setFailed("Alarm time should not be empty");
        }

        $data["user_uuid"] = $user_uuid;
        $data["name"] = $name;
        $data["description"] = $description;
        $data["user_info"] = json_encode(array("todo_id" => $todo_id, "alert_id" => $alert_id, "type" => $type));

        $data["alarm"] = $alarm;

        $data["latency_start"] = $latency_start;
        $data["latency_end"] = $latency_end;

        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        $todo = new Todo();
        $todo_uuid = $todo->todoExists($data);

        if ($todo_uuid !== false) {

            if ($alarm - $latency_end <= 60) {

                $todo->deleteTodo($todo_uuid);
                return $this->setFailed("We are not able to send you remote notification #", array("latency_end" => $latency_end, "latency_start" => $latency_start, "alarm" => $alarm));

            }

            $data["todo_uuid"] = $todo_uuid;
            $data["status"] = 0;
            if ($todo->updateTodo($data)) {

            } else {
                return $this->setFailed("Wrong db operation");
            }

        } else {

            if ($alarm - $latency_end <= 60) {

                return $this->setFailed("We are not able to send you remote notification #", array("latency_end" => $latency_end, "latency_start" => $latency_start, "alarm" => $alarm));

            }

            $todo_uuid = $todo->addTodo($data);
            if ($todo_uuid) {

            } else {
                return $this->setFailed("Wrong db operation");
            }
        }

        return $this->setSuccess("", array ("todo_uuid"=>$todo_uuid));
    }

    // add appointment for current user
    // under development


    protected function addAppointment($user_info)
    {
        $user_uuid = $user_info["user_uuid"];
        $name = "";
        $description = $user_info["description"];

        $appointment_id = $user_info["todo_id"];
        $alert_id = $user_info["alert_id"];
        $type = $user_info["type"];
        $alarm = $user_info["alarm"];

        $latency_start = $user_info["latency_start"];
        $latency_end = time();

        if (empty ($alarm)) {
            return $this->setFailed("Alarm time should not be empty");
        }

        $data["user_uuid"] = $user_uuid;
        $data["name"] = $name;
        $data["description"] = $description;
        $data["user_info"] = json_encode(array("todo_id" => $appointment_id, "alert_id" => $alert_id, "type" => $type));

        $data["alarm"] = $alarm;

        $data["latency_start"] = $latency_start;
        $data["latency_end"] = $latency_end;

        $user = new User();
        $user_uuid = $user->userExists($user_uuid);

        if (!$user_uuid) {
            return $this->setFailed("There is no user with current id#$user_uuid");
        }

        $appointment = new Appointment();
        $appointment_uuid = $appointment->appointmentExists($data);

        if ($appointment_uuid !== false) {

            if ($alarm - $latency_end <= 60) {

                $appointment->deleteAppointment($appointment_uuid);
                return $this->setFailed("We are not able to send you remote notification #", array("latency_end" => $latency_end, "latency_start" => $latency_start, "alarm" => $alarm));

            }

            $data["appointment_uuid"] = $appointment_uuid;
            $data["status"] = 0;
            if ($appointment->updateAppointment($data)) {

            } else {
                return $this->setFailed("Wrong db operation");
            }

        } else {

            if ($alarm - $latency_end <= 60) {

                return $this->setFailed("We are not able to send you remote notification #", array("latency_end" => $latency_end, "latency_start" => $latency_start, "alarm" => $alarm));

            }

            $appointment_uuid = $appointment->addAppointment($data);
            if ($appointment_uuid) {

            } else {
                return $this->setFailed("Wrong db operation");
            }
        }

        return $this->setSuccess("", array ("appointment_uuid"=>$appointment_uuid));
    }

}