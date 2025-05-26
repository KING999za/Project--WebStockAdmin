<?php 
session_start();

// ตรวจสอบการ login ก่อนเข้าใช้งาน
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $conn->connect_error);
}

include 'log_function.php';

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

// จำนวนผู้ใช้ (ไม่รวม admin)
$stmt_users = $conn->prepare("SELECT COUNT(*) AS users FROM users WHERE role != 'admin'");
$stmt_users->execute();
$stmt_users->bind_result($users);
$stmt_users->fetch();
$stmt_users->close();

// ดึงจำนวนสินค้าทั้งหมด
$stmt_products = $conn->prepare("SELECT COUNT(*) AS products FROM products");
$stmt_products->execute();
$stmt_products->bind_result($products);
$stmt_products->fetch();
$stmt_products->close();

// ดึงสินค้าที่ใกล้หมด
$stmt_low_stock = $conn->prepare("SELECT name, stock_quantity FROM products WHERE stock_quantity < 20");
$stmt_low_stock->execute();
$stmt_low_stock->store_result();
$low_stock_count = $stmt_low_stock->num_rows;
$stmt_low_stock->close();

// ยอดขายรวม
$stmt_sales = $conn->prepare("SELECT SUM(quantity * price) AS total_sales FROM sales");
$stmt_sales->execute();
$stmt_sales->bind_result($total_sales);
$stmt_sales->fetch();
$stmt_sales->close();

// ดึงผู้ใช้ที่เป็น user
$sql = "SELECT id, name, email, created_at, role FROM users WHERE role = 'user'";
$result = $conn->query($sql);

// --- เพิ่มส่วนนี้สำหรับเช็ค POST ก่อนเขียน log ---
if (isset($_POST['product_id'], $_POST['quantity']) && !empty($_POST['product_id']) && $_POST['quantity'] > 0) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $action = 'add_product';
    $description = "เพิ่มสินค้า: รหัส $product_id จำนวน $quantity";
    write_log($conn, $user_id, $action, $description);
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ST Admin - Dashboard</title>

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
                    <!-- เปลี่ยนจาก <i> เป็น <img> -->
                    <img src="./images/logo.png" alt="Brand Icon" class="sidebar-icon-img">
                </div>
                <div class="sidebar-brand-text mx-3">Admin <sup>$</sup></div>
            </a>


            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin_page.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboards</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="DataProduct.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Product</span></a>
            </li>
         
            <li class="nav-item">
                <a class="nav-link" href="DataProduct.php">
                    <i class="fas fa-money-bill-wave"></i>
                    <span> Sales History</span></a>
            </li>

            <li class="nav-item">
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

            <!-- Sidebar Message -->
        

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
  <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profile
</a>

    </li>

    <li>
      <a class="dropdown-item" href="#">
        <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
        Settings
      </a>
    </li>

    <li>
      <a class="dropdown-item" href="#">
        <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
        Activity Log
      </a>
    </li>

    <li>
      <a class="dropdown-item" href="Download-Monthly-Report.php" target="_blank">
        <i class="fas fa-file-pdf fa-sm fa-fw me-2 text-gray-400"></i>
        Download Monthly Report
      </a>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
      <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
        Logout
      </a>
    </li>
  </ul>
</li>
</ul>
</nav>
<!-- Profile Modal -->
<div class="modal fade" id="Profile" tabindex="-1" aria-labelledby="profileModalTitle">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header text-white rounded-top" style="background-color: #16302b;">
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


                
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                     
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            All Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"> <?= $products ?></div>
                                        </div>
                                        <div class="col-auto">
                                        <i class="fa fa-archive fa-2x text-gray-700" aria-hidden="true"></i>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        The product is almost out of stock
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo $low_stock_count; ?>
                    </div>
                   
                </div>
                <div class="col-auto">
                    <i class="fa fa-exclamation-triangle fa-2x text-gray-700" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>


                                                
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                All Customers
                                            </div>
                                            
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $users ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa fa-user fa-2x text-gray-700" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                       
                        <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total sales</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_sales, 2) ?> </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2x text-gray-700"></i>
                </div>
            </div>
        </div>
    </div>
</div>



                    </div>

                    <div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Data Users</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">User Table</h6>
          
        </div>
        <div class="card-body">

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">    
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



                    <div class="row">
    <!-- Monthly Sales Chart -->
    <div class="col-12 mb-4 d-flex">
        <div class="card shadow w-100 d-flex flex-column">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Monthly sales</h6>
            </div>
            <div class="card-body flex-grow-1">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

                  

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('get_sales_chart.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Total (Baht)',
                        data: data.values,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 30000
                        }
                    }
                }
            });
        });
</script>

                </div>
                <!-- /.container-fluid -->

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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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

                    <a class="btn btn-danger" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
<!-- Load jQuery once -->
<script src="vendor/jquery/jquery.min.js"></script>

<!-- Load Bootstrap bundle once (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Load other plugins -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<!-- DataTables CSS and JS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });
</script>


</body>

</html>