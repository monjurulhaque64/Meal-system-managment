<?php 
session_start();
require('connectDB.php');

if (isset($_POST['update'])) {
    $current_session_email = $_SESSION['Admin-email'];
    $up_name = $_POST['up_name'];
    $up_email = $_POST['up_email'];
    $current_password = $_POST['up_pwd']; // যাচাইয়ের জন্য বর্তমান পাসওয়ার্ড
    $new_password = $_POST['new_pwd'];     // নতুন পাসওয়ার্ড (যদি থাকে)

    if (empty($up_name) || empty($up_email) || empty($current_password)) {
        header("location: UsersLog.php?error=emptyfields");
        exit();
    } else {
        // ১. আগে চেক করি বর্তমান পাসওয়ার্ডটি ডাটাবেসের সাথে মেলে কি না
        $sql = "SELECT * FROM admin WHERE admin_email=?";  
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("location: UsersLog.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $current_session_email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                // পাসওয়ার্ড ভেরিফাই করা
                $pwdCheck = password_verify($current_password, $row['admin_pwd']);
                if ($pwdCheck == false) {
                    // যদি পাসওয়ার্ড ভুল হয় তবে এই এররটি দিবে
                    header("location: UsersLog.php?error=wrongpasswordup");
                    exit();
                } else {
                    // ২. পাসওয়ার্ড সঠিক হলে আপডেট লজিক
                    if (!empty($new_password)) {
                        // যদি নতুন পাসওয়ার্ড ইনপুট দেওয়া হয়
                        $hashed_pwd = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql_up = "UPDATE admin SET admin_name=?, admin_email=?, admin_pwd=? WHERE admin_email=?";
                        $stmt_up = mysqli_stmt_init($conn);
                        mysqli_stmt_prepare($stmt_up, $sql_up);
                        mysqli_stmt_bind_param($stmt_up, "ssss", $up_name, $up_email, $hashed_pwd, $current_session_email);
                    } else {
                        // নতুন পাসওয়ার্ড না দিলে শুধু নাম ও ইমেইল আপডেট
                        $sql_up = "UPDATE admin SET admin_name=?, admin_email=? WHERE admin_email=?";
                        $stmt_up = mysqli_stmt_init($conn);
                        mysqli_stmt_prepare($stmt_up, $sql_up);
                        mysqli_stmt_bind_param($stmt_up, "sss", $up_name, $up_email, $current_session_email);
                    }

                    if (mysqli_stmt_execute($stmt_up)) {
                        // সেশন আপডেট করা
                        $_SESSION['Admin-name'] = $up_name;
                        $_SESSION['Admin-email'] = $up_email;
                        header("location: UsersLog.php?success=updated");
                        exit();
                    }
                }
            } else {
                header("location: UsersLog.php?error=nouser");
                exit();
            }
        }
    }
} else {
    header("location: UsersLog.php");
    exit();
}
?>