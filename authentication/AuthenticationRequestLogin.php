<?php
session_abort();
/*
 * Developer: Mr.Wanchalerm  Junsong (wanchalerm.dev@gmail.com)
 * Since: 2017 September 14
 * Page: Authentication Request Login
 * Discript: โมเดลที่รับค่าจาก Form Login มาเพื่อตรวจสอบว่า username และ password ที่กรอกมานั้นถูกต้องหรือไม่
 *           หากข้อมูลไม่ถูกต้องจะคืนค่าไปบอกที่ client ว่าไม่ถูกต้อง
 *           หากถูกต้องจะต้องคือค่า token ไปให้ client
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
    _pageNotFound();
} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    _postRequest($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] === "PUT") {
    _pageNotFound();
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    _pageNotFound();
} else {
    _pageNotFound();
}

/*
 * Function: _pageNotFound
 * Purpose: เพื่อใช้ return ค่าว่าไม่มีหน้าเพจนี้อยู่ในระบบ
 * Param: none
 */
function _pageNotFound(){
    http_response_code(404); // แสดงหน้าไม่พบหน้าเว็บ
    die(); // ปิดการเชื่อมต่อ
}

/*
 * Function _getRequest
 * Purpose: ไม่ใช้งาน
 */
function _getRequest($input) {}

/*
 * Function _postRequest
 * Purpose: ตรวจสอบกับฐานข้อมูลว่าเป็นสมาชิกหรือไม่
 */
function _postRequest($input) {
    /*
     * เตรียมตัวแปร
     */
    global $conn;
    $username_in = $input['username'];
    $password_in = $input['password'];
    
    /*
     * เตรียม SQL เพื่อเรียกตข้อมูลของคนที่มี Username ที่ส่งมา
     */
    $sql = "select * from user_account where user_account_username = '$username_in'";
    
    /*
     * Query จากฐานข้อมูล
     */
    $result = $conn->query($sql);
    $data = $result->fetch(PDO::FETCH_ASSOC);
    if(md5($password_in) === $data['user_account_password']){
        /*
         * ผ่านการตรวจสอบแล้วว่า Username และ Password ที่ส่งมานั้นถูกต้อง
         * การลงชื่อเข้าใช้สำเร็จ
         */
        
        /*
         * เตรียม Array สำหร้บเก็บค่าข้อมูลของบัญชีผู้ใช้ที่ลงชื่อสำเร็จ
         */
        $authen = array();
        
        
        $authen['login'] = "success";
        $authen['token_key'] = md5(rand(10000, 300000)) . md5(time());
        $authen['user_id'] = $data['user_account_id'];
        $authen['prename'] = $data['user_account_pre_name'];
        $authen['firstname'] = $data['user_account_firstname'];
        $authen['lastname'] = $data['user_account_lastname'];
        $authen['role'] = $data['user_account_type'];
        $authen['school_id'] = $data['school_id'];
        $authen['counter'] = 1;
        
        /*
         * เก็บข้อมูลลง Session
         */
        $_SESSION['AUTHEN'] = $authen;
        
        /*
         * return ค่าต่างๆ ของผู้ใช้
         */
        echo json_encode($authen);
    }else{
        /*
         * ไม่ผ่านการตรวจสอบเนื่องจากว่า password ไม่ถูกต้อง แต่ username ถูกต้อง
         * การลงชื่อเข้าใช้ไม่สำเร็จ
         */
        
        /*
         * เตรียม array สำหรับเก็บค่าข้อมูลของบัญชีผู้ใช้ที่ลงชื่อไม่สำเร็จ
         * และนับจำนวนครั้งที่ลงชื่อเข้าใช้ไม่สำเร็จด้วย
         */
        $authen = array();
        
        
        $authen['login'] = "fail";
        $authen['counter'] = 1;
        
        /*
         * เก็บข้อมูลลง Session
         */
        $_SESSION['AUTHEN'] = $authen;
        
        /*
         * return ค่าที่ลงชื่อเข้าใช้ไม่ผ่าน
         */
        echo json_encode($authen);
    }
    
}

/*
 * Function _getRequest
 * Purpose: ไม่ใช้งาน
 */
function _putRequest(){}

/*
 * Function _getRequest
 * Purpose: ไม่ใช้งาน
 */
function _deleteRequest(){}

?>