<?php
class UserService
{
    /** @var UserService  */
    public static $instance;
    /** @var Database  */
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function getUser($uid)
    {
        $this->db->query("SELECT * from user where UserID = ?", $uid);
        // Null reference catch
        if(!($user = $this->db->fetchArray()))
            return null;

        $userObj = new User(
            $user['UserID'],
            $user['Username'],
            $user['FirstName'],
            $user['LastName'],
            array(),
            $this->getUsertype($user['Usertype'])
        );

        return $userObj;
    }

    public function getUsertype($usertype) {
        // echo 'Warning: getUsertype() function from class UserService not implemented yet! Using default value "admin"';
        return "admin";
    }

    public function getUserCount() {
        $this->db->query("SELECT COUNT(UserID) as Amount FROM user ");
        return $this->db->fetchArray()['Amount'];
    }
}

// INIT SERVICE
UserService::$instance = new UserService(Database::$instance);
