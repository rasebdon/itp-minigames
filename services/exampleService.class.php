<?php
class ExampleService
{
    public static $instance;
    protected $db;
    function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function getUser($uid) //use another function and need user id to declare which user used function
    {
        $this->db->query("SELECT * from user where uid = ?", $uid);
        $user = $this->db->fetchArray();
        return new User(
            $user['uid'],
            $user['FK_iid'],
            $user['session_id'],
            $user['usertype'],
            $user['is_active'],
            $user['salutation'],
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['username'],
            $user['password']
        );
    }

    public function getActiveUser($uid) //use another function and need user id to declare which user used function
    {
        $this->db->query("SELECT * from user where uid = ? AND is_active = 1", $uid);
        $user = $this->db->fetchArray();
        return new User(
            $user['uid'],
            $user['FK_iid'],
            $user['session_id'],
            $user['usertype'],
            $user['is_active'],
            $user['salutation'],
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['username'],
            $user['password']
        );
    }

    public function getAllUser()
    {
        $this->db->query("SELECT * FROM user");
        $users_array = $this->db->fetchAll();
        $users = array();
        foreach ($users_array as $user) {
            $users[] = new User(
                $user['uid'],
                $user['FK_iid'],
                $user['session_id'],
                $user['usertype'],
                $user['is_active'],
                $user['salutation'],
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['username'],
                $user['password']
            );
        }
        return $users;
    }

    public function removeInactive($users)
    {
        foreach ($users as $key => $user) {
            if ($user->getIs_active() == 0)
                unset($users[$key]);
        }
        return $users;
    }

    public function toggleIsActive($uid)
    {
        $this->db->query("SELECT * FROM user WHERE uid = ?", $uid);
        $user = $this->db->fetchArray();
        $this->db->query(
            "UPDATE user SET is_active = ? WHERE uid = ?",
            $user['is_active'] ? 0 : 1,
            $uid
        );
    }

    public function loginUser($login_username)
    {
        $this->db->query("SELECT * FROM user WHERE username = ? AND is_active = 1", $login_username); //* gets whole tables
        $user = $this->db->fetchArray();
        return new User(
            $user['uid'],
            $user['FK_iid'],
            $user['session_id'],
            $user['usertype'],
            $user['is_active'],
            $user['salutation'],
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['username'],
            $user['password']
        );
    }

    public function updateSessionID($session_id, $uid)
    {

        $this->db->query("UPDATE USER SET session_id = ? WHERE uid = ?", $session_id, $uid);
    }

    public function bakeCookie($session_id)
    {
        $this->db->query("SELECT * FROM user WHERE session_id = ?", $session_id);
        $user = $this->db->fetchArray();
        if (empty($user)) {
            return false;
        } else {
            return new User(
                $user['uid'],
                $user['FK_iid'],
                $user['session_id'],
                $user['usertype'],
                $user['is_active'],
                $user['salutation'],
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['username'],
                $user['password']
            );
        }
    }

    public function registerUser($userdata)
    {
        //? because of prepared statement (string needs ? to fill in correct data)
        $this->db->query("SELECT * FROM image_user WHERE source_path = 'pictures/profile/default.jpg'");
        $default_image = $this->db->fetchArray();
        $this->db->query(
            "INSERT INTO user (FK_iid, session_id, usertype, is_active, salutation, first_name, last_name, email, username, password) VALUES (?, ?, 'user', 1, ?, ?, ?, ?, ?, ?)",
            $default_image['iid'],
            session_id() . time(),
            $userdata['salutation'],
            $userdata['first_name'],
            $userdata['last_name'],
            $userdata['email'],
            $userdata['username'],
            password_hash($userdata['password'], PASSWORD_DEFAULT)
        );
        return $this->db->lastInsertID(); //gets last inserted ID
    }

    public function updateSettings($userdata, $uid)
    {
        $this->db->query(
            "UPDATE user SET salutation = ?, first_name = ?, last_name = ?, username = ? 
            WHERE uid = ?",
            $userdata['salutation'],
            $userdata['first_name'],
            $userdata['last_name'],
            $userdata['username'],
            $uid
        );
    }

    public function updatePassword($password, $uid)
    {
        $this->db->query(
            "UPDATE user SET password = ? 
            WHERE uid = ?",
            password_hash($password, PASSWORD_DEFAULT),
            $uid
        );
    }

    public function findUsers($search)
    {
        $this->db->query("SELECT * FROM user WHERE username LIKE ?", "%" . $search . "%");
        $user_array = array();
        $user_array = $this->db->fetchAll();
        $users = array();
        foreach ($user_array as $key => $user) {
            $users[] = new User(
                $user['uid'],
                $user['FK_iid'],
                $user['session_id'],
                $user['usertype'],
                $user['is_active'],
                $user['salutation'],
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['username'],
                $user['password']
            );
        }
        return $users;
    }

    public function getUserInGroup($gid)
    {
        $this->db->query("SELECT * FROM user WHERE uid IN (SELECT FK_uid FROM user_in_group WHERE FK_gid = ?)", $gid);
        $users_array = array();
        $users_array = $this->db->fetchAll();
        $users = array();
        foreach ($users_array as $key => $user) {
            $users[] = new User(
                $user['uid'],
                $user['FK_iid'],
                $user['session_id'],
                $user['usertype'],
                $user['is_active'],
                $user['salutation'],
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['username'],
                $user['password']
            );
        }
        return $users;
    }

    public function resetUserImage($iid, $uid)
    {
        $this->db->query("UPDATE user SET FK_iid = ? WHERE uid = ?", $iid, $uid);
    }
}

ExampleService::$instance = new ExampleService(Database::$instance);