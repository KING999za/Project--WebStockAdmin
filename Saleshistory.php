<?php
require_once "connect.php";

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
session_start();
require_once "connect.php";

// ดึง user_id จาก session
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จาก user_id
$stmt_user = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
    $name = $user_data['name'];
    $email = $user_data['email'];
    $role = $user_data['role'];
} else {
    $name = $email = $role = 'ไม่พบข้อมูล';
}
$stmt_user->close();

// ไม่มีการ JOIN users อีกแล้ว
$sql = "
  SELECT sales.*, products.name AS product_name
  FROM sales
  JOIN products ON sales.product_id = products.id
";

if (!empty($start) && !empty($end)) {
    $sql .= " WHERE sale_date BETWEEN '$start' AND '$end'";
}

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ST Admin - Sales History</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

      
    <style>
    .custom-btn {
        background-color: #16302b; /* สีพื้นหลังของปุ่ม */
        border-color: #16302b; /* สีขอบของปุ่ม */
        color: #ffffff; /* สีของไอคอน */
    }

    .custom-btn:hover {
        background-color: #14502b; /* สีพื้นหลังเมื่อ hover */
        border-color: #14502b; /* สีขอบเมื่อ hover */
    }

    .custom-btn i {
        color: #ffffff; /* สีของไอคอน */
    }
    .sidebar-icon-img {
    width: 50px; /* กำหนดความกว้างของไอคอน */
    height: auto; /* ให้ความสูงของไอคอนคงที่ตามสัดส่วน */
}
    </style>



</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #16302b;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_page.php">
                <div class="sidebar-brand-icon ">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img src="./images/logo.png" alt="Brand Icon" class="sidebar-icon-img">
                </div>
                <div class="sidebar-brand-text mx-3">Admin <sup>$</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="admin_page.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="DataProduct.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Product</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="Saleshistory.php">
                    <i class="fas fa-money-bill-wave"></i>
                    <span> Sales History</span></a>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="FAQ.php">
                <i class="fas fa-question-circle"></i>
                    <span>Frequently Asked (FAQ)</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

               

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-dark"><?= isset($_SESSION['name']) ? $_SESSION['name'] : 'User'; ?></span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <li>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#Profile">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            Profile
                             </a>
                            </li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                                Settings
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
                                Activity Log
                            </a></li>
                            <li>
                                <a class="dropdown-item" href="Download-Monthly-Report.php" target="_blank">
                                <i class="fas fa-file-pdf fa-sm fa-fw me-2 text-gray-400"></i>
                             Download Monthly Report
                            </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Logout
                            </a></li>
                        </ul>
                    </li>

               

                </nav>

                <div class="modal fade" id="Profile" tabindex="-1" role="dialog" aria-labelledby="profileModalTitle">


  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header  text-white rounded-top"style="background-color: #16302b;">
        <h5 class="modal-title" id="profileModalTitle">
          <i class="fas fa-user-circle mr-2"></i> Profile
        </h5>
       
      </div>

      <div class="modal-body bg-light">
        <div class="form-group">
          <label class="font-weight-bold">User ID</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($user_id) ?>" readonly>
        </div>

        <div class="form-group mt-3">
          <label class="font-weight-bold">User name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($name) ?>" readonly>
        </div>

        <div class="form-group mt-3">
          <label class="font-weight-bold">Email</label>
          <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
        </div>

        <div class="form-group mt-3">
          <label class="font-weight-bold">Role</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($role) ?>" readonly>
        </div>
      </div>

      <div class="modal-footer bg-light">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

                
                <!-- End of Topbar -->
                <div class="container my-5">
  <div class="bg-white p-5 rounded-4 shadow-lg">
    <h2 class="mb-4 text-primary">
      <i class="fas fa-history me-2"></i> Sales History
    </h2>

<!-- Filter Form -->
<!-- Filter Form -->
<form method="get" class="row g-3 align-items-end mb-4">
  <div class="col-md-4">
    <label for="start" class="form-label fw-semibold">Start Date</label>
    <input 
      type="text" 
      id="start" 
      name="start" 
      class="form-control" 
      placeholder="Select start date" 
      value="<?= htmlspecialchars($start) ?>"
    >
  </div>

  <div class="col-md-4">
    <label for="end" class="form-label fw-semibold">End Date</label>
    <input 
      type="text" 
      id="end" 
      name="end" 
      class="form-control" 
      placeholder="Select end date" 
      value="<?= htmlspecialchars($end) ?>"
    >
  </div>

  <div class="col-md-4 d-grid">
    <button type="submit" class="btn btn-primary mt-2 mb-2">
      <i class="fas fa-search me-1"></i> Search
    </button>
    <button type="button" id="clearDates" class="btn btn-outline-secondary">
      <i class="fas fa-eraser me-1"></i> Clear Dates
    </button>
  </div>
</form>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Initialize Flatpickr -->
<script>
  const startPicker = flatpickr("#start", {
    dateFormat: "Y-m-d",
    locale: "en"
  });

  const endPicker = flatpickr("#end", {
    dateFormat: "Y-m-d",
    locale: "en"
  });

  // Clear button functionality
  document.getElementById("clearDates").addEventListener("click", function () {
    startPicker.clear();
    endPicker.clear();
  });
</script>


    <!-- Sales Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>ID Product</th>
            <th>Quality</th>
            <th>Price</th>
            <th>Total Price</th>
            <th>Sale Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td class="text-start"><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['product_id']) ?></td>
                <td><?= (int)$row['quantity'] ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td class="text-success fw-bold"><?= number_format($row['quantity'] * $row['price'], 2) ?></td>
                <td><?= htmlspecialchars($row['sale_date']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">No data found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>




            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                    <span>STOCK &copy; Website By Patipan 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                 
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>

                    <a class="btn btn-danger" href="index.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    
</body>

</html>