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

    public function getUsers($offset, $amount)
    {
        $this->db->query("SELECT * from user ORDER BY UserID ASC LIMIT ?, ?", $offset, $amount);
        // Null reference catch
        if(!($userData = $this->db->fetchAll()))
            return null;

        $userObjs = array();
        
        for ($i = 0; $i < sizeof($userData); $i++) {
            $userObjs[$i] = new User(
                $userData[$i]['UserID'],
                $userData[$i]['Username'],
                $userData[$i]['FirstName'],
                $userData[$i]['LastName'],
                array(),
                $this->getUsertype($userData[$i]['Usertype'])
            );
        }
        return $userObjs;
    }

    public function getUsertype($usertype) {
        // echo 'Warning: getUsertype() function from class UserService not implemented yet! Using default value "admin"';
        return "admin";
    }

    public function getUserCount() {
        $this->db->query("SELECT COUNT(UserID) as Amount FROM user ");
        return $this->db->fetchArray()['Amount'];
    }

    public function deleteUser($uid) {
        $this->db->query("DELETE FROM user WHERE UserID = ?", $uid);
    }
}

// INIT SERVICE
UserService::$instance = new UserService(Database::$instance);
