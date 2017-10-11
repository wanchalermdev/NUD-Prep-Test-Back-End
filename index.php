<!DOCTYPE html>
<?php
error_reporting(0);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body style="text-align: center;">
        <div>
            <h3 style="color: tomato;">อัพโหลดหลักฐานการโอนเงินโครงการ NUD PREP TEST 2017</h3>
        </div>
        <form method="post" action="./uploadFile.php" enctype="multipart/form-data">
            <input type="hidden" name="school_id" value="<?php echo $_GET['s'];  ?>" />
            กรุณาเลือกไฟล์ <input type="file" name="fileToUpload" />
            
            <br />
            <br />
            <input type="submit" value="อัพโหลด" name="submit">
        </form>
    </body>
</html>
