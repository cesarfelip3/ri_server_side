<?php

namespace Model;

use \Model\Model;

class Image extends Model {


    public $table = "image";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public function addImage ($data)
    {

        $data["image_uuid"] = uniqid();
        $data["create_date"] = time();
        $data["modified_date"] = time ();

        $this->db->insert($this->table, $data);

        return $data["image_uuid"];
    }

    public function updateImage ($data)
    {

        $image_uuid = $data["image_uuid"];
        unset($data["image_uuid"]);
        $this->db->update($this->table, $data, array('image_uuid'=>$image_uuid));
    }

    public function deleteImage ($imageId)
    {
        $this->db->delete($this->table, array('token' => $data["token"]));
    }

    public function imageExists ($imageId)
    {
        $uuid = $this->db->fetchColumn("SELECT image_uuid FROM {$this->table} WHERE image_uuid=?", array ($imageId));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }

    // get images
    public function getImagesByUser ($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];
        $user_uuid = $data["user_uuid"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";
        $result = $this->db->fetchAll ("SELECT `image_uuid`, `name`, `description`, `width`, `height`, `create_date`, `modified_date`, `file_name` FROM {$this->table} WHERE user_uuid=? ORDER BY modified_date DESC LIMIT {$limit} ", array ($user_uuid));

        if (!empty ($result)) {

            foreach ($result as &$item) {

            }
        }

        return $result;

    }

    public function getLatestImages($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $userTable = User::table();

        $sql = "SELECT img.image_uuid, img.name, img.description, img.width, img.height, img.create_date, img.modified_date, img.file_name, usr.token AS user_token, usr.user_uuid AS user_uuid, usr.fullname AS fullname, usr.username AS username FROM {$this->table} img INNER JOIN $userTable usr ON img.user_uuid=usr.user_uuid  ORDER BY modified_date DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }

    public function getImagesWithoutThumbnail($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $userTable = User::table();

        $sql = "SELECT img.image_uuid, img.width, img.height, img.file_path, img.file_name, usr.token AS user_token, usr.user_uuid AS user_uuid, usr.fullname AS fullname, usr.username AS username FROM {$this->table} img INNER JOIN $userTable usr ON img.user_uuid=usr.user_uuid WHERE img.thumbnails=0 ORDER BY modified_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }

}