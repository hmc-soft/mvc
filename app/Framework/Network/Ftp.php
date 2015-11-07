<?php
namespace HMC\Network;

/*
 * FTP Class - interact with remote FTP Server
 *
 * @author David Carr - dave@simplemvcframework.HMC
 * @version 1.0
 * @date June 27, 2014
 * @date May 18 2015
 */
class Ftp
{

    /**
     * hold the FTP connction
     * @var integer
     */
    private $conn;

    /**
     * holds the path relative to the root of the server
     * @var string
     */
    private $basePath;

    /**
     * open a FTP connection
     * @param string $host the server address
     * @param string $user username
     * @param string $pass password
     * @param string $base the public folder usually public_html or httpdocs
     */
    public function __construct($host, $user, $pass, $base)
    {
        //set the basepath
        $this->basePath = $base.'/';

        //open a connection
        $this->conn = ftp_connect($host);

        //login to server
        ftp_login($this->conn, $user, $pass);
    }

    /**
     * close the connection
     */
    public function close()
    {
        ftp_close($this->conn);
    }

    /**
     * create a directory on th remote FTP server
     * @param  string $dirToCreate name of the directory to create
     */
    public function mkdir($dirToCreate)
    {
        if (!file_exists($this->basePath.$dirToCreate)) {
            ftp_mkdir($this->conn, $this->basePath.$dirToCreate);
        }
    }

    /**
     * delete directory from FTP server
     * @param  string $dir foldr to delete
     */
    public function rmdir($dir)
    {
        ftp_rmdir($this->conn, $this->basePath.$dir);
    }

    /**
     * Set folder permission
     * @param  string $folderChmod folder name
     * @param  integer $permission permission value
     * @return string              success message
     */
    public function chmod($folderChmod, $permission)
    {
        if (ftp_chmod($this->conn, $permission, $folderChmod) !== false) {
            return true;
        }
        return false;
    }

    /**
     * upload file to remove FTP server
     * @param  string $remoteFile path and filename for remote file
     * @param  string $localFile  local path to file
     * @return string             message
     */
    public function uploadFile($remoteFile, $localFile)
    {
        if (ftp_put($this->conn, $this->basePath.$remoteFile, $localFile, FTP_BINARY)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * delete remove file
     * @param  string $file path and filename
     */
    public function rm($file)
    {
        ftp_delete($this->conn, $this->basePath.$file);
    }
}
