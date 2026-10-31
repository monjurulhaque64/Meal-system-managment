<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' type='text/css' href="bootstrap.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="header.css"/>
    
    <style>
        /* ইনপুট গ্রুপ এবং আইকন ফিক্স করার জন্য স্টাইল */
        .input-group {
            display: flex !important;
            width: 100%;
        }
        .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            flex: 1;
        }
        .input-group-addon {
            cursor: pointer;
            background-color: #ffffff !important;
            border: 1px solid #ccc !important;
            border-left: none !important;
            display: flex;
            align-items: center;
            padding: 0 12px;
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }
        .input-group-addon:hover {
            background-color: #f8f9fa !important;
        }
        /* সাকসেস/এরর মেসেজ স্টাইল */
        .up_info1, .up_info2 {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<header>
<div class="header">
    <div class="logo">
        <a href="UsersLog.php">Sonaimuri Residential Model School Attendance</a>
    </div>
</div>

<?php  
// এরর এবং সাকসেস মেসেজ হ্যান্ডলিং (আগের মতোই)
if (isset($_GET['error']) && $_GET['error'] == "wrongpasswordup") {
    echo '<script>setTimeout(function(){ $(".up_info1").fadeIn(200).text("The password is wrong!!"); $("#admin-account").modal("show"); }, 500);</script>';
} 
if (isset($_GET['success']) && $_GET['success'] == "updated") {
    echo '<script>setTimeout(function(){ $(".up_info2").fadeIn(200).text("Your Account has been updated"); }, 500);</script>';
}
?>

<div class="topnav" id="myTopnav">
    <a href="UsersLog.php">Users Log</a>
    <a href="index.php">Users</a>
    
    <?php if (isset($_SESSION['Admin-role']) && $_SESSION['Admin-role'] == 1): ?>
        <a href="ManageUsers.php">Manage Users</a>
        <a href="admin_manage.php">Admin Management</a> 
    <?php endif; ?>
    
    <?php  
        if (isset($_SESSION['Admin-name'])) {
            echo '<a href="#" data-toggle="modal" data-target="#admin-account">'.$_SESSION['Admin-name'].'</a>';
            echo '<a href="logout.php">Log Out</a>';
        } else {
            echo '<a href="login.php">Log In</a>';
        }
    ?>
    <a href="javascript:void(0);" class="icon" onclick="navFunction()"><i class="fa fa-bars"></i></a>
</div>

<div class="up_info1 alert alert-danger"></div>
<div class="up_info2 alert alert-success"></div>
</header>

<div class="modal fade" id="admin-account" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Update Your Account Info:</h3>
      </div>
      
      <form action="ac_update.php" method="POST" autocomplete="off">
          <div class="modal-body">
              <div class="form-group">
                  <label>Admin Name:</label>
                  <input type="text" name="up_name" class="form-control" value="<?php echo $_SESSION['Admin-name']; ?>" required>
              </div>
              
              <div class="form-group">
                  <label>Admin E-mail:</label>
                  <input type="email" name="up_email" class="form-control" value="<?php echo $_SESSION['Admin-email']; ?>" required>
              </div>
              
              <div class="form-group">
                  <label>New Password (ঐচ্ছিক):</label>
                  <div class="input-group">
                      <input type="password" name="new_pwd" id="new_pwd" class="form-control" placeholder="নতুন পাসওয়ার্ড দিন" autocomplete="new-password">
                      <span class="input-group-addon" onclick="togglePass('new_pwd', 'eye_new')">
                          <i class="fa fa-eye" id="eye_new"></i>
                      </span>
                  </div>
              </div>
              
              <hr>
              
              <div class="form-group">
                  <label style="color: #e74c3c;"><b>Current Password (নিশ্চিত করতে বর্তমান পাসওয়ার্ড দিন):</b></label>
                  <div class="input-group">
                      <input type="password" name="up_pwd" id="up_pwd" class="form-control" placeholder="Enter Current Password" required autocomplete="current-password">
                      <span class="input-group-addon" onclick="togglePass('up_pwd', 'eye_curr')">
                          <i class="fa fa-eye" id="eye_curr"></i>
                      </span>
                  </div>
              </div>
          </div>
          
          <div class="modal-footer">
            <button type="submit" name="update" class="btn btn-success">Save changes</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    function navFunction() {
      var x = document.getElementById("myTopnav");
      x.className = (x.className === "topnav") ? "topnav responsive" : "topnav";
    }

    // পাসওয়ার্ড শো/হাইড ফাংশন
    function togglePass(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>