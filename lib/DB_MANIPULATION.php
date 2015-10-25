<?php
abstract class DB_MANIPULATION
{
    protected $conn = null;
    protected $error = null;
   
    public function __construct()
    {
        $this->conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
    }

    public function error()
    {
        if (!empty($this->error))
        {
            return $this->error;
        }

        return false;
    }

}
