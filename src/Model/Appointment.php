<?php

namespace Model;

use \Model\Model;

class Appointment extends Model
{

    public $table = "appointment";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public static function table()
    {
        return "appointment";
    }

    public function addAppoint ()
    {
        $data["appointment_uuid"] = uniqid();
        $data["create_date"] = time ();

        $this->db->insert($this->table, $data);

        return true;
    }
}