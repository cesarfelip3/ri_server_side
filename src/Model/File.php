<?php

namespace Model;

use \Model\Model;

class File extends Model {


    public $table = "file";

    public function __construct()
    {
        $this->db = self::$DB;
    }

    public function addFile ($data)
    {

        $data["file_uuid"] = uniqid();
        $data["create_date"] = time();
        $data["modified_date"] = time ();

        $this->db->insert($this->table, $data);

        return $data["file_uuid"];
    }

    public function updateFile ($data)
    {

        $file_uuid = $data["file_uuid"];
        unset($data["file_uuid"]);
        $this->db->update($this->table, $data, array('file_uuid'=>$file_uuid));
    }

    public function deleteFile ($fileId)
    {
        $this->db->delete($this->table, array('token' => $data["token"]));
    }

    public function fileExists ($fileId)
    {
        $uuid = $this->db->fetchColumn("SELECT file_uuid FROM {$this->table} WHERE file_uuid=?", array ($fileId));

        if (empty ($uuid)) {
            return false;
        }

        return $uuid;
    }

    // get files
    public function getFilesByUser ($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];
        $user_uuid = $data["user_uuid"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";
        $result = $this->db->fetchAll ("SELECT `file_uuid`, `name`, `description`, `width`, `height`, `create_date`, `modified_date`, `file_name` FROM {$this->table} WHERE user_uuid=? ORDER BY modified_date DESC LIMIT {$limit} ", array ($user_uuid));

        if (!empty ($result)) {

            foreach ($result as &$item) {

            }
        }

        return $result;

    }

    public function getLatestFiles($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $userTable = User::table();

        $sql = "SELECT img.file_uuid, img.name, img.description, img.width, img.height, img.create_date, img.modified_date, img.file_name, usr.token AS user_token, usr.user_uuid AS user_uuid, usr.fullname AS fullname, usr.username AS username FROM {$this->table} img INNER JOIN $userTable usr ON img.user_uuid=usr.user_uuid  ORDER BY modified_date DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }

    public function getFilesWithoutThumbnail($data)
    {
        $page = $data["page"];
        $pageSize = $data["page_size"];

        $page = intval($page);
        $pageSize = intval($pageSize);
        $page = $page * $pageSize;

        $limit = "$page, $pageSize";

        $userTable = User::table();

        $sql = "SELECT img.file_uuid, img.width, img.height, img.file_path, img.file_name, usr.token AS user_token, usr.user_uuid AS user_uuid, usr.fullname AS fullname, usr.username AS username FROM {$this->table} img INNER JOIN $userTable usr ON img.user_uuid=usr.user_uuid WHERE img.thumbnails=0 ORDER BY modified_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }

}