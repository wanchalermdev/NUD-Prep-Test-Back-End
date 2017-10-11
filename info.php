<?php
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 17
 * Page: Info All The System
 * Discript: ส่วนจัดการสารสนเทศของระบบ
 */
session_abort();
error_reporting(0);
include_once './DATABASE_CONNECTION.php';
include_once './exam_center/examCenterInfo.php';



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

/*
 * Check Method Receives
 */
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
    if(isset($input['school_id'])){
        //echo "yes you have a school_id is " . $input['school_id'];
        $school_id = $input['school_id'];
    }

    /*
     * ตรวจสอบว่ามีตัวแปรจาก Method GET หรือไม่
     */
    if ((isset($input['id'])) && (isset($input['school_id']))) {
        // มี ID คือการ select รายห้อง

        $exam_room_id = $_GET['id'];
        

        //$sql = "select * from exam_room inner join building where exam_room.exam_room_id =  and building.school_id = '' and exam_room.building_id = building.school_id";
        $sql = "select * from exam_room inner join building where exam_room.building_id = building.building_id and building.school_id = '$school_id' and exam_room.exam_room_id = $exam_room_id";
        //echo $sql;
        
        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success"; // query สำเร็จ

            if ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                $operation["body"]['exam_room_id'] = $data['exam_room_id'];
                $operation["body"]['exam_room_name'] = $data['exam_room_name'];
                $operation["body"]['exam_room_capacity'] = $data['exam_room_capacity'];
                $operation["body"]['exam_room_committee1'] = $data['exam_room_committee1'];
                $operation["body"]['exam_room_committee2'] = $data['exam_room_committee2'];
                $operation["body"]['building_id'] = $data['building_id'];
            }
        }
    } else {
        // ไม่มี ID คือการ select ทั้งหมด
        $sql = "select * from exam_room inner join school, building where school.school_id = $school_id and building.building_id like exam_room.building_id and building.school_id = school.school_id order by building.building_id, exam_room.exam_room_id asc";
        $i = 1;
        if ($result = $conn->query($sql)) {

            $operation['operation'] = "success";  // query สำเร็จ

            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {

                $operation["body"][$i]['exam_room_id'] = $data['exam_room_id'];
                $operation["body"][$i]['exam_room_name'] = $data['exam_room_name'];
                $operation["body"][$i]['exam_room_capacity'] = $data['exam_room_capacity'];
                $operation["body"][$i]['exam_room_committee1'] = $data['exam_room_committee1'];
                $operation["body"][$i]['exam_room_committee2'] = $data['exam_room_committee2'];
                $operation["body"][$i]['building_id'] = $data['building_id'];
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

    $exam_room_name = $input['exam_room_name'];
    $exam_room_capacity = $input['exam_room_capacity'];
    $exam_room_committee1 = $input['exam_room_committee1'];
    $exam_room_committee2 = $input['exam_room_committee2'];
    $building_id = $input['building_id'];


    /*
     * เตรียม SQL เพื่อเพิ่มข้อมูลอาคารใหม่
     */
    
    $sql = "insert into exam_room ("
            . "exam_room_name, "
            . "exam_room_capacity, "
            . "exam_room_committee1, "
            . "exam_room_committee2, "
            . "building_id "
            . ") values("
            . "'$exam_room_name', "
            . "'$exam_room_capacity', "
            . "'$exam_room_committee1', "
            . "'$exam_room_committee2', "
            . "'$building_id' "
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
        $exam_room_id = $input['id'];
        $exam_room_name = $input['exam_room_name'];
        $exam_room_capacity = $input['exam_room_capacity'];
        $exam_room_committee1 = $input['exam_room_committee1'];
        $exam_room_committee2 = $input['exam_room_committee2'];
        $building_id = $input['building_id'];

        /*
         * เตรียม SQL เพื่อแก้ไขข้ออาคาร
         */
        $sql = "update exam_room set "
                . "exam_room_name = '$exam_room_name', "
                . "exam_room_capacity = '$exam_room_capacity', "
                . "exam_room_committee1 = '$exam_room_committee1', "
                . "exam_room_committee2 = '$exam_room_committee2', "
                . "building_id = '$building_id' "
                . "where exam_room_id = $exam_room_id";
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
        $exam_room_id = $input['id'];
        $sql = "delete from exam_room where exam_room_id = $exam_room_id";

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