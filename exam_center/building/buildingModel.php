<?php

session_abort();
error_reporting(0);
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 17
 * Page: User Account Management
 * Discript: โมเดลที่รับค่าจากฝั่ง Client จัดการข้อมูลเกี่ยวกับอาคารสอบของแต่ละศูนย์สอบ
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
 * Purpose: ใช้สำหรับค้นหาข้อมูลอาคารเรียน
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
        // มี ID คือการ select รายอาคาร

        $building_id = $_GET['id'];

        $sql = "select * from building where building_id = $building_id";

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success"; // query สำเร็จ

            if ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"]['building_id'] = $data['building_id'];
                $operation["body"]['building_name'] = $data['building_name'];
                $operation["body"]['school_id'] = $data['school_id'];
            }
        }
    } else {
        // ไม่มี ID คือการ select ทั้งหมด
        $sql = "select * from building order by school_id asc";
        $i = 1;

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success";  // query สำเร็จ

            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {

                $operation["body"][$i]['building_id'] = $data['building_id'];
                $operation["body"][$i]['building_name'] = $data['building_name'];
                $operation["body"][$i]['school_id'] = $data['school_id'];
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

    $building_id = $input['building_id'];
    $building_name = $input['building_name'];
    $school_id = $input['school_id'];


    /*
     * เตรียม SQL เพื่อเพิ่มข้อมูลอาคารใหม่
     */
    $sql = "insert into building("
            . "building_id, "
            . "building_name, "
            . "school_id "
            . ") values("
            . "'$building_id', "
            . "'$building_name', "
            . "'$school_id' "
            . ")";

    /*
     * Operate คำสั่งลงฐานข้อมูล
     */
    if ($result = $conn->query($sql)) {
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

        $building_id = $input['id'];
        $building_name = $input['building_name'];
        $school_id = $input['school_id'];

        /*
         * เตรียม SQL เพื่อแก้ไขข้ออาคาร
         */
        $sql = "update building set "
                . "building_name = '$building_name', "
                . "school_id = '$school_id' "
                . "where school_id = $school_id";

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
        $building_id = $input['id'];
        $sql = "delete from building where building_id = $building_id";

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