<?php
namespace HMC\Document;

/*
 * Document Helper - collection of methods for working with documents
 *
 * @author Ebben Feagan - ebben@hmc-soft.com
 * @author David Carr - dave@simplemvcframework.com
 */

class Document
{
    /**
     * Determine the "type" of the file based on the extension.
     * @param  string $extension file extension
     * @return string            group name
     */
    public static function getFileType($extension)
    {
        $images = array('jpg', 'gif', 'png', 'bmp','tiff','tif','jpeg','wmf','svg','webp');
        $docs   = array('txt', 'rtf', 'doc', 'docx', 'pdf', 'odt', 'html', 'xml', 'epub', 'mobi');
        $archives = array('zip','rar','7z','gz','lzma','rpm','deb','tgz','bzip');
        $apps   = array('exe', 'bat', 'sh','bin','run');
        $video  = array('mpg', 'wmv', 'avi', 'mp4', 'mkv','webm');
        $audio  = array('wav', 'mp3', 'ogg', 'ape', 'flac', 'aac');
        $db     = array('sql', 'csv', 'xls','xlsx', 'ods', 'db');

        if (in_array($extension, $images)) {
            return "Image";
        }
        if (in_array($extension, $docs)) {
            return "Document";
        }
        if (in_array($extension, $archives)) {
          return "Archive";
        }
        if (in_array($extension, $apps)) {
            return "Application";
        }
        if (in_array($extension, $video)) {
            return "Video";
        }
        if (in_array($extension, $audio)) {
            return "Audio";
        }
        if (in_array($extension, $db)) {
            return "Database/Spreadsheet";
        }
        return "Other";
    }

    /**
     * create a human friendly measure of the size provided
     * @param  integer  $bytes     file size
     * @param  integer $precision precision to be used
     * @return string             size with measure
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Converts a human readable file size value to a number of bytes that it
     * represents. Supports the following modifiers: K, M, G and T.
     * Invalid input is returned unchanged.
     *
     * Example:
     * <code>
     * $config->getBytesSize(10);          // 10
     * $config->getBytesSize('10b');       // 10
     * $config->getBytesSize('10k');       // 10240
     * $config->getBytesSize('10K');       // 10240
     * $config->getBytesSize('10kb');      // 10240
     * $config->getBytesSize('10Kb');      // 10240
     * // and even
     * $config->getBytesSize('   10 KB '); // 10240
     * </code>
     *
     * @param number|string $value
     * @return number
     */
    public static function getBytesSize($value)
    {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't':
                    $m[1] *= 1024;
                    break;
                case 'g':
                    $m[1] *= 1024;
                    break;
                case 'm':
                    $m[1] *= 1024;
                    break;
                case 'k':
                    $m[1] *= 1024;
                    break;
            }
            return $m[1];
        }, $value);
    }

    /**
     * return the size of a folder in bytes
     * @param string $path
     * @return string
     */
    public static function getFolderSize($path_)
    {
        $bytestotal = 0;
        $path = realpath($path_);
        if($path !== false) {
          foreach(new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
          ) as $object) {
            try {
              $bytestotal += $object->getSize();
            }
            catch(\Exception $e) {
              Logger::warn(
                "There was an error access the path: " .
                $object->getPathname() . '\n' .
                Logger::buildExceptionMessage($e)
              );
            }

          }
        }
        return $bytestotal;
    }

    /**
     * return the file extension based on the filename provided
     * @param  string $file
     * @return string
     */
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * remove extension of file
     * @param  string  $file filename and extension
     * @return file name missing extension
     */
    public static function getFilename($file)
    {
        if (strpos($file, '.')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }
        return $file;
    }
}
