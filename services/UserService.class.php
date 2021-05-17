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
        if (!($user = $this->db->fetchArray()))
            return null;

        $userObj = new User(
            $user['UserID'],
            $user['Username'],
            $user['FirstName'],
            $user['LastName'],
            $user['FK_PictureID'],
            array(),
            new UserType($user['Usertype'])
        );

        return $userObj;
    }

    public function getUserSession($sessionid)
    {
        $this->db->query("SELECT * from user where SessionID = ?", $sessionid);
        // Null reference catch
        if (!($user = $this->db->fetchArray()))
            return null;

        $userObj = new User(
            $user['UserID'],
            $user['Username'],
            $user['FirstName'],
            $user['LastName'],
            $user['FK_PictureID'],
            array(),
            new UserType($user['Usertype'])
        );

        return $userObj;
    }

    public function getUsers($offset, $amount)
    {
        $this->db->query("SELECT * from user ORDER BY UserID ASC LIMIT ?, ?", $offset, $amount);
        // Null reference catch
        if (!($userData = $this->db->fetchAll()))
            return null;

        $userObjs = array();

        for ($i = 0; $i < sizeof($userData); $i++) {
            $userObjs[$i] = new User(
                $userData[$i]['UserID'],
                $userData[$i]['Username'],
                $userData[$i]['FirstName'],
                $userData[$i]['LastName'],
                $userData[$i]['FK_PictureID'],
                array(),
                new UserType($userData[$i]['Usertype'])
            );
        }
        return $userObjs;
    }

    public function searchUser($username)
    {
        $this->db->query("SELECT * from user WHERE Username LIKE ? ORDER BY UserID ASC", "%" . $username . "%");
        // Null reference catch
        if (!($userData = $this->db->fetchAll()))
            return null;

        $userObjs = array();

        for ($i = 0; $i < sizeof($userData); $i++) {
            $userObjs[$i] = new User(
                $userData[$i]['UserID'],
                $userData[$i]['Username'],
                $userData[$i]['FirstName'],
                $userData[$i]['LastName'],
                $userData[$i]['FK_PictureID'],
                array(),
                new UserType($userData[$i]['Usertype'])
            );
        }
        return $userObjs;
    }

    public function getUserByUsername($username)
    {
        $this->db->query("SELECT * from user WHERE Username = ?", $username);

        $userArray = $this->db->fetchArray();

        return new User(
            $userArray['UserID'],
            $userArray['Username'],
            $userArray['FirstName'],
            $userArray['LastName'],
            $userArray['FK_PictureID'],
            array(),
            new UserType($userArray['Usertype'])
        );
    }

    public function getUsertype($usertype)
    {
        // echo 'Warning: getUsertype() function from class UserService not implemented yet! Using default value "admin"';
        return "admin";
    }

    public function getUserCount()
    {
        $this->db->query("SELECT COUNT(UserID) as Amount FROM user ");
        return $this->db->fetchArray()['Amount'];
    }

    public function deleteUser($uid)
    {
        $this->db->query("DELETE FROM user WHERE UserID = ?", $uid);
    }

    // returns inserted id to auto login after registration
    public function insertUserData($userData)
    {
        $this->db->query("SELECT * FROM picture WHERE SourcePath LIKE 'resources/profilePictures/default%'");
        $defaultPictureID = $this->db->fetchArray()['PictureID'];

        $this->db->query(
            "INSERT INTO user 
        (FirstName, LastName, Username, Email, Usertype, Password, SessionID, FK_PictureID)
         VALUES (?, ?, ?, ?, 'user', ?, ?, ?)",
            $userData['FirstName'],
            $userData['LastName'],
            $userData['Username'],
            $userData['Email'],
            password_hash($userData['Password'], PASSWORD_DEFAULT),
            session_id() . time(),
            $defaultPictureID
        );

        return $this->db->lastInsertID();
    }

    public function updateSessionID($sessionID, $userID)
    {
        $this->db->query("UPDATE user SET SessionID = ? WHERE UserID = ?", $sessionID, $userID);
    }

    public function updateProfileData($userData, $uid)
    {
        $this->db->query(
            "UPDATE user SET FirstName = ?, LastName = ?, Username = ? WHERE UserID = ?",
            $userData['FirstName'],
            $userData['LastName'],
            $userData['Username'],
            $uid
        );
    }

    public function updatePassword($password, $uid)
    {
        $this->db->query("UPDATE user SET Password = ? WHERE UserID = ?", password_hash($password, PASSWORD_DEFAULT), $uid);
    }
}

// INIT SERVICE
UserService::$instance = new UserService(Database::$instance);
