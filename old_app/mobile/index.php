<?php

require_once '../includes/DbHandler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/***********************/
/*                     */
/*   LOGIN AND USERS   */
/*                     */
/***********************/

// Accounts
define('LOCAL_ACCOUNT', '1');
define('GOOGLE_ACCOUNT', '2');
define('LDAP_ACCOUNT', '3');

/**
 * User Registration - should have been normal registration and then changed just to google :(
 * url - /register
 * method - POST
 * params - name, surname, login, email, account_type
 */
$app->post('/register', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('name', 'surname', 'login', 'email', 'account_type'));
            
            $response = array();

            // reading post params
            $account_type = $app->request->post('account_type');
            $login = $app->request->post('login');
            $email = $app->request->post('email');
            $name = $app->request->post('name');
            $surname = $app->request->post('surname');
            
            $db = new DbHandler();

            if ($account_type == GOOGLE_ACCOUNT) { // GOOGLE ACCOUNT
                $res = $db->getUserByLogin($login);
                if ($res != NULL) {
                    $response["error"] = false;
                    $response["user"] = $res;
                    $response["message"] = "You are allready registered";

                    // echo json response
                    echoRespnse(201, $response);

                    return;
                }
            }
            
            $res = $db->createUser($account_type, $login, $email, $name, $surname);

            if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
            } else {
                $res = $db->getUserByLogin($login);

                if ($res != NULL) {
                    $response["error"] = false;
                    $response["user"] = $res;
                    $response["message"] = "You are successfully registered";
                } else {
                    $response["error"] = true;
                    $response["message"] = "Oops! An error occurred while registereing";
                }
            }
            
            // echo json response
            echoRespnse(201, $response);
        });

/**
 * User Login
 * url - /login
 * method - POST
 * params - login, password
 */
$app->post('/login', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('login', 'password'));

            // reading post params
            $login = $app->request()->post('login');
            $password = $app->request()->post('password');
            $response = array();

            $db = new DbHandler();
            // check for correct login and password
            if ($db->checkLogin($login, $password)) {
                // get the user by login
                $user = $db->getUserByLogin($login);

                if ($user != NULL) {
                    $response["error"] = false;
                    $response["id"] = $user["id"];
                } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }

            echoRespnse(200, $response);
        });

/**
 * User Login LDAP
 * url - /login/ldap
 * method - POST
 * params - login, password
 */
$app->post('/login/ldap', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('login', 'password'));

            // reading post params
            $login = $app->request()->post('login');
            $password = $app->request()->post('password');
            $response = array();

            $db = new DbHandler();
            // check for correct login and password
            if ($db->loginLDAP($login, $password, LDAP_ACCOUNT)) {
                // get the user by login
                $user = $db->getUserByLogin($login);

                if ($user != NULL) {
                    $response["error"] = false;
                    $response["id"] = $user["id"];
                } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }

            echoRespnse(200, $response);
        });

/**
 * Listing all users
 * method GET
 * url /users          
 */
