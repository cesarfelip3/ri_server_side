<?php

namespace Model;

use \Model\Model;

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
        $this->db->insert($this->table, $data);

        return $data["user_uuid"];
    }

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

    public static function table()
    {
        return "user";
    }

}