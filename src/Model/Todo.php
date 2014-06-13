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

    public function addTodo ()
    {

        $data["todo_uuid"] = uniqid();
        $data["create_date"] = time ();

        $this->db->insert($this->table, $data);

        return true;
    }
}