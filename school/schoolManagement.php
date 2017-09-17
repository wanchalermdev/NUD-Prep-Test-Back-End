<?php

session_abort();
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 17
 * Page: User Account Management
 * Discript: โมเดลที่รับค่าจากฝั่ง Client เพื่อจัดการ เพิ่ม แก้ไข ลบ ศูนย์สอบ
 */

/*
 * initial model
 */
include_once '../INITIAL_MODEL.php';

/*
 * HEADER
 */
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    _getRequest($_GET);
} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    _postRequest($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    _putRequest();
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    _deleteRequest();
} else {
    _pageNotFound();
}

/*
 * Function: _pageNotFound
 * Purpose: เพื่อใช้ return ค่าว่าไม่มีหน้าเพจนี้อยู่ในระบบ
 * Param: none
 */

function _pageNotFound() {
    http_response_code(404); // แสดงหน้าไม่พบหน้าเว็บ
    die(); // ปิดการเชื่อมต่อ
}

/*
 * Function _getRequest
 * Purpose: ใช้สำหรับคืนค่าของข้อมูลผู้ใช้
 */

function _getRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";
    /*
     * ตรวจสอบว่ามีตัวแปรจาก Method GET หรือไม่
     */
    if (isset($input['id'])) {
        // มี ID คือการ select รายโรงเรียน

        $schoolId = $_GET['id'];

        $sql = "select * from school where school_id = $schoolId";

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success"; // query สำเร็จ

            if ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"]['school_edu_year'] = $data['school_edu_year'];
                $operation["body"]['school_name'] = $data['school_name'];
                $operation["body"]['school_address'] = $data['school_address'];
                $operation["body"]['school_district'] = $data['school_district'];
                $operation["body"]['school_city'] = $data['school_city'];
                $operation["body"]['school_postcode'] = $data['school_postcode'];
            }
        }
    } else {
        // ไม่มี ID คือการ select ทั้งหมด
        $sql = "select * from school order by school_id asc";
        $i = 1;

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success";  // query สำเร็จ

            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {

                $operation["body"][$i]['school_edu_year'] = $data['school_edu_year'];
                $operation["body"][$i]['school_name'] = $data['school_name'];
                $operation["body"][$i]['school_addressschool_address'] = $data['school_address'];
                $operation["body"][$i]['school_district'] = $data['school_district'];
                $operation["body"][$i]['school_city'] = $data['school_city'];
                $operation["body"][$i]['school_postcode'] = $data['school_postcode'];
                $i++;
            }
        }
    }
    echo json_encode($operation);
}

/*
 * Function _postRequest
 * Purpose: ตรวจสอบกับฐานข้อมูลว่าเป็นสมาชิกหรือไม่
 */

function _postRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";

    $username_in = $input['username'];
    $password_in = md5($input['password']);
    $user_account_personal_code = $input['personal_id'];
    $school_id_in = $input['school_id'];
    $user_account_pre_name = $input['prename'];
    $user_account_firstname = $input['firstname'];
    $user_account_lastname = $input['lastname'];
    $user_account_phone = $input['phone'];
    $user_account_email = $input['email'];
    $user_account_type = "coordinator_committee";
    $user_account_edu_year = 2560;
    $user_account_position = $input[''];

    /*
     * เตรียม SQL เพื่อสร้างบัญชีผู้ใช้ใหม่
     */
    $sql = "insert into user_account("
            . "user_account_username, "
            . "user_account_password, "
            . "user_account_type, "
            . "user_account_personal_code, "
            . "user_account_edu_year, "
            . "user_account_pre_name, "
            . "user_account_firstname, "
            . "user_account_lastname, "
            . "user_account_phone, "
            . "user_account_email, "
            . "school_id,"
            . "user_account_position"
            . ") values("
            . "'$username_in', "
            . "'$password_in', "
            . "'$user_account_type', "
            . "'$user_account_personal_code', "
            . "'$user_account_edu_year', "
            . "'$user_account_pre_name', "
            . "'$user_account_firstname', "
            . "'$user_account_lastname', "
            . "'$user_account_phone', "
            . "'$user_account_email', "
            . "'$school_id_in', "
            . "''"
            . ")";

    /*
     * Operate คำสั่งลงฐานข้อมูล
     */
    if ($result = $conn->query($sql)) {
        $operation['operation'] = "success";
        $operation['body'] = $sql;
    }
    echo json_encode($operation);
}

/*
 * Function _getRequest
 * Purpose: ไม่ใช้งาน
 */

function _putRequest() {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    parse_str(file_get_contents('php://input'), $input);
    $operation = array();
    $operation['operation'] = "fail";
    if (isset($input['id'])) {

        $username_in = $input['username'];
        $password_in = md5($input['password']);
        $user_account_personal_code = $input['personal_id'];
        $school_id_in = $input['school_id'];
        $user_account_pre_name = $input['prename'];
        $user_account_firstname = $input['firstname'];
        $user_account_lastname = $input['lastname'];
        $user_account_phone = $input['phone'];
        $user_account_email = $input['email'];
        $user_account_type = "coordinator_committee";
        $user_account_edu_year = 2560;
        $user_account_position = $input[''];
        
        $user_id = $input['id'];

        /*
         * เตรียม SQL เพื่อแก้ไขข้อมูลบัญชีผู้ใช้
         */
        $sql = "update user_account set "
                . "user_account_username = '$username_in', "
                . "user_account_password = '$password_in', "
                . "user_account_pre_name = '$user_account_pre_name', "
                . "user_account_firstname = '$user_account_firstname', "
                . "user_account_lastname = '$user_account_lastname', "
                . "user_account_phone = '$user_account_phone', "
                . "user_account_email = '$user_account_email', "
                . "school_id = '$school_id_in' "
                . "where user_account_id = $user_id";

        /*
         * Operate คำสั่งลงฐานข้อมูล
         */
        if ($result = $conn->prepare($sql)) {
            $result->execute();
            $operation['operation'] = "success";
            $operation['body'] = '';
        }
    }
    echo json_encode($operation);
}

/*
 * Function _getRequest
 * Purpose: เพื่อใช้ลบบัญชีผู้ใช้ออกจากฐานข้อมูล
 */

function _deleteRequest() {

    /*
     * เตรียมตัวแปร
     */
    global $conn;
    parse_str(file_get_contents('php://input'), $input);
    $operation = array();
    $operation['operation'] = "fail";
    if (isset($input['id'])) {

        /*
         * เตรียม SQL เพื่อแก้ไขข้อมูลบัญชีผู้ใช้
         */
        $user_id = $input['id'];
        $sql = "delete from user_account where user_account_id = $user_id";

        /*
         * Operate คำสั่งลงฐานข้อมูล
         */
        if ($result = $conn->prepare($sql)) {
            $result->execute();
            $operation['operation'] = "success";
            $operation['body'] = '';
        }
    }
    echo json_encode($operation);
}

?>