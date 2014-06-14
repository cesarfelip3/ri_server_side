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

        return true;
    }
}