<?php

namespace Model;

use \Model\Model;

class Todo extends Model
{

    public $table = "todo";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public static function table()
    {
        return "todo";
    }

    public function addTodo ($data)
    {

        $data["todo_uuid"] = uniqid();
        $data["create_date"] = time ();
        $data["description"] = $this->db->quote ($data["description"]);
        $data["description"] = trim ($data["description"], "'");
        $this->db->insert($this->table, $data);

        return $data["todo_uuid"];
    }

    public function updateTodo ($data)
    {

        $todo_uuid = $data["todo_uuid"];
        unset ($data["todo_uuid"]);

        $data["description"] = $this->db->quote ($data["description"]);
        $data["description"] = trim ($data["description"], "'");
        $this->db->update($this->table, array ("todo_uuid" => $todo_uuid));

        return true;
    }

    public function todoExists($data)
    {
        $uuid = $this->db->fetchColumn("SELECT todo_uuid FROM {$this->table} WHERE user_info=? AND user_uuid=?", array($data["user_info"]), $data["user_uuid"]);

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }
}