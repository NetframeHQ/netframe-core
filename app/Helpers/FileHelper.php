<?php

namespace App\Helpers;

// @TODO dead code, seems unused

/**
 *
 *
 * Helper File for driving folders and files
 */
class FileHelper
{

    /**
     * Map a directory and child folders / files. $direcotry_depth 0 default or set number
     * depth do you want
     *
     * @param string $source_dir
     * @param int $directory_depth
     * @param boolean $hidden
     * @return multitype:string array folder and files or false
     */
    public static function directoryMap($source_dir, $directory_depth = 0, $hidden = false)
    {
        if ($fp = @opendir($source_dir)) {
            $filedata   = array();
            $new_depth  = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

            while (false !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' or $file === '..' or ($hidden === false && $file[0] === '.')) {
                    continue;
                }

                is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 or $new_depth > 0) && is_dir($source_dir.$file)) {
                    $filedata[$file] = static::directoryMap($source_dir.$file, $new_depth, $hidden);
                } else {
                    $filedata[] = $file;
                }
            }

            closedir($fp);
            return $filedata;
        }

        return false;
    }


    /**
     * Read file and open file specified in the path and return content string
     *
     * @param string $file
     * @return string file content
     */
    public static function readFile($file)
    {
        return file_get_contents($file);
    }


    /**
     *  Write data to the file specified
     *
     * @param string $path
     * @param string $data
     * @param string $mode fopen() mode default: 'wb'
     * @return boolean
     */
    public static function writeFile($path, $data, $mode = 'wb')
    {
        if (! $fp = @fopen($path, $mode)) {
            return false;
        }
        flock($fp, LOCK_EX);
        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($data, $written))) === false) {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return is_int($result);
    }


    /**
     * Delete all files contained in the supplied directory path
     *
     * @param string $path  File Path
     * @param bool $del_dir     Whether to delete any directories found in the path
     * @param bool $htdocs  Whether to skip deleting
     * @param int $_level   Current directory depth level default: 0
     * @return boolean
     */
    public static function deleteFiles($path, $del_dir = false, $htdocs = false, $_level = 0)
    {
        // Trim the trailing slash
        $path = rtrim($path, '/\\');
        if (! $current_dir = @opendir($path)) {
            return false;
        }
        while (false !== ($filename = @readdir($current_dir))) {
            if ($filename !== '.' && $filename !== '..') {
                if (is_dir($path.DIRECTORY_SEPARATOR.$filename) && $filename[0] !== '.') {
                    delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $htdocs, $_level + 1);
                } elseif ($htdocs !== true
                    or ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)) {
                    @unlink($path.DIRECTORY_SEPARATOR.$filename);
                }
            }
        }
        closedir($current_dir);
        return ($del_dir === true && $_level > 0) ? @rmdir($path) : true;
    }


    /**
     * Get Filenames
     *
     * Reads the specified directory and builds an array containing the filenames.
     * Any sub-folders contained within the specified path are read as well.
     *
     * @param   string  path to source
     * @param   bool    whether to include the path as part of the filename
     * @param   bool    internal variable to determine recursion status - do not use in calls
     * @return  array
     */
    public static function getFilenames($source_dir, $include_path = false, $_recursion = false)
    {
        static $_filedata = array();
        if ($fp = @opendir($source_dir)) {
            // reset the array and make sure $source_dir has a trailing slash on the initial call
            if ($_recursion === false) {
                $_filedata = array();
                $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            }
            while (false !== ($file = readdir($fp))) {
                if (is_dir($source_dir.$file) && $file[0] !== '.') {
                    static::getFilenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, true);
                } elseif ($file[0] !== '.') {
                    $_filedata[] = ($include_path === true) ? $source_dir.$file : $file;
                }
            }
            closedir($fp);
            return $_filedata;
        }
        return false;
    }
}
