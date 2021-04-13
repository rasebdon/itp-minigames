<?php
class Validation
{
    public static $instance;
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // generic success message, which will be filled in any empty error message slots at the end of validation
    const successMessage = "<div class='mt-1 alert alert-success' role='alert'> Success! </div>";

    // Error messages, you can append any new error message
    const error = [
        "USERNAME_EXISTS" => "<div class='mt-1 alert alert-danger' role='alert'> Username already exists! </div>",
        "EMAIL_EXISTS" => "<div class='mt-1 alert alert-danger' role='alert'> Email already exists! </div>",
        "EMPTY" => "<div class='mt-1 alert alert-danger' role='alert'> Please provide a value. </div>",
        "USERNAME_FORMAT" => "<div class='mt-1 alert alert-danger' role='alert'> Username may contain -_ a-Z and 0-9, 5-30 characters </div>",
        "PASSWORD_FORMAT" => "<div class='mt-1 alert alert-danger' role='alert'> Password must be at least 8 characters long </div>",
        "PASSWORD_MATCH" => "<div class='mt-1 alert alert-danger' role='alert'> Password does not match </div>",
        "PASSWORD_WRONG" => "<div class='mt-1 alert alert-danger' role='alert'> Wrong Password! </div>",
        "USERNAME_WRONG" => "<div class='mt-1 alert alert-danger' role='alert'> Wrong username / password! </div>"
    ];

    // bool tracking successful validation, MUST BE RESET EVERY VALIDATION CALL, using the clearErrors() function
    private $success = true;

    // array containing corresponding error messages, MUST BE RESET EVERY VALIDATION CALL, using the clearErrors() function
    private $returnErrors = array();

    // gets error messages, usually called after unsuccessful validation
    public function getReturnErrors()
    {
        return $this->returnErrors;
    }

    // clears both error message array, and success bool
    private function clearErrors()
    {
        $this->returnErrors = array();
        $this->success = true;
    }


    /**
     * Checks if value exists in database
     * @param string $table     Table name in database containing possible value
     * @param string $column    Column name in database containing possible value
     * @param mixed $value            Value to search duplicates for
     *
     * @return bool             True if value is found, false if not
     */
    private function valueExists(string $table, string $column, $value)
    {
        $this->db->query("SELECT * FROM $table WHERE $column = ?", $value);
        if ($this->db->numRows() > 0)
            return true;
        else
            return false;
    }

    /**
     * Checks if array contains empty values. 
     * If any are found: Sets error messages, as well as success member to false
     * @param array $data     array to search through
     */
    private function checkEmpty(array $data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $this->returnErrors[$key] = Validation::error['EMPTY'];
                $this->success = false;
            }
        }
    }

    /**
     * Removes form-submit from array, as it does not need to be validated.
     * This function checks for any keys containing "submit" (case insensitive)
     * @param array &$data     array to search through and modify
     */
    private function removeSubmit(array &$data)
    {
        foreach ($data as $key => $value)
            if (preg_match('/submit/i', $key))
                unset($data[$key]);
    }

    /**
     * Fills error message array with successful messages, provided there is no entry yet. 
     * @param array $data       array containing form data, its keys are used to fill corresponding indices
     */
    private function fillSuccessful(array $data)
    {
        foreach ($data as $key => $value) {
            if (!isset($this->returnErrors[$key])) {
                $this->returnErrors[$key] = Validation::successMessage;
            }
        }
    }

    /**
     * Validates registration data. Also serves as example template for future validation:
     *      1. Clear previous errors, and reset success bool
     *      2. Remove submit button
     *      3. Validate input (use checkEmpty(), valueExists(), preg_match(), etc.)
     *      4. fill error message array with success messages for each valid value
     *      5. return success bool
     * 
     * @param mixed $registerData   Form data to validate
     * 
     * @return bool                 true if everything was valid, false if one or more was invalid
     */
    public function register($registerData)
    {
        // 1. Clear previous errors, and reset success bool
        $this->clearErrors();

        // 2. Remove submit button
        $this->removeSubmit($registerData);

        // 3. Validate input (use checkEmpty(), valueExists(), preg_match(), etc.)
        $this->checkEmpty($registerData);

        if (!isset($this->returnErrors['Username']))
            if (preg_match('/^[a-z\d_-]{5,30}$/i', $registerData['Username']) === 0) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_FORMAT'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Username']))
            if ($this->valueExists("user", "Username", $registerData['Username'])) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_EXISTS'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Email']))
            if ($this->valueExists("user", "Email", $registerData['Email'])) {
                $this->returnErrors['Email'] = Validation::error['EMAIL_EXISTS'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Password']) || !isset($this->returnErrors['ConfirmPassword']))
            if (strlen($registerData['Password']) < 8) {
                $this->returnErrors['Password'] = Validation::error['PASSWORD_FORMAT'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Password']) || !isset($this->returnErrors['ConfirmPassword']))
            if ($registerData['Password'] !== $registerData['ConfirmPassword']) {
                $this->returnErrors['ConfirmPassword'] = Validation::error['PASSWORD_MATCH'];
                $this->success = false;
            }

        // 4. fill error message array with success messages for each valid value
        $this->fillSuccessful($registerData);

        // 5. return success bool
        return $this->success;
    }

    public function matchPassword($username, $password)
    {
        $this->db->query("SELECT * FROM user WHERE username = ?", $username);

        $user = $this->db->fetchArray();

        if ($user === false) {
            return false;
        }

        return password_verify($password, $user['Password']);
    }

    public function login($loginData)
    {
        $this->clearErrors();
        $this->removeSubmit($loginData);
        $this->checkEmpty($loginData);

        if (!isset($this->returnErrors['Username']))
            if (!$this->valueExists("user", "Username", $loginData['Username'])) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_WRONG'];
                $this->returnErrors['Password'] = Validation::error['USERNAME_WRONG'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Username'])) {
            if (!$this->matchPassword($loginData['Username'], $loginData['Password'])) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_WRONG'];
                $this->returnErrors['Password'] = Validation::error['USERNAME_WRONG'];
                $this->success = false;
            }
        }

        $this->fillSuccessful($loginData);
        return $this->success;
    }

    public function editProfile($profileData, $uid)
    {
        $this->clearErrors();
        $this->removeSubmit($profileData);
        $this->checkEmpty($profileData);

        if (!isset($this->returnErrors['Username']))
            if (preg_match('/^[a-z\d_-]{5,30}$/i', $profileData['Username']) === 0) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_FORMAT'];
                $this->success = false;
            }

        if (!isset($this->returnErrors['Username'])) {

            $this->db->query("SELECT * FROM user WHERE Username = ? AND UserID != ? ", $profileData['Username'], $uid);

            if ($this->db->numRows() > 0) {
                $this->returnErrors['Username'] = Validation::error['USERNAME_EXISTS'];
                $this->success = false;
            }
        }
        $this->fillSuccessful($profileData);
        return $this->success;
    }
}

Validation::$instance = new Validation(Database::$instance);
