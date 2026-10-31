<?php  
//Connect to database
require 'connectDB.php';
date_default_timezone_set('Asia/Dhaka');
$d = date("Y-m-d");
$t = date("H:i:s");
$current_hour = (int)date("H");

if (isset($_GET['card_uid']) && isset($_GET['device_token'])) {
    
    $card_uid = $_GET['card_uid'];
    $device_uid = $_GET['device_token'];

    // ১. ডিভাইস চেক করা
    $sql = "SELECT * FROM devices WHERE device_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_device";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "s", $device_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        
        if ($row = mysqli_fetch_assoc($resultl)) {
            $device_mode = $row['device_mode'];
            $device_dep = $row['device_dep'];

            // ***************************************************************
            // ডিভাইস মোড যদি ১ হয় (Attendance/Meal Mode)
            // ***************************************************************
            if ($device_mode == 1) {
                
                // সময় অনুযায়ী খাবারের বেলা নির্ধারণ
                $target_col = "";
                if ($current_hour >= 1 && $current_hour < 11) $target_col = "breakfast";
                elseif ($current_hour >= 11 && $current_hour < 16) $target_col = "lunch";
                elseif ($current_hour >= 16 && $current_hour <= 23) $target_col = "dinner";

                if ($target_col == "") { echo "Canteen Closed"; exit(); }

                // ইউজার রেজিস্টার্ড কিনা দেখা
                $sql = "SELECT * FROM users WHERE card_uid=? AND add_card=1";
                $result = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($result, $sql)) {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);

                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $Uname = $row['username'];
                        $SNum = $row['serialnumber'];

                        // আজকের লগ চেক করা
                        $sql_log = "SELECT * FROM users_logs WHERE card_uid=? AND checkindate=?";
                        $stmt_log = mysqli_prepare($conn, $sql_log);
                        mysqli_stmt_bind_param($stmt_log, "ss", $card_uid, $d);
                        mysqli_stmt_execute($stmt_log);
                        $res_log = mysqli_stmt_get_result($stmt_log);

                        if ($log = mysqli_fetch_assoc($res_log)) {
                            // অলরেডি খাবার নিয়েছে কিনা চেক
                            if ($log[$target_col] == NULL || $log[$target_col] == "00:00:00") {
                                $sql_up = "UPDATE users_logs SET $target_col=? WHERE id=?";
                                $stmt_up = mysqli_prepare($conn, $sql_up);
                                mysqli_stmt_bind_param($stmt_up, "si", $t, $log['id']);
                                mysqli_stmt_execute($stmt_up);
                                echo "success:" . $Uname . ":" . $SNum . ":" . $target_col;
                            } else {
                                echo "already:" . $target_col;
                            }
                        } else {
                            // নতুন দিনের প্রথম এন্ট্রি
                            $sql_in = "INSERT INTO users_logs (username, serialnumber, card_uid, device_uid, device_dep, checkindate, $target_col) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt_in = mysqli_prepare($conn, $sql_in);
                            mysqli_stmt_bind_param($stmt_in, "sssssss", $Uname, $SNum, $card_uid, $device_uid, $device_dep, $d, $t);
                            mysqli_stmt_execute($stmt_in);
                            echo "success:" . $Uname . ":" . $SNum . ":" . $target_col;
                        }
                    } else {
                        echo "Not registered!";
                        exit();
                    }
                }
            }
            // ***************************************************************
            // ডিভাইস মোড যদি ০ হয় (Enrollment Mode) - আপনার অরিজিনাল লজিক
            // ***************************************************************
            else if ($device_mode == 0) {
                $sql = "SELECT * FROM users WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($result, $sql)) {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);

                    if ($row = mysqli_fetch_assoc($resultl)) {
                        // কার্ড আছে, জাস্ট সিলেক্ট করা
                        mysqli_query($conn, "UPDATE users SET card_select=0");
                        $sql_up = "UPDATE users SET card_select=1 WHERE card_uid=?";
                        $stmt_up = mysqli_prepare($conn, $sql_up);
                        mysqli_stmt_bind_param($stmt_up, "s", $card_uid);
                        mysqli_stmt_execute($stmt_up);
                        echo "available";
                    } else {
                        // একদম নতুন কার্ড
                        mysqli_query($conn, "UPDATE users SET card_select=0");
                        $sql_in = "INSERT INTO users (card_uid, card_select, device_uid, device_dep, user_date, add_card) VALUES (?, 1, ?, ?, CURDATE(), 0)";
                        $stmt_in = mysqli_prepare($conn, $sql_in);
                        mysqli_stmt_bind_param($stmt_in, "sss", $card_uid, $device_uid, $device_dep);
                        mysqli_stmt_execute($stmt_in);
                        echo "succesful";
                    }
                }
            }
        } else {
            echo "Invalid Device!";
        }
    }
}
?>
