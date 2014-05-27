<?php

namespace Model;

use \Model\Model;

class Person extends Model
{

    public $table = "person";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public static function table()
    {
        return "person";
    }
}
