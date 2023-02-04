<?php
namespace App;

class DriveFolder
{
    public function __construct(Array $folder)
    {
        $this->id = $folder['id'];
        $this->name = $folder['folder_name'];
        $this->created_at = $folder['created_at'];
        $this->updated_at = $folder['updated_at'];
        $this->default_folder = $folder['default_folder'];
        $this->platform = $folder['platform'];
    }
}
