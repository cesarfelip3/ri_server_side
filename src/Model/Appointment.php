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

    public function addAppointment ($data)
    {

        $data["appointment_uuid"] = uniqid();
        $data["create_date"] = time ();
        $data["description"] = $this->db->quote ($data["description"]);
        $data["description"] = trim ($data["description"], "'");
        $this->db->insert($this->table, $data);

        return $data["appointment_uuid"];
    }

    public function updateAppointment ($data)
    {

        $appointment_uuid = $data["appointment_uuid"];
        unset ($data["appointment_uuid"]);

        $data["description"] = $this->db->quote ($data["description"]);
        $data["description"] = trim ($data["description"], "'");
        $this->db->update($this->table, $data, array ("appointment_uuid" => $appointment_uuid));

        return true;
    }

    public function deleteAppointment ($appointmentId)
    {
        $this->db->delete($this->table, array('appointment_uuid'=>$appointmentId));
    }

    public function deleteAppointmentByUser ($userId)
    {
        $this->db->delete($this->table, array('user_uuid'=>$userId));
    }

    public function appointmentExists($data)
    {
        $uuid = $this->db->fetchColumn("SELECT appointment_uuid FROM {$this->table} WHERE user_info=? AND user_uuid=?", array($data["user_info"], $data["user_uuid"]));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }
}

