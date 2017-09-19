<?php

session_abort();
error_reporting(0);
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 17
 * Page: User Account Management
 * Discript: โมเดลที่รับค่าจากฝั่ง Client จัดการข้อมูลเกี่ยวกับกรรมการ
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
 * Purpose: ใช้สำหรับค้นหาข้อมูลกรรมการ
 */

function _getRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";
    $school_id = '';
    if(isset($input['school_id'])){
        //echo "yes you have a school_id is " . $input['school_id'];
        $school_id = $input['school_id'];
    }

    /*
     * ตรวจสอบว่ามีตัวแปรจาก Method GET หรือไม่
     */
    if ((isset($input['id'])) && (isset($input['school_id']))) {
        // มี ID คือการ select รายห้อง

        $committee_id = $_GET['id'];
        

        $sql = "select * from committee where committee_id = $committee_id and school_id = '$school_id'";

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success"; // query สำเร็จ

            if ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"]['committee_id'] = $data['committee_id'];
                $operation["body"]['committee_prename'] = $data['committee_prename'];
                $operation["body"]['committee_firstname'] = $data['committee_firstname'];
                $operation["body"]['committee_lastname'] = $data['committee_lastname'];
                $operation["body"]['school_id'] = $data['school_id'];
            }
        }
    } else {
        // ไม่มี ID คือการ select ทั้งหมด
        $sql = "select * from committee inner join school where school.school_id = $school_id and committee.school_id = school.school_id order by committee_id asc";
        $i = 1;

        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success";  // query สำเร็จ

            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {

                $operation["body"][$i]['committee_id'] = $data['committee_id'];
                $operation["body"][$i]['committee_prename'] = $data['committee_prename'];
                $operation["body"][$i]['committee_firstname'] = $data['committee_firstname'];
                $operation["body"][$i]['committee_lastname'] = $data['committee_lastname'];
                $operation["body"][$i]['school_id'] = $data['school_id'];
                $i++;
            }
        }
    }
    echo json_encode($operation);
}

/*
 * Function _postRequest
 * Purpose: สร้างข้อมูลกรรมการ
 */

function _postRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $operation = array();
    $operation['operation'] = "fail";

    $committee_prename= $input['committee_prename'];
    $committee_firstname = $input['committee_firstname'];
    $committee_lastname = $input['committee_lastname'];
    $school_id = $input['school_id'];

    /*
     * เตรียม SQL เพื่อเพิ่มข้อมูลกรรมการ
     */
    
    $sql = "insert into committee ("
            . "committee_prename, "
            . "committee_firstname, "
            . "committee_lastname, "
            . "school_id "
            . ") values("
            . "'$committee_prename', "
            . "'$committee_firstname', "
            . "'$committee_lastname', "
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
 * Purpose: แก้ไขข้อมูลกรรมการ
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
        $committee_id = $input['id'];
        $committee_prename = $input['committee_prename'];
        $committee_firstname = $input['committee_firstname'];
        $committee_lastname = $input['committee_lastname'];
        $school_id = $input['school_id'];

        /*
         * เตรียม SQL เพื่อแก้ไขข้อมูลกรรมการ
         */
        $sql = "update committee set "
                . "committee_prename = '$committee_prename', "
                . "committee_firstname = '$committee_firstname', "
                . "committee_lastname = '$committee_lastname', "
                . "school_id = '$school_id' "
                . "where committee_id = $committee_id";
        /*
         * Operate คำสั่งลงฐานข้อมูล
         */
        if ($result = $conn->query($sql)) {
            $operation['operation'] = "success";
            $operation['body'] = '';
        }
    }
    echo json_encode($operation);
}

/*
 * Function _getRequest
 * Purpose: เพื่อใช้ลบข้อมูลกรรมการ
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
         * เตรียม SQL เพื่อลบข้อมูลกรรมการ
         */
        $committee_id = $input['id'];
        $sql = "delete from committee where committee_id = $building_id";

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