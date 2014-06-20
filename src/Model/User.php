<?php

namespace Model;

use \Model\Model;
use \Model\Todo;
use \Model\Appointment;

class User extends Model
{

    public $table = "user";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    // we will add user and its token to db
    // this is called from the app

    public function addUser($data)
    {

        $data["user_uuid"] = uniqid();
        $data["create_date"] = time();
        $data["modified_date"] = time();
        $data["dev_token"] = $this->convertToken($data["dev_token"]);
        $this->db->insert($this->table, $data);

        return $data["user_uuid"];
    }

    // we will change the token, from apple documentation
    // this token could be changed constantly
    // if the token is changed, and db value not updated
    // then the message will not be able to send to the user

    public function updateUser($data)
    {
        $user_uuid = $data["user_uuid"];
        unset ($data["user_uuid"]);
        $data["modified_date"] = time();
        $this->db->update($this->table, $data, array('user_uuid' => $user_uuid));
    }

    public function deleteUser($data)
    {
        $this->db->delete($this->table, array('token' => $data["token"]));
    }

    //=============================
    //
    //=============================

    public function userExistsByEmail($email)
    {
        $uuid = $this->db->fetchColumn("SELECT user_uuid FROM {$this->table} WHERE `email`=?", array($email));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }

    public function userExists($userId)
    {
        $uuid = $this->db->fetchColumn("SELECT user_uuid FROM {$this->table} WHERE user_uuid=?", array($userId));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }


    // get all todo and appointment from current user
    // this could be a long list, so it's paging based

    public function getAllNotification ($data)
    {

        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $table_appointment = Appointment::table();
        $table_todo = Todo::table();
        $table_user = User::table();

        $currentTime = time ();

        $result = array ();
        $result_todo = $this->db->fetchAll ("SELECT * FROM $table_todo WHERE status=? AND alarm<=? LIMIT {$limit}", array (0, $currentTime));

        // we only get these todo from valid user
        //

        $_todo = new Todo();

        foreach ($result_todo as $key => $todo) {

            $user_uuid = $todo["user_uuid"];
            $token = $this->getDevTokenByUUID($user_uuid);

            if (empty ($token)) {
                continue;
            }

            $todo["dev_token"] = $token;
            $result[$key] = $todo;

            $_data["status"] = 1;
            $_data["todo_uuid"] = $todo["todo_uuid"];
            $_todo->updateTodo($_data);
        }

        $result_appointment = $this->db->fetchAll ("SELECT * FROM $table_appointment WHERE status=? AND alarm<=? LIMIT {$limit}", array (0, $currentTime));

        // we only get these todo from valid user
        //

        $_todo = new Appointment();
        foreach ($result_appointment as $key => $todo) {

            $user_uuid = $todo["user_uuid"];
            $token = $this->getDevTokenByUUID($user_uuid);

            if (empty ($token)) {
                continue;
            }

            $todo["dev_token"] = $token;
            $result[$key] = $todo;

            $_data["status"] = 1;
            $_data["appointment_uuid"] = $todo["appointment_uuid"];
            $_todo->updateAppointment($_data);
        }

        return $result;

    }

    public function getDevTokenByUUID ($user_uuid)
    {
        $token = $this->db->fetchColumn("SELECT dev_token FROM {$this->table} WHERE `user_uuid`=? AND dev_token_disable=?", array($user_uuid, 0));

        if (empty ($token)) {
            return false;
        }

        return $token;
    }

    //=======================================
    //
    //=======================================

    public function convertToken ($token)
    {

        $token_segment_array = explode("_", $token);
        $dev_token = "";

        foreach ($token_segment_array as $segment) {

            $value = dechex(intval($segment));
            $value .= "";

            if (strlen($value) == 1) {
                $value = "0" . $value;
            }

            $dev_token .= $value;
        }

        return $dev_token;

    }

    public static function table()
    {
        return "user";
    }

}