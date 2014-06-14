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

    public function addUser($uploadFolder)
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

            $uuid = $user->addUser($data);
            if (empty ($uuid)) {
                return $this->setFailed("add user wrong");
            }

            $userId = $uuid;
        } else {

            $data['user_uuid'] = $uuid;
            $user->updateUser($data);
        }

        $user_uuid = $userId;

        $this->setSuccess("", array ("user_uuid"=>$user_uuid));
        return true;
    }

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

        if ($latency_end - $alarm - ($latency_end - $latency_start) * 2 <= 0) {

            return $this->setFailed("We are not able to send you remote notification #");
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

    public function addAppointment ()
    {
        $token = $this->request->get("token", "");
        $name = $this->request->get("name", "");
        $description = $this->request->get("description", "");
        $time = $this->request->get("time", 0);

        $time = intval ($time);
        if ($time == 0) {
            return $this->setFailed("The timestamp is invalid");
        }

        $data["name"] = $name;
        $data["description"] = $description;
        $data["time"] = $time;

        $user = new User();
        $user_uuid = $user->userExistsByToken($token);
        if (!$user_uuid) {
            return $this->setFailed("No user for this token = $token");
        }

        $data["user_uuid"] = $user_uuid;

        $appoint = new Appointment();

        if ($appint->addAppoint()) {

        } else {
            return $this->setFailed("Wrong db operation");
        }

        return true;
    }

}