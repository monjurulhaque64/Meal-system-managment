<?php
session_start();
require 'connectDB.php';
date_default_timezone_set('Asia/Dhaka');

// ১. ফিল্টার প্রসেসিং (AJAX থেকে আসা ডাটা অনুযায়ী)
if (isset($_POST['log_date'])) {
    $searchQuery = " 1=1 ";
    if (!empty($_POST['date_sel_start'])) {
        $start_date = $_POST['date_sel_start'];
        if (!empty($_POST['date_sel_end'])) {
            $end_date = $_POST['date_sel_end'];
            $searchQuery .= " AND checkindate BETWEEN '$start_date' AND '$end_date'";
        } else {
            $searchQuery .= " AND checkindate = '$start_date'";
        }
    }
    if (!empty($_POST['card_sel']) && $_POST['card_sel'] != "0") {
        $card_uid = mysqli_real_escape_string($conn, $_POST['card_sel']);
        $searchQuery .= " AND card_uid = '$card_uid'";
    }
    if (!empty($_POST['dev_uid']) && $_POST['dev_uid'] != "0") {
        $dev_uid = mysqli_real_escape_string($conn, $_POST['dev_uid']);
        $searchQuery .= " AND device_uid = '$dev_uid'";
    }
    if (!empty($_POST['time_sel_start']) && !empty($_POST['time_sel_end'])) {
        $t_start = $_POST['time_sel_start'];
        $t_end = $_POST['time_sel_end'];
        $time_type = $_POST['time_sel'];
        if ($time_type == "Time_in") {
            $searchQuery .= " AND breakfast BETWEEN '$t_start' AND '$t_end'";
        } else {
            $searchQuery .= " AND dinner BETWEEN '$t_start' AND '$t_end'";
        }
    }
    $_SESSION['searchQuery'] = $searchQuery;
}

if (isset($_POST['select_date']) && $_POST['select_date'] == 1) {
    $today = date("Y-m-d");
    $_SESSION['searchQuery'] = " checkindate = '$today' ";
}

if (!isset($_SESSION['searchQuery'])) {
    $_SESSION['searchQuery'] = " 1=1 ";
}

$sql = "SELECT * FROM users_logs WHERE " . $_SESSION['searchQuery'] . " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<style>
    #mealTable tbody tr:hover {
        background-color: #708090 !important; /* Gray hover effect */
        transition: 0.3s;
    }
    .table-secondary td { color: #333 !important; vertical-align: middle; }
</style>

<div class="table-responsive" style="max-height: 500px;"> 
  <table class="table table-hover text-center align-middle" id="mealTable">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Roll</th>
        <th>Date</th>
        <th>Breakfast</th>
        <th>Lunch</th>
        <th>Dinner</th>
        <?php if (isset($_SESSION['Admin-role']) && $_SESSION['Admin-role'] == 1): ?>
            <th>Action</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody class="table-secondary">
      <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <TR>
                  <TD><?php echo $row['id'];?></TD>
                  <TD><strong><?php echo $row['username'];?></strong></TD>
                  <TD>
    <div  style="color: #FFFFFF; ">
        #<?php echo $row['serialnumber']; ?>
    </div>
</TD>

<TD>
    <div style=" color: #FFFFFF;">
        <i class="fa fa-calendar-o  me-1" style="font-size: 12px;"></i>
        <?php echo date("d M, Y", strtotime($row['checkindate'])); ?>
    </div>
</TD>
                  
                  <TD>
                    <?php echo (!empty($row['breakfast']) && $row['breakfast'] != "00:00:00") 
                        ? '<span class="badge bg-warning text-dark">'.date("h:i A", strtotime($row['breakfast'])).'</span>' 
                        : '<span class="badge border " style="border-style: dashed !important; font-weight: normal;">-- : --</span>'; ?>
                  </TD>

                  <TD>
                    <?php echo (!empty($row['lunch']) && $row['lunch'] != "00:00:00") 
                        ? '<span class="badge bg-success text-white">'.date("h:i A", strtotime($row['lunch'])).'</span>' 
                        : '<span class="badge border " style="border-style: dashed !important; font-weight: normal;">-- : --</span>'; ?>
                  </TD>

                  <TD>
                    <?php echo (!empty($row['dinner']) && $row['dinner'] != "00:00:00") 
                        ? '<span class="badge bg-primary text-white">'.date("h:i A", strtotime($row['dinner'])).'</span>' 
                        : '<span class="badge border " style="border-style: dashed !important; font-weight: normal;">-- : --</span>'; ?>
                  </TD>
                  
                  <?php if (isset($_SESSION['Admin-role']) && $_SESSION['Admin-role'] == 1): ?>
                  <TD>
                    <a href="edit_log.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                  </TD>
                  <?php endif; ?>
                </TR>
                <?php
            }
        } else {
            $colspan = (isset($_SESSION['Admin-role']) && $_SESSION['Admin-role'] == 1) ? 8 : 7;
            echo "<tr><td colspan='$colspan' class='py-4'>কোনো ডাটা পাওয়া যায়নি।</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>