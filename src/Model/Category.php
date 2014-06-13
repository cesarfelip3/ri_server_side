<?php


namespace Model;

use \Model\Model;

class Category extends Model {
    public $table = "user";

    public function __construct()
    {
        $this->db = self::$DB;
    }


}