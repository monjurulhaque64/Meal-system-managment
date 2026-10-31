<?php
session_start();
require 'connectDB.php';
date_default_timezone_set('Asia/Dhaka');

if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}

// ডাটা আপডেট করার লজিক
if (isset($_POST['update_log'])) {
    $id = mysqli_real_escape_string($conn, $_POST['log_id']);
    $date = mysqli_real_escape_string($conn, $_POST['edit_date']);
    
    // ১২ ঘণ্টা ফরম্যাটকে ডাটাবেসের জন্য ২৪ ঘণ্টা (H:i:s) ফরম্যাটে রূপান্তর
    // যদি ইনপুট খালি থাকে তবে 00:00:00 সেট করা হবে
    $breakfast = !empty($_POST['edit_breakfast']) ? date("H:i:s", strtotime($_POST['edit_breakfast'])) : "00:00:00";
    $lunch = !empty($_POST['edit_lunch']) ? date("H:i:s", strtotime($_POST['edit_lunch'])) : "00:00:00";
    $dinner = !empty($_POST['edit_dinner']) ? date("H:i:s", strtotime($_POST['edit_dinner'])) : "00:00:00";

    $update_sql = "UPDATE users_logs SET checkindate='$date', breakfast='$breakfast', lunch='$lunch', dinner='$dinner' WHERE id='$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Meal Logs Updated Successfully!'); window.location.href='UsersLog.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// ডাটা ফেচ করা
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM users_logs WHERE id='$id'");
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
    } else {
        header("location: UsersLog.php");
    }
} else {
    header("location: UsersLog.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Meal Log | Smart UI</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">

    <style>
        body { background: linear-gradient(135deg, #1a5e63 0%, #0d2c2f 100%); min-height: 100vh; font-family: 'Poppins', sans-serif; display: flex; align-items: center; color: #fff; }
        .edit-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.15); padding: 40px; border-radius: 25px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); width: 100%; max-width: 600px; margin: auto; }
        .user-header { border-bottom: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 25px; padding-bottom: 15px; }
        .user-badge { background: rgba(26, 188, 156, 0.2); color: #1abc9c; padding: 5px 15px; border-radius: 50px; font-size: 0.85rem; display: inline-block; margin-top: 10px; }
        .form-group label { font-weight: 500; font-size: 0.9rem; color: #bdc3c7; margin-bottom: 8px; }
        .form-control { background: rgba(0, 0, 0, 0.3) !important; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; color: #fff !important; height: 50px; }
        .btn-update { background: #1abc9c; border: none; height: 55px; font-weight: 600; border-radius: 12px; color: #fff; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; width: 100%; }
        .btn-update:hover { background: #16a085; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(26, 188, 156, 0.4); }
        .btn-cancel { color: #e74c3c; text-align: center; display: block; margin-top: 15px; font-weight: 500; }
    </style>
</head>
<body>

<div class="container">
    <div class="edit-card">
        <div class="user-header text-center">
            <h4>Edit Meal Record</h4>
            <div class="user-badge">
                <i class="fas fa-user mr-1"></i> <?php echo $row['username']; ?> | Serial: <?php echo $row['serialnumber']; ?>
            </div>
        </div>
        
        <form method="POST">
            <input type="hidden" name="log_id" value="<?php echo $row['id']; ?>">
            
            <div class="form-group">
                <label><i class="far fa-calendar-alt mr-1"></i> Log Date</label>
                <input type="date" name="edit_date" class="form-control" value="<?php echo $row['checkindate']; ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Breakfast</label>
                        <input type="text" name="edit_breakfast" id="bf_picker" class="form-control" 
                               value="<?php echo ($row['breakfast'] != "00:00:00") ? date('h:i A', strtotime($row['breakfast'])) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Lunch</label>
                        <input type="text" name="edit_lunch" id="ln_picker" class="form-control" 
                               value="<?php echo ($row['lunch'] != "00:00:00") ? date('h:i A', strtotime($row['lunch'])) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Dinner</label>
                        <input type="text" name="edit_dinner" id="dn_picker" class="form-control" 
                               value="<?php echo ($row['dinner'] != "00:00:00") ? date('h:i A', strtotime($row['dinner'])) : ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" name="update_log" class="btn btn-update">
                    <i class="fas fa-check-circle mr-2"></i> Save Changes
                </button>
                <a href="UsersLog.php" class="btn btn-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Logs
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const config = {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
        allowInput: true
    };

    flatpickr("#bf_picker", config);
    flatpickr("#ln_picker", config);
    flatpickr("#dn_picker", config);
</script>

</body>
</html>