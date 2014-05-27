<?php

namespace Model;

use \Model\Model;

class Tag extends Model {

    public $table = "user";

    public function __construct()
    {
        $this->db = self::$DB;
    }

}