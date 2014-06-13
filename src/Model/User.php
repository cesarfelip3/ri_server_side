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

    public function addUser($data)
    {

        $data["user_uuid"] = uniqid();
        $data["create_date"] = time();
        $data["modified_date"] = time();
        $data["dev_token"] = $this->convertToken($data["dev_token"]);
        $this->db->insert($this->table, $data);

        return $data["user_uuid"];
    }

    public function updateUser($data)
    {
        $user_uuid = $data["user_uuid"];
        unset ($data["user_uuid"]);
        $data["modified_date"] = time();
        $data["dev_token"] = $this->convertToken($data["dev_token"]);
        $this->db->update($this->table, $data, array('user_uuid' => $user_uuid));
    }

    public function deleteUser($data)
    {
        $this->db->delete($this->table, array('token' => $data["token"]));
    }

    public function userExistsByToken($userId)
    {
        $uuid = $this->db->fetchColumn("SELECT user_uuid FROM {$this->table} WHERE token=?", array($userId));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }

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

    public function getAllNotification ($data)
    {

        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $table_appoint = Appointment::table();
        $table_todo = Todo::table();
        $table_user = User::table();

        $result_todo = $this->db->fetchAll ("SELECT * FROM $table_todo WHERE status=? LIMIT {$limit}", array (0));

        foreach ($result_todo as $key => $todo) {

            $user_uuid = $todo["user_uuid"];
            $token = $this->getDevTokenByUUID($user_uuid);

            if (empty ($token)) {
                $this->setFailed("Invalid empty dev token for user#$user_uuid");
                return false;
            }

            $todo["dev_token"] = $token;
            $result_todo[$key] = $todo;
        }

        //$result_appoint = $this->db->fetchAll ("SELECT * FROM $table_appoint WHERE status=?", array (1));

        $result = array_merge($result_todo);

        return $result;

    }

    public function getDevTokenByUUID ($user_uuid)
    {
        $token = $this->db->fetchColumn("SELECT dev_token FROM {$this->table} WHERE `user_uuid`=?", array($user_uuid));

        if (empty ($token)) {
            return false;
        }

        return $token;
    }

    public function convertToken ($token)
    {

        $token_segment_array = explode("_", $token);
        $dev_token = "";

        foreach ($token_segment_array as $segment) {

            $dev_token .= dechex(intval($segment));
        }

        return $dev_token;

    }

    public static function table()
    {
        return "user";
    }

}