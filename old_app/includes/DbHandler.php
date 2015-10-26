<?php

/**
 *
 * Class to handle all db operations
 *
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /***********************/
    /*                     */
    /*   LOGIN AND USERS   */
    /*                     */
    /***********************/

    /**
     * Creating new user
     * @param Integer $account_type type of the account
     * @param String $login nickname of the user
     * @param String $name User name
     * @param String $surname User surname
     * @param String $email User login email
     */
    public function createUser($account_type, $login, $email, $name, $surname) {
        $stmt = $this->conn->prepare("INSERT INTO olm_admin_users (account_type, login, email, name, surname, language_code) values(?, ?, ?, ?, ?, 'sk')");
        $stmt->bind_param("issss", $account_type, $login, $email, $name, $surname);

        $result = $stmt->execute();

        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return $this->conn->insert_id;
        } else {
            // Failed to create user
            return USER_CREATE_FAILED;
        }
    }

    public function loginLDAP($login, $password, $account_type) {
        $ldapconfig['host'] = 'ldap.stuba.sk';
        $ldapconfig['port'] = '389';
        $ldaprdn = "uid=$login, ou=People, DC=stuba, DC=sk";

        if (!($ldapconn = ldap_connect($ldapconfig['host'], $ldapconfig['port']))) {
            return FALSE; // LDAP error
        }

        $security = new Security();
        if ($bind = ldap_bind($ldapconn, $ldaprdn, $security->decrypt($password))) {
            if ($this->getUserByLogin($login) == NULL) {
                $this->createUser($account_type, $login, $login . "@stuba.sk", $login, "");
            }
            return TRUE;
        } else {
            return FALSE; // bad credentials
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return id or null if not exists
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * from olm_admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Checking user login
     * @param String $login User login name
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($login, $password) {
        // fetching user by login
        $stmt = $this->conn->prepare("SELECT pass FROM olm_admin_users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->fetch();

        if ($password_hash != null) {

            // Found user with the login
            // Now verify the password
            if ($password_hash == $password) {
                $stmt->close();
                return TRUE; // user password is correct
            } else {
                $stmt->close();                
                return FALSE; // user password is incorrect
            }
        } else {
            $stmt->close();
            return FALSE; // user not existed with the login
        }
    }

    /**
     * Fetching user by login
     * @param String $login User login
     */
    public function getUserByLogin($login) {
        $stmt = $this->conn->prepare("SELECT * FROM olm_admin_users WHERE login = ?");
        $stmt->bind_param("s", $login);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            return $user;
        } else {
            return NULL;
        }
    }

    /** 
     * Getting all users
     */
    public function getAllUsers() {        
        $stmt = $this->conn->prepare("SELECT * FROM olm_admin_users");
        $stmt->execute();
        $users = $stmt->get_result();
        $stmt->close();
        return $users;
    }

    /**
     * Updating user
     * @param Integer &user_id
     * @param String $login
     * @param String $name
     * @param String $surname
     * @param String $email
     * @param Integer $role
     * @param String $language
     * @param String $password
     */
    public function updateUser($user_id, $login, $name, $surname, $email, $role, $language, $password) {
        if ($password == null || $password == '') {
            $stmt = $this->conn->prepare("UPDATE olm_admin_users set login = ?, name = ?, surname = ?, email = ?, role = ?, language_code = ? WHERE id = ?");
            $stmt->bind_param("ssssisi", $login, $name, $surname, $email, $role, $language, $user_id);
        } else {
            $stmt = $this->conn->prepare("UPDATE olm_admin_users set login = ?, name = ?, surname = ?, email = ?, role = ?, language_code = ?, pass = ? WHERE id = ?");
            $stmt->bind_param("ssssissi", $login, $name, $surname, $email, $role, $language, $password, $user_id);
        }
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    /**
     * Fetching user data by user id
     * @param Integer $user_id User id
     */
    public function getUserById($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM olm_admin_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Deleting a user
     * @param $user_id id of the user to delete
     */
    public function deleteUser($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM olm_admin_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /********************/
    /*                  */
    /*    EQUIPMENTS    */
    /*                  */
    /********************/

    /** 
     * Getting all equipments
     */
    public function getAllEquipments() {        
        $stmt = $this->conn->prepare("SELECT * FROM olm_equipments ORDER BY id DESC");
        $stmt->execute();
        $equipments = $stmt->get_result();
        $stmt->close();
        return $equipments;
    }

    /**
     * Creating new equipment
     * @param String $equipment_name name of the equipment
     * @param String $ip equipments ip address
     * @param String $color color assigned to equipment
     */
    public function createEquipment($equipment_name, $ip, $color) {
        $stmt = $this->conn->prepare("INSERT INTO olm_equipments(equipment_name, ip, color) VALUES (?,?,?)");
        $stmt->bind_param("sss", $equipment_name, $ip, $color);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // Equipment successfully inserted
            return $this->conn->insert_id;
        } else {
            // Equipment not inserted
            return NULL;
        }
    }

    /**
     * Updating equipment
     * @param Integer $equipment_id
     * @param String $equipment_name
     * @param String $ip
     * @param String $color
     */
    public function updateEquipment($equipment_id, $equipment_name, $ip, $color) {
        $stmt = $this->conn->prepare("UPDATE olm_equipments set equipment_name = ?, ip = ?, color = ? WHERE id = ?");
        $stmt->bind_param("sssi", $equipment_name, $ip, $color, $equipment_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Deleting equipment
     * @param $equipment_id id of the equipment to delete
     */
    public function deleteEquipment($equipment_id) {
        $stmt = $this->conn->prepare("DELETE FROM olm_equipments WHERE id = ?");
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /*******************/
    /*                 */
    /*   CONTROLLERS   */
    /*                 */
    /*******************/

    /** 
     * Getting all controllers
     */
    public function getAllControllers($user_id) {        
        $stmt = $this->conn->prepare("SELECT c.id, c.user_id, c.equipment_id, c.name, c.permissions, c.body, c.date, u.login, e.equipment_name FROM olm_controllers c 
            JOIN olm_admin_users u ON c.user_id = u.id JOIN olm_equipments e ON c.equipment_id = e.id WHERE c.permissions = 1 OR (c.permissions = 2 AND c.user_id = ?) 
            ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $controllers = $stmt->get_result();
        $stmt->close();
        return $controllers;
    }

    /**
     * Creating new controller
     * @param Integer $user_id id of the user that is creating the controller
     * @param Integer $equipment_id equipments id assigned to controller
     * @param String $name name of the controller
     * @param Integer $permissions edit permissions
     * @param String $body body of the controller
     * @param String $date date of the created controller
     */
    public function createController($user_id, $equipment_id, $name, $permissions, $body, $date) {
        $stmt = $this->conn->prepare("INSERT INTO olm_controllers(user_id, equipment_id, name, permissions, body, date) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iisiss", $user_id, $equipment_id, $name, $permissions, $body, $date);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // Controller successfully inserted
            return $this->conn->insert_id;
        } else {
            // Controller not inserted
            return NULL;
        }
    }

    /**
     * Updating controller
     * @param Integer $controller_id id of the controller that is being updated
     * @param Integer $equipment_id equipments id assigned to controller
     * @param String $name name of the controller
     * @param Integer $permissions edit permissions
     * @param String $body body of the controller
     */
    public function updateController($controller_id, $equipment_id, $name, $permissions, $body) {
        $stmt = $this->conn->prepare("UPDATE olm_controllers set equipment_id = ?, name = ?, permissions = ?, body = ? WHERE id = ?");
        $stmt->bind_param("isisi", $equipment_id, $name, $permissions, $body, $controller_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
    
    /*******************/
    /*                 */
    /*     REPORTS     */
    /*                 */
    /*******************/

    /**
     * Creating new report
     */
    public function createReport($user_id, $equipment_id, $regulator, $regulator_settings, $ip, $report_date, $experiment_settings) {
        $stmt = $this->conn->prepare("INSERT INTO olm_reports(user_id, equipment_id, regulator, 
            regulator_settings, ip, report_date, experiment_settings) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("iisssss", $user_id, $equipment_id, $regulator, $regulator_settings, $ip, $report_date, $experiment_settings);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // Report successfully inserted
            return $this->conn->insert_id;
        } else {
            // Report not inserted
            return NULL;
        }
    }

    /**
     * Updating report
     */
    public function updateReport($report_id, $output, $console, $report_simulation_time, $running) {
        $stmt = $this->conn->prepare("UPDATE olm_reports set output = ?, console = ?, report_simulation_time = ?, exp_running = ? WHERE id = ?");
        $stmt->bind_param("sssii", $output, $console, $report_simulation_time, $running, $report_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Updating report to stop
     */
    public function stopReport($report_id) {
        $stmt = $this->conn->prepare("UPDATE olm_reports set exp_running = 0 WHERE id = ?");
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /** 
     * Getting reports in range by user id
     */
    public function getReportsInRangeByUser($user_id, $from) {
        $stmt = $this->conn->prepare("SELECT r.id, r.user_id, r.equipment_id, r.output, r.console, r.regulator, r.regulator_settings, r.ip, r.report_date,
            r.report_simulation_time, r.experiment_settings, r.exp_running, r.notes, e.equipment_name FROM olm_reports r JOIN olm_equipments e ON r.equipment_id = e.id
            WHERE r.user_id = ? and report_date < ? ORDER BY report_date DESC LIMIT 5");

        $stmt->bind_param("is", $user_id, $from);

        if ($stmt->execute()) {
            $reports = $stmt->get_result();
            $stmt->close();
            return $reports;
        } else {
            return NULL;
        }
    }

    /** 
     * Getting last report by user id
     */
    public function getLastReportByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT r.id, r.user_id, r.equipment_id, r.output, r.console, r.regulator, r.regulator_settings, r.ip, r.report_date,
            r.report_simulation_time, r.experiment_settings, r.exp_running, r.notes, e.equipment_name FROM olm_reports r JOIN olm_equipments e ON r.equipment_id = e.id
            WHERE r.user_id = ? ORDER BY id DESC LIMIT 1");

        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $report = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $report;
        } else {
            return NULL;
        }
    }

    /******************/
    /*                */
    /*  RESERVATIONS  */
    /*                */
    /******************/

    /** 
     * Getting all reservations
     */
    public function getAllReservations() {
        $stmt = $this->conn->prepare("SELECT r.id, r.user_id, u.login, r.equipment, e.equipment_name, e.color, r.start, r.end FROM olm_reservations r 
            JOIN olm_equipments e ON r.equipment = e.id JOIN olm_admin_users u ON r.user_id = u.id ORDER BY r.start ASC");
        $stmt->execute();
        $reservations = $stmt->get_result();
        $stmt->close();
        return $reservations;
    }

    /** 
     * Getting reservations by date range
     */
    public function getReservationsByDateRange($start, $end) {
        $stmt = $this->conn->prepare("SELECT r.id, r.user_id, u.login, r.equipment, e.equipment_name, e.color, r.start, r.end FROM olm_reservations r 
            JOIN olm_equipments e ON r.equipment = e.id JOIN olm_admin_users u ON r.user_id = u.id WHERE r.start > ? AND r.end < ? ORDER BY r.start ASC");

        $stmt->bind_param("ss", $start, $end);

        $stmt->execute();
        $reservations = $stmt->get_result();
        $stmt->close();
        return $reservations;
    }

    /**
     * Creating new reservation
     * @param Integer $equipment_id id of the equipment that is reserved
     * @param Integer $user_id id of the user who is reserving the equipment
     * @param String $title title of the reservation
     * @param String $start start of the reservation
     * @param String $end end of the reservation
     */
    public function createReservation($equipment_id, $user_id, $title, $start, $end) {
        $stmt = $this->conn->prepare("INSERT INTO olm_reservations(equipment, user_id, title, start, end) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iisss", $equipment_id, $user_id, $title, $start, $end);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // Reservation successfully inserted
            return $this->conn->insert_id;
        } else {
            // Reservation not inserted
            return NULL;
        }
    }

    /**
     * Updating reservation
     * @param Integer $reservation_id
     * @param Integer $equipment_id
     * @param String $start
     * @param String $end
     */
    public function updateReservation($reservation_id, $equipment_id, $title, $start, $end) {
        $stmt = $this->conn->prepare("UPDATE olm_reservations SET equipment = ?, title = ?, start = ?, end = ? WHERE id = ?");
        $stmt->bind_param("isssi", $equipment_id, $title, $start, $end, $reservation_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
    * Checks the reservation availability - if its reserver return true, else false
    */
    public function checkReservationAvailability($reservation_id, $start, $end, $equipment_id) {
        $stmt = $this->conn->prepare("SELECT id FROM olm_reservations 
            WHERE (? > start AND ? < end OR ? > start AND ? < end OR ((start = ? OR ? < start) AND (end = ? OR ? > end))) AND (equipment = ? AND id != ?)");
        $stmt->bind_param("ssssssssii", $start, $start, $end, $end, $start, $start, $end, $end, $equipment_id, $reservation_id);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->num_rows;
        $stmt->close();
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deleting equipment
     * @param $reservation_id id of the reservation to delete
     */
    public function deleteReservation($reservation_id) {
        $stmt = $this->conn->prepare("DELETE FROM olm_reservations WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /** 
     * Getting all future active equipment reservations
     */
    public function getAllEquipmentReservations($user_id) {
        $stmt = $this->conn->prepare("SELECT r.id, eq.id as equipment_id, eq.equipment_name, eq.color, r.start, r.end, CASE WHEN r.start < now() AND r.end > now()
                                        THEN 'true' ELSE 'false' END as startable FROM
                                        (SELECT e.*,
                                            (SELECT r.id
                                                FROM olm_reservations r
                                                WHERE r.equipment = e.id AND (start > now() OR (start < now() AND end > now())) AND user_id = ?
                                                ORDER BY r.start ASC
                                                LIMIT 1
                                            ) AS next_resid FROM olm_equipments e
                                        ) eq LEFT JOIN olm_reservations r ON eq.next_resid = r.id");
        $stmt->bind_param("i", $user_id);

        $stmt->execute();
        $reservations = $stmt->get_result();
        $stmt->close();
        return $reservations;
    }

    /*************************/
    /*                       */
    /*  EQUIPMENT VARIABLES  */
    /*                       */
    /*************************/

    /** 
     * Getting all equipment variables
     */
    public function getAllEquipmentVariables($equipment_id, $type) {
        $stmt = $this->conn->prepare("SELECT * FROM olm_equipment_variables WHERE equipment_id = ? AND type = ?");
        $stmt->bind_param("is", $equipment_id, $type);
        $stmt->execute();
        $equipment_variables = $stmt->get_result();
        $stmt->close();
        return $equipment_variables;
    }

    public function createEquipmentVariable($equipment_id, $sk, $en, $db_variable, $exp_variable, $type) {
        $stmt = $this->conn->prepare("INSERT INTO olm_equipment_variables(equipment_id, sk, en, db_variable, exp_variable, type) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isssss", $equipment_id, $sk, $en, $db_variable, $exp_variable, $type);
        $result = $stmt->execute();
        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // Equipment variable successfully inserted
            return $this->conn->insert_id;
        } else {
            // Equipment variable not inserted
            return NULL;
        }
    }

    /**
     * Deleting equipment variable
     * @param $equipment_id id of the equipment to delete variable
     */
    public function deleteEquipmentVariables($equipment_id) {
        $stmt = $this->conn->prepare("DELETE FROM olm_equipment_variables WHERE equipment_id = ?");
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
}

/*
Copyright (C) 2011 by Steven Holder

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
class Security {

    private $KEY = "FEISecurityKEY15";

    public static function encrypt($input) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB); 
        $input = Security::pkcs5_pad($input, $size); 
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, ''); 
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
        mcrypt_generic_init($td, "FEISecurityKEY15", $iv); 
        $data = mcrypt_generic($td, $input); 
        mcrypt_generic_deinit($td); 
        mcrypt_module_close($td); 
        $data = base64_encode($data); 
        return $data; 
    } 

    private static function pkcs5_pad ($text, $blocksize) { 
        $pad = $blocksize - (strlen($text) % $blocksize); 
        return $text . str_repeat(chr($pad), $pad); 
    } 

    public static function decrypt($sStr) {
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            "FEISecurityKEY15", 
            base64_decode($sStr), 
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted); 
        $padding = ord($decrypted[$dec_s-1]); 
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }   
}

?>
