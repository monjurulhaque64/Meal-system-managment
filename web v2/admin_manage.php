<?php
session_start();
require 'connectDB.php';

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['Admin-role']) || $_SESSION['Admin-role'] != 1) {
    header("location: UsersLog.php");
    exit();
}

// ২. অ্যাডমিন যোগ করার লজিক
$message = "";
if (isset($_POST['add_admin'])) {
    $name = mysqli_real_escape_string($conn, $_POST['admin_name']);
    $email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $pass = $_POST['admin_pwd']; 
    $role = $_POST['admin_role'];

    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);

    $check_email = mysqli_query($conn, "SELECT admin_email FROM admin WHERE admin_email='$email'");
    if(mysqli_num_rows($check_email) > 0){
        $message = "<div class='alert alert-danger animate__animated animate__shakeX'>Email already exists!</div>";
    } else {
        $sql = "INSERT INTO admin (admin_name, admin_email, admin_pwd, role) VALUES ('$name', '$email', '$pass_hash', '$role')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success animate__animated animate__fadeInDown'>Admin Created Successfully!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Management | Qranic Arabic Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            background: linear-gradient(135deg, #1a5e63 0%, #0d2c2f 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            padding-top: 100px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        /* ড্রপডাউন এবং ইনপুট ফিক্স - একদম পরিষ্কার দেখার জন্য */
        .form-group label {
            color: #00ffcc;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            background: #ffffff !important; /* সলিড সাদা ব্যাকগ্রাউন্ড যাতে লেখা বোঝা যায় */
            color: #000000 !important; /* কালো টেক্সট */
            border: 2px solid #1abc9c;
            border-radius: 10px;
            height: 48px;
            font-weight: 500;
        }

        /* Assign Role ড্রপডাউন বিশেষ স্টাইল */
        select.form-control {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 20px !important;
            padding-right: 40px !important;
            appearance: none;
        }

        select.form-control option {
            background: #ffffff;
            color: #000000;
        }

        .btn-smart {
            background: linear-gradient(45deg, #1abc9c, #16a085);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            height: 50px;
            margin-top: 20px;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-smart:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(26, 188, 156, 0.5);
        }

        /* টেবিল স্টাইল */
        .table { color: #fff; }
        .table thead th {
            color: #00ffcc;
            border-top: none;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        .table td {
            vertical-align: middle;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .admin-name { font-weight: 600; font-size: 1.1rem; color: #fff; }
        .admin-email { color: #1abc9c; font-size: 0.85rem; }

        .badge-role {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .badge-super { background: rgba(26, 188, 156, 0.2); color: #1abc9c; border: 1px solid #1abc9c; }
        .badge-editor { background: rgba(52, 152, 219, 0.2); color: #3498db; border: 1px solid #3498db; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <?php echo $message; ?>
    <div class="row">
        <div class="col-lg-5 mb-4 animate__animated animate__fadeInLeft">
            <div class="glass-card">
                <h4 class="mb-4" style="color:#00ffcc;"><i class="fas fa-user-plus mr-2"></i> Create Admin</h4>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="admin_name" class="form-control" placeholder="Enter Full Name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="admin_email" class="form-control" placeholder="admin@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="admin_pwd" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Assign Role</label>
                        <select name="admin_role" class="form-control" required>
                            <option value="" disabled selected>Click to Select Role</option>
                            <option value="1">Super Admin (Full Access)</option>
                            <option value="2">Editor (Logs Only)</option>
                        </select>
                    </div>
                    <button type="submit" name="add_admin" class="btn btn-smart btn-block">
                        <i class="fas fa-save mr-2"></i> Confirm & Create
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7 animate__animated animate__fadeInRight">
            <div class="glass-card">
                <h4 class="mb-4" style="color:#00ffcc;"><i class="fas fa-users-cog mr-2"></i> System Admins</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Administrator</th>
                                <th>Access Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM admin ORDER BY role ASC");
                            while($row = mysqli_fetch_assoc($res)) {
                                $role_name = ($row['role'] == 1) ? "Super Admin" : "Editor";
                                $badge = ($row['role'] == 1) ? "badge-super" : "badge-editor";
                                echo "<tr>
                                        <td>
                                            <div class='admin-name'>{$row['admin_name']}</div>
                                            <div class='admin-email'>{$row['admin_email']}</div>
                                        </td>
                                        <td><span class='badge-role $badge'>$role_name</span></td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>