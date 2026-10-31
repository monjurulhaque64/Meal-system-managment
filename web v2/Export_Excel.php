<?php
session_start(); // সেশন শুরু করা বাধ্যতামূলক
require 'connectDB.php';

$output = '';

if(isset($_POST["To_Excel"])){
    
    // ডিফল্ট কোয়েরি (যাতে কোনো ফিল্টার না থাকলে অন্তত আজকের ডেটা দেখায়)
    $searchQuery = "checkindate = CURDATE()"; 
    $Start_date = date("Y-m-d");

    // ১. তারিখ ফিল্টার (Date Filter)
    if (!empty($_POST['date_sel_start'])) {
        $Start_date = $_POST['date_sel_start'];
        if (!empty($_POST['date_sel_end'])) {
            $End_date = $_POST['date_sel_end'];
            $searchQuery = "checkindate BETWEEN '$Start_date' AND '$End_date'";
        } else {
            $searchQuery = "checkindate = '$Start_date'";
        }
    }

    // ২. টাইম ফিল্টার (Meal Time Filter)
    if (isset($_POST['time_sel']) && $_POST['time_sel'] != "0") {
        // টাইম ফিল্ড অনুযায়ী সিলেক্ট করা
        $time_type = $_POST['time_sel']; // e.g. breakfast, lunch, or dinner
        
        if (!empty($_POST['time_sel_start']) && !empty($_POST['time_sel_end'])) {
            $s_time = $_POST['time_sel_start'];
            $e_time = $_POST['time_sel_end'];
            $searchQuery .= " AND $time_type BETWEEN '$s_time' AND '$e_time'";
        } elseif (!empty($_POST['time_sel_start'])) {
            $s_time = $_POST['time_sel_start'];
            $searchQuery .= " AND $time_type = '$s_time'";
        }
    }

    // ৩. কার্ড আইডি ফিল্টার (Card Filter)
    if (!empty($_POST['card_sel'])) {
        $card_uid = $_POST['card_sel'];
        $searchQuery .= " AND card_uid = '$card_uid'";
    }

    // ৪. ডিভাইস/ডিপার্টমেন্ট ফিল্টার (Device Filter)
    if (!empty($_POST['dev_sel'])) {
        $dev_uid = $_POST['dev_sel'];
        $searchQuery .= " AND device_uid = '$dev_uid'";
    }

    // চূড়ান্ত SQL কুয়েরি
    $sql = "SELECT * FROM users_logs WHERE $searchQuery ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0){
        // Excel হেডার সেট করা
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=User_Log_'.$Start_date.'.xls');
        
        $output .= '
                  <table border="1">  
                    <TR>
                      <TH style="background-color: #f2f2f2;">ID</TH>
                      <TH style="background-color: #f2f2f2;">Name</TH>
                      <TH style="background-color: #f2f2f2;">Roll / Serial</TH>
                      <TH style="background-color: #f2f2f2;">Date</TH>
                      <TH style="background-color: #f2f2f2;">Breakfast</TH>
                      <TH style="background-color: #f2f2f2;">Lunch</TH>
                      <TH style="background-color: #f2f2f2;">Dinner</TH>
                    </TR>';

        while($row = mysqli_fetch_assoc($result)) {
            // Breakfast সময় ফরম্যাট
            $breakfast = (!empty($row['breakfast']) && $row['breakfast'] != "00:00:00") 
                ? date("h:i A", strtotime($row['breakfast'])) 
                : '--:--';

            // Lunch সময় ফরম্যাট
            $lunch = (!empty($row['lunch']) && $row['lunch'] != "00:00:00") 
                ? date("h:i A", strtotime($row['lunch'])) 
                : '--:--';

            // Dinner সময় ফরম্যাট
            $dinner = (!empty($row['dinner']) && $row['dinner'] != "00:00:00") 
                ? date("h:i A", strtotime($row['dinner'])) 
                : '--:--';

            // Date ফরম্যাট
            $checkindate = !empty($row['checkindate']) ? date("d M, Y", strtotime($row['checkindate'])) : '';

            $output .= '
                        <TR> 
                            <TD>'.$row['id'].'</TD>
                            <TD>'.$row['username'].'</TD>
                            <TD>'.$row['serialnumber'].'</TD>
                            <TD>'.$checkindate.'</TD>
                            <TD>'.$breakfast.'</TD>
                            <TD>'.$lunch.'</TD>
                            <TD>'.$dinner.'</TD>
                        </TR>';
        }
        $output .= '</table>';
        echo $output;
        exit();
    } else {
        // ডেটা না থাকলে আগের পেজে ফেরত পাঠানো
        echo "<script>alert('No records found for this filter!'); window.location='UsersLog.php';</script>";
        exit();
    }
}
?>