$app->get('/users', function() {
            $response = array();
            $db = new DbHandler();

            // fetching all users
            $result = $db->getAllUsers();
            
            $response["error"] = false;
            $response["users"] = array();
            
            // looping through result and preparing tasks array
            while ($user = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $user["id"];
                $tmp["login"] = $user["login"];
                $tmp["name"] = $user["name"];
                $tmp["surname"] = $user["surname"];
                $tmp["email"] = $user["email"];
                $tmp["language_code"] = $user["language_code"];
                $tmp["role"] = $user["role"];
                $tmp["account_type"] = $user["account_type"];
                array_push($response["users"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Updating existing user
 * method POST
 * params id, login, name, surname, email, role, language, (new password, retype password)
 * url - /users
 */
$app->post('/users', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('id', 'login', 'email'));

            $user_id = $app->request->post('id');
            $login = $app->request->post('login');
            $name = $app->request->post('name');
            $surname = $app->request->post('surname');
            $email = $app->request->post('email');
            $role = $app->request->post('role');
            $language = $app->request->post('language');
            $password = $app->request->post('password');
            
            $db = new DbHandler();
            $response = array();
            
            // updating user
            $result = $db->updateUser($user_id, $login, $name, $surname, $email, $role, $language, $password);
            if ($result) {
                // user updated successfully
                $response["error"] = false;
                $response["message"] = "User updated successfully";
            } else {
                // user failed to update
                $response["error"] = true;
                $response["message"] = "User failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });

/**
 * Getting user info by user id
 * method GET
 * url /users/id
 */
$app->get('/users/:id', function($user_id) {
            $response = array();
            $db = new DbHandler();
            
            // fetching user by id
            $result = $db->getUserById($user_id);

            if ($result != NULL) {
                $response["error"] = false;
                $response["id"] = $result["id"];
                $response["login"] = $result["login"];
                $response["name"] = $result["name"];
                $response["surname"] = $result["surname"];
                $response["email"] = $result["email"];
                $response["language_code"] = $result["language_code"];
                $response["role"] = $result["role"];
                $response["account_type"] = $result["account_type"];
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "The requested resource doesn't exist";
                echoRespnse(404, $response);
            }
        });

/**
 * Deleting user by user id
 * method DELETE
 * url /users
 */
$app->delete('/users/:id', function($user_id) use($app) {
            $response = array();
            $db = new DbHandler();

            $result = $db->deleteUser($user_id);
            if ($result) {
                // user deleted successfully
                $response["error"] = false;
                $response["message"] = "User deleted succesfully";
            } else {
                // user failed to delete
                $response["error"] = true;
                $response["message"] = "User failed to delete. Please try again!";
            }
            echoRespnse(200, $response);
        });

/********************/
/*                  */
/*    EQUIPMENTS    */
/*                  */
/********************/

/**
 * Listing all equipments
 * method GET
 * url /equipments          
 */
$app->get('/equipments', function() {
            $response = array();
            $db = new DbHandler();

            // fetching all equipments
            $result = $db->getAllEquipments();
            
            $response["error"] = false;
            $response["equipments"] = array();
            
            // looping through result and preparing tasks array
            while ($equipment = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $equipment["id"];
                $tmp["equipment_name"] = $equipment["equipment_name"];
                $tmp["ip"] = $equipment["ip"];
                $tmp["color"] = $equipment["color"];

                // See reports too
                $tmp["inputs"] = array();
                $tmp["outputs"] = array();

                $resultVariables = $db->getAllEquipmentVariables($equipment["id"], "input");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($tmp["inputs"], $inputTmp);
                }

                $resultVariables = $db->getAllEquipmentVariables($equipment["id"], "output");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($tmp["outputs"], $inputTmp);
                }

                array_push($response["equipments"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Creating new equipment in db
 * method POST
 * params - equipment_name, ip, color
 * url - /equipments
 */
$app->post('/equipments', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('equipment_name', 'ip', 'color', 'inputs', 'outputs'));

            $response = array();

            // reading post params
            $equipment_name = $app->request->post('equipment_name');
            $ip = $app->request->post('ip');
            $color = $app->request->post('color');
            $inputs = $app->request->post('inputs');
            $outputs = $app->request->post('outputs');

            // creating new equipment
            $db = new DbHandler();
            $equipment_id = $db->createEquipment($equipment_name, $ip, $color);

            if ($equipment_id != NULL) {
                decodeAndCreateVariablesIO($db, $equipment_id, $inputs);
                decodeAndCreateVariablesIO($db, $equipment_id, $outputs);

                // equipment created successfully
                $response["error"] = false;
                $response["id"] = $equipment_id;
                $response["message"] = "Equipment created successfully";
                echoRespnse(201, $response);
            } else {
                // equipment failed to create
                $response["error"] = true;
                $response["message"] = "Failed to create equipment. Please try again";
                echoRespnse(200, $response);
            }
        });

/**
 * Updating existing equipment
 * method POST
 * params id, equipment_name, ip, color
 * url - /equipments
 */
$app->post('/equipments/:id', function($equipment_id) use($app) {
            // check for required params
            verifyRequiredParams(array('equipment_name', 'ip', 'color', 'inputs', 'outputs'));

            $equipment_name = $app->request->post('equipment_name');
            $ip = $app->request->post('ip');
            $color = $app->request->post('color');
            $inputs = $app->request->post('inputs');
            $outputs = $app->request->post('outputs');
            
            $db = new DbHandler();
            $response = array();
            
            // updating equipment
            $db->updateEquipment($equipment_id, $equipment_name, $ip, $color);
            $db->deleteEquipmentVariables($equipment_id);

            decodeAndCreateVariablesIO($db, $equipment_id, $inputs);
            decodeAndCreateVariablesIO($db, $equipment_id, $outputs);

            // equipment updated successfully
            $response["error"] = false;
            $response["message"] = "Equipment updated successfully";
            echoRespnse(200, $response);
        });

/**
 * Deleting equipment
 * method DELETE
 * url /equipments
 */
$app->delete('/equipments/:id', function($equipment_id) use($app) {
            $response = array();
            $db = new DbHandler();

            $result = $db->deleteEquipment($equipment_id);
            if ($result) {
                // equipment deleted successfully
                $response["error"] = false;
                $response["message"] = "Equipment deleted succesfully";
            } else {
                // equipment failed to delete
                $response["error"] = true;
                $response["message"] = "Equipment failed to delete. Please try again!";
            }
            echoRespnse(200, $response);
        });

/*******************/
/*                 */
/*   CONTROLLERS   */
/*                 */
/*******************/

/**
 * Listing all controllers
 * method GET
 * url /controllers          
 */
$app->get('/controllers/:id', function($user_id) use($app) {
            $response = array();
            $db = new DbHandler();

            // fetching all controllers
            $result = $db->getAllControllers($user_id);
            
            $response["error"] = false;
            $response["controllers"] = array();
            
            // looping through result and preparing tasks array
            while ($controller = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $controller["id"];
                $tmp["user_id"] = $controller["user_id"];
                $tmp["equipment_id"] = $controller["equipment_id"];
                $tmp["name"] = $controller["name"];
                $tmp["permissions"] = $controller["permissions"];
                $tmp["body"] = $controller["body"];
                $tmp["date"] = $controller["date"];
                $tmp["login"] = $controller["login"];
                $tmp["equipment_name"] = $controller["equipment_name"];
                array_push($response["controllers"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Creating new controller in db
 * method POST
 * params - user_id, equipment_id, name, permissions, body, date
 * url - /controllers
 */
$app->post('/controllers', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('user_id', 'equipment_id', 'name', 'permissions', 'body', 'date'));

            $response = array();

            // reading post params
            $user_id = $app->request->post('user_id');
            $equipment_id = $app->request->post('equipment_id');
            $name = $app->request->post('name');
            $permissions = $app->request->post('permissions');
            $body = $app->request->post('body');
            $date = $app->request->post('date');
            
            // creating new controller
            $db = new DbHandler();
            $res = $db->createController($user_id, $equipment_id, $name, $permissions, $body, $date);

            if ($res != NULL) {
                $response["error"] = false;
                $response["id"] = $res;
                $response["message"] = "Controller created successfully";
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to create controller. Please try again";
                echoRespnse(200, $response);
            }
        });

/**
 * Updating existing controller
 * method POST
 * params controller_id, equipment_id, name, permissions, body
 * url - /controllers
 */
$app->post('/controllers/:id', function($controller_id) use($app) {
            // check for required params
            verifyRequiredParams(array('equipment_id', 'name', 'body'));

            $equipment_id = $app->request->post('equipment_id');
            $name = $app->request->post('name');
            $permissions = $app->request->post('permissions');
            $body = $app->request->post('body');
            
            $db = new DbHandler();
            $response = array();
            
            // updating controller
            $result = $db->updateController($controller_id, $equipment_id, $name, $permissions, $body);
            if ($result) {
                // controller updated successfully
                $response["error"] = false;
                $response["message"] = "Controller updated successfully";
            } else {
                // controller failed to update
                $response["error"] = true;
                $response["message"] = "Controller failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });

/*******************/
/*                 */
/*     REPORTS     */
/*                 */
/*******************/

/**
 * Creating new report in db
 * method POST
 * url - /reports
 */
$app->post('/reports', function() use ($app) {
            $response = array();

            // reading post params
            $user_id = $app->request->post('user_id');
            $equipment_id = $app->request->post('equipment_id');
            $regulator = $app->request->post('regulator');
            $regulator_settings = $app->request->post('regulator_settings');
            $ip = $app->request->post('ip');
            $report_date = $app->request->post('report_date');
            $experiment_settings = $app->request->post('experiment_settings');

            // creating new report
            $db = new DbHandler();
            $res = $db->createReport($user_id, $equipment_id, $regulator, $regulator_settings, $ip, $report_date, $experiment_settings);

            if ($res != NULL) {
                $response["error"] = false;
                $response["id"] = $res;
                $response["message"] = "Report created successfully";
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to create report. Please try again";
                echoRespnse(200, $response);
            }
        });

/**
 * Updating existing report
 * method POST
 * url - /reports
 */
$app->post('/reports/:id', function($report_id) use ($app) {
            $output = $app->request->post('output');
            $console = $app->request->post('console');
            $report_simulation_time = $app->request->post('report_simulation_time');
            $running = $app->request->post('exp_running');
            
            $db = new DbHandler();
            $response = array();
            
            // updating report
            $result = $db->updateReport($report_id, $output, $console, $report_simulation_time, $running);
            if ($result) {
                // report updated successfully
                $response["error"] = false;
                $response["message"] = "Report updated successfully";
            } else {
                // report failed to update
                $response["error"] = true;
                $response["message"] = "Report failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });

/**
 * Updating existing report status from running to stopped
 * method POST
 * url - /reports/stop/id
 */
$app->post('/reports/stop/:id', function($report_id) use ($app) {
            $db = new DbHandler();
            $response = array();
            
            // stopping report
            $result = $db->stopReport($report_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Report updated successfully";
            } else {
                $response["error"] = true;
                $response["message"] = "Report failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });

/**
 * Getting reports by user id
 * method POST
 * url /reports/range/id
 */
$app->post('/reports/range/:id', function($user_id) use ($app) {
            $response = array();
            $db = new DbHandler();

            $from = $app->request->post('from');
            
            // fetching reports in range
            $result = $db->getReportsInRangeByUser($user_id, $from);
            
            $response["error"] = false;
            $response["reports"] = array();

            // looping through result and preparing tasks array
            while ($report = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $report["id"];
                $tmp["user_id"] = $report["user_id"];
                $tmp["equipment_id"] = $report["equipment_id"];
                $tmp["output"] = $report["output"];
                $tmp["console"] = $report["console"];
                $tmp["regulator"] = $report["regulator"];
                $tmp["regulator_settings"] = $report["regulator_settings"];
                $tmp["ip"] = $report["ip"];
                $tmp["report_date"] = $report["report_date"];
                $tmp["report_simulation_time"] = $report["report_simulation_time"];
                $tmp["experiment_settings"] = $report["experiment_settings"];
                $tmp["exp_running"] = $report["exp_running"];
                $tmp["notes"] = $report["notes"];
                $tmp["equipment_name"] = $report["equipment_name"];

                // See get equipments too
                $tmp["inputs"] = array();
                $tmp["outputs"] = array();

                $resultVariables = $db->getAllEquipmentVariables($report["equipment_id"], "input");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($tmp["inputs"], $inputTmp);
                }

                $resultVariables = $db->getAllEquipmentVariables($report["equipment_id"], "output");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($tmp["outputs"], $inputTmp);
                }

                array_push($response["reports"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Getting last report by user id
 * method GET
 * url /reports/last/id
 */
$app->get('/reports/last/:id', function($user_id) {
            $response = array();
            $db = new DbHandler();
            
            // fetching last report
            $result = $db->getLastReportByUser($user_id);
            
            if ($result != NULL) {
                $response["error"] = false;
                $response["id"] = $result["id"];
                $response["user_id"] = $result["user_id"];
                $response["equipment_id"] = $result["equipment_id"];
                $response["output"] = $result["output"];
                $response["console"] = $result["console"];
                $response["regulator"] = $result["regulator"];
                $response["regulator_settings"] = $result["regulator_settings"];
                $response["ip"] = $result["ip"];
                $response["report_date"] = $result["report_date"];
                $response["report_simulation_time"] = $result["report_simulation_time"];
                $response["experiment_settings"] = $result["experiment_settings"];
                $response["exp_running"] = $result["exp_running"];
                $response["notes"] = $result["notes"];
                $response["equipment_name"] = $result["equipment_name"];

                // See get equipments too
                $response["inputs"] = array();
                $response["outputs"] = array();

                $resultVariables = $db->getAllEquipmentVariables($result["equipment_id"], "input");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($response["inputs"], $inputTmp);
                }

                $resultVariables = $db->getAllEquipmentVariables($result["equipment_id"], "output");
                while ($variable = $resultVariables->fetch_assoc()) {
                    $inputTmp["sk"] = $variable["sk"];
                    $inputTmp["en"] = $variable["en"];
                    $inputTmp["db_variable"] = $variable["db_variable"];
                    $inputTmp["exp_variable"] = $variable["exp_variable"];
                    $inputTmp["type"] = $variable["type"];

                    array_push($response["outputs"], $inputTmp);
                }

                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Report error";
                echoRespnse(404, $response);
            }
        });

/******************/
/*                */
/*  RESERVATIONS  */
/*                */
/******************/

/**
 * Listing all reservations
 * method GET
 * url /reservations          
 */
$app->get('/reservations', function() {
            $response = array();
            $db = new DbHandler();

            // fetching all reservations
            $result = $db->getAllReservations();
            
            $response["error"] = false;
            $response["reservations"] = array();
            
            // looping through result and preparing tasks array
            while ($reservation = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $reservation["id"];
                $tmp["user_id"] = $reservation["user_id"];
                $tmp["login"] = $reservation["login"];
                $tmp["equipment_id"] = $reservation["equipment"];
                $tmp["equipment_name"] = $reservation["equipment_name"];
                $tmp["color"] = $reservation["color"];
                $tmp["start"] = $reservation["start"];
                $tmp["end"] = $reservation["end"];
                array_push($response["reservations"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Listing reservations in date range
 * method POST
 * url /reservations/range         
 */
$app->post('/reservations/range', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('start', 'end'));

            $response = array();

            // reading post params
            $start = $app->request->post('start');
            $end = $app->request->post('end');

            // fetching all reservations
            $db = new DbHandler();
            $result = $db->getReservationsByDateRange($start, $end);
            
            if ($result != NULL) {
                $response["error"] = false;
                $response["reservations"] = array();

                // looping through result and preparing tasks array
                while ($reservation = $result->fetch_assoc()) {
                    $tmp = array();
                    $tmp["id"] = $reservation["id"];
                    $tmp["user_id"] = $reservation["user_id"];
                    $tmp["login"] = $reservation["login"];
                    $tmp["equipment_id"] = $reservation["equipment"];
                    $tmp["equipment_name"] = $reservation["equipment_name"];
                    $tmp["color"] = $reservation["color"];
                    $tmp["start"] = $reservation["start"];
                    $tmp["end"] = $reservation["end"];
                    array_push($response["reservations"], $tmp);
                }
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to get reservations. Please try again";
                echoRespnse(200, $response);
            }
        });

/**
 * Creating new reservation in db
 * method POST
 * params - equipment_id, user_id, start, end
 * url - /reservations
 */
$app->post('/reservations', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('equipment_id', 'user_id', 'start', 'end'));

            $response = array();

            // reading post params
            $equipment_id = $app->request->post('equipment_id');
            $user_id = $app->request->post('user_id');
            $title = $app->request->post('title');
            $start = $app->request->post('start');
            $end = $app->request->post('end');

            $db = new DbHandler();
            
            if ($start < date("Y-m-d H:i:s") || $db->checkReservationAvailability(-1, $start, $end, $equipment_id)) {
                $response["error"] = true;
                $response["message"] = "Reservation time not available";
                echoRespnse(200, $response);
            } else {

                // creating new reservation
                $db = new DbHandler();
                $res = $db->createReservation($equipment_id, $user_id, $title, $start, $end);

                if ($res != NULL) {
                    $response["error"] = false;
                    $response["id"] = $res;
                    $response["message"] = "Reservation created successfully";
                    echoRespnse(201, $response);
                } else {
                    $response["error"] = true;
                    $response["message"] = "Failed to create reservation. Please try again";
                    echoRespnse(200, $response);
                }    
            }        
        });

/**
 * Updating existing reservation
 * method POST
 * params reservation_id, equipment_id, start, end
 * url - /reservations
 */
$app->post('/reservations/:id', function($reservation_id) use($app) {
            $equipment_id = $app->request->post('equipment_id');
            $title = $app->request->post('title');
            $start = $app->request->post('start');
            $end = $app->request->post('end');
            
            $db = new DbHandler();
            $response = array();
            
            if ($start < date("Y-m-d H:i:s") || $db->checkReservationAvailability($reservation_id, $start, $end, $equipment_id)) {
                $response["error"] = true;
                $response["message"] = "Reservation time not available";
                echoRespnse(200, $response);
            } else {

                // updating reservation
                $db = new DbHandler();
                $result = $db->updateReservation($reservation_id, $equipment_id, $title, $start, $end);

                if ($result) {
                    // reservation updated successfully
                    $response["error"] = false;
                    $response["message"] = "Reservation updated successfully";
                    echoRespnse(201, $response);
                } else {
                    // reservation failed to update
                    $response["error"] = true;
                    $response["message"] = "Reservation failed to update. Please try again!";
                    echoRespnse(200, $response);
                }
            }   
        });

/**
 * Deleting reservation
 * method DELETE
 * url /reservations
 */
$app->delete('/reservations/:id', function($reservation_id) use($app) {
            $response = array();
            $db = new DbHandler();

            $result = $db->deleteReservation($reservation_id);
            if ($result) {
                // reservation deleted successfully
                $response["error"] = false;
                $response["message"] = "Reservation deleted succesfully";
            } else {
                // reservation failed to delete
                $response["error"] = true;
                $response["message"] = "Reservation failed to delete. Please try again!";
            }
            echoRespnse(200, $response);
        });

/**
 * Listing all active reservations for a user
 * method GET
 * url /reservations/active 
 */
$app->get('/reservations/active/:id', function($user_id) use($app) {
            $response = array();
            $db = new DbHandler();

            // fetching all reservations
            $result = $db->getAllEquipmentReservations($user_id);
            
            $response["error"] = false;
            $response["reservations"] = array();
            
            // looping through result and preparing tasks array
            while ($reservation = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $reservation["id"];
                $tmp["user_id"] = $user_id;
                $tmp["equipment_id"] = $reservation["equipment_id"];
                $tmp["equipment_name"] = $reservation["equipment_name"];
                $tmp["color"] = $reservation["color"];
                $tmp["start"] = $reservation["start"];
                $tmp["end"] = $reservation["end"];
                $tmp["startable"] = $reservation["startable"];
                array_push($response["reservations"], $tmp);
            }

            echoRespnse(200, $response);
        });

/*******************/
/*                 */
/*      OTHER      */
/*                 */
/*******************/

/**
 *
 */
function decodeAndCreateVariablesIO($db, $equipment_id, $io) {
    $io_array = json_decode($io, true);
    foreach ($io_array as $value) {
        $res = $db->createEquipmentVariable($equipment_id, $value["sk"], $value["en"], 
            $value["databaseVariable"], $value["experimentVariable"], strtolower($value["type"]));
    }
}

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>