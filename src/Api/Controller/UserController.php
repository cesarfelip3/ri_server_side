<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\Controller\BaseController;

use Model\User;
use Model\Alert;
use Model\Appointment;
use Model\File;

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

        $data = array(
            "email" => $email,
            "username" => $username
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
        }

        $user_uuid = $userId;
        $file = $this->request->files->get("fileinfo");

        if (empty ($file)) {
            return $this->setFailed("file handler not exist");
        }

        if ($file->isValid()) {

            $data = array ();
            $data["mime"] = $file->getMimeType();

            $data["file_name"] = "dev_token_" . uniqid();
            $data["file_path"] = $uploadFolder;
            $data["user_uuid"] = $user_uuid;

            $data['name'] = $this->request->get("name", "Untitle");
            $data['description'] = $this->request->get("description", "Untitle file");


            if (file_exists($data["file_path"])) {

                if (false == $file->move ($data["file_path"], $data["file_name"])) {
                    return $this->setFailed("move file error");
                } else {

                    $file = new File();
                    $file->deleteFilesByUser($user_uuid);
                    $file_uuid = $file->addFile($data);

                    if (empty ($file_uuid)) {

                        return $this->setFailed("save file to db error");
                    }
                }

            } else {
                return $this->setFailed("upload folder not exist");
            }
        } else {

            // basically php.ini issue

            return $this->setFailed("invalid upload", array("result"=>$file));
        }

        $this->setSuccess("", array ("user_uuid"=>$user_uuid));
        return true;
    }

    public function addAlert ()
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

        $alert = new Alert();

        if ($alert->addAlert()) {

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