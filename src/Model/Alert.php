<?php

namespace Model;

use \Model\Model;

class Alert extends Model
{

    public $table = "alert";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public static function table()
    {
        return "alert";
    }
}