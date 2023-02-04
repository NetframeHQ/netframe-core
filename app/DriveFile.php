<?php
namespace App;

class DriveFile
{
    public function __construct(Array $file)
    {
        $this->id = $file['id'];
        $this->name = $file['name'];
        $this->file_name = $file['file_name'];
        $this->mime_type = $file['mime_type'];
        $this->created_at = $file['created_at'];
        $this->updated_at = $file['updated_at'];
        $this->file_path = $file['file_path'];
        $this->platform = $file['platform'];
        $this->type = $file['type'];
    }

    public function getType()
    {
        return "Media";
    }

    public function isTypeDisplay()
    {
        return $this->type == 0;
    }
}
