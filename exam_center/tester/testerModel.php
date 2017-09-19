<?php

session_abort();
error_reporting(0);
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 17
 * Page: User Account Management
 * Discript: โมเดลที่รับค่าจากฝั่ง Client จัดการข้อมูลเกี่ยวกับห้องสอบ
 */

/*
 * initial model
 */
require_once '../../DATABASE_CONNECTION.php';

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
 * Purpose: ใช้สำหรับค้นหาข้อมูลห้องเรียน
 */

function _getRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";
    $school_id = '';
    if (isset($input['school_id'])) {
        //echo "yes you have a school_id is " . $input['school_id'];
        $school_id = $input['school_id'];
    }

    /*
     * ตรวจสอบว่ามีตัวแปรจาก Method GET หรือไม่
     */
    if (isset($input['id'])) {
        // มี ID คือการ select รายห้อง

        $tester_id = $_GET['id'];


        $sql = "select * from tester where tester_id = $tester_id and school_id = '$school_id'";

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success"; // query สำเร็จ

            if ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"]['tester_id'] = $data['tester_id'];
                $operation["body"]['tester_personal_code'] = $data['tester_personal_code'];
                $operation["body"]['tester_prename'] = $data['tester_prename'];
                $operation["body"]['tester_firstname'] = $data['tester_firstname'];
                $operation["body"]['tester_firstname'] = $data['tester_firstname'];
                $operation["body"]['tester_lastname'] = $data['tester_lastname'];
                $operation["body"]['school_id'] = $data['school_id'];
                $operation["body"]['tester_phone'] = $data['tester_phone'];
                $operation["body"]['tester_type'] = $data['tester_type'];
                $operation["body"]['tester_level'] = $data['tester_level'];
            }
        }
    } else {
        // ไม่มี ID คือการ select ทั้งหมด
        $sql = "select * from tester inner join school where school.school_id = $school_id and tester.school_id = school.school_id order by tester.tester_id asc";
        $i = 1;

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success";  // query สำเร็จ

            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"][$i]['tester_id'] = $data['tester_id'];
                $operation["body"][$i]['tester_personal_code'] = $data['tester_personal_code'];
                $operation["body"][$i]['tester_prename'] = $data['tester_prename'];
                $operation["body"][$i]['tester_firstname'] = $data['tester_firstname'];
                $operation["body"][$i]['tester_firstname'] = $data['tester_firstname'];
                $operation["body"][$i]['tester_lastname'] = $data['tester_lastname'];
                $operation["body"][$i]['school_id'] = $data['school_id'];
                $operation["body"][$i]['tester_phone'] = $data['tester_phone'];
                $operation["body"][$i]['tester_type'] = $data['tester_type'];
                $operation["body"][$i]['tester_level'] = $data['tester_level'];
                $i++;
            }
        }
    }
    echo json_encode($operation);
}

/*
 * Function _postRequest
 * Purpose: สร้างข้อมูลอาคารใหม่
 */

function _postRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";

    $tester_personal_code = $input['tester_personal_code'];
    $tester_prensme = $input['tester_prensme'];
    $tester_firstname = $input['tester_firstname'];
    $tester_lastname = $input['tester_lastname'];
    $tester_phone = $input['tester_phone'];
    $tester_type = $input['tester_type'];
    $tester_level = $input['tester_level'];

    $school_id = $input['school_id'];

    /*
     * เตรียม SQL เพื่อเพิ่มข้อมูลอาคารใหม่
     */

    $sql = "insert into tester ("
            . "tester_personal_code, "
            . "tester_prename, "
            . "tester_firstname, "
            . "tester_lastname, "
            . "tester_phone, "
            . "tester_type, "
            . "tester_level, "
            . "school_id "
            . ") values("
            . "'$tester_personal_code', "
            . "'$tester_prensme', "
            . "'$tester_firstname', "
            . "'$tester_lastname', "
            . "'$tester_phone', "
            . "'$tester_type', "
            . "'$tester_level', "
            . "'$school_id' "
            . ")";
    /*
     * Operate คำสั่งลงฐานข้อมูล
     */
    if ($result = $conn->prepare($sql)) {
        $result->execute();
        $operation['operation'] = "success";
        $operation['body'] = '';
    }

    echo json_encode($operation);
}

/*
 * Function _getRequest
 * Purpose: แก้ไขข้อมูลอาคารเรียน
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
        
        $tester_id = $input['id'];
        $tester_personal_code = $input['tester_personal_code'];
        $tester_prename = $input['tester_prename'];
        $tester_firstname = $input['tester_firstname'];
        $tester_lastname = $input['tester_lastname'];
        $tester_phone = $input['tester_phone'];
        $tester_type = $input['tester_type'];
        $tester_level = $input['tester_level'];
        $school_id = $input['school_id'];
        
        /*
         * เตรียม SQL เพื่อแก้ไขข้ออาคาร
         */
        $sql = "update tester set "
                . "tester_personal_code = '$tester_personal_code', "
                . "tester_prename = '$tester_prename', "
                . "tester_firstname = '$tester_firstname', "
                . "tester_lastname = '$tester_lastname', "
                . "tester_phone = '$tester_phone', "
                . "tester_type = '$tester_type', "
                . "tester_level = '$tester_level', "
                . "school_id = '$school_id' "
                . "where tester_id = $tester_id";
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
 * Purpose: เพื่อใช้ลบข้อมูลอาคาร
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
         * เตรียม SQL เพื่อลบข้อมูลอาคาร
         */
        $tester_id = $input['id'];
        $sql = "delete from tester where tester_id = $tester_id";

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