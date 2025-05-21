<?php 
session_start();

if (!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'users_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'log_function.php';

// ✅ ดึง user_id จาก session ก่อนใช้งาน
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จาก session
$sql = "SELECT name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $name = $user_data['name'];
    $email = $user_data['email'];
    $role = $user_data['role'];
} else {
    $name = $email = $role = 'ไม่พบข้อมูล';
}

// รับค่าจากฟอร์ม
$product_id = $_POST['product_id'] ?? 'ไม่ระบุ';
$quantity = $_POST['quantity'] ?? 0;

// เก็บ log
$action = 'add_product'; // หรือ 'sell_product'
$description = "เพิ่มสินค้า: รหัส $product_id จำนวน $quantity";

write_log($conn, $user_id, $action, $description);
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin - Data Product</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    
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
            <li class="nav-item">
                <a class="nav-link" href="admin_page.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="charts.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item active">
                <a class="nav-link" href="DataProduct.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Product</span></a>
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
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                   <!-- Topbar Search -->
                   <!-- <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn custom-btn" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form> -->

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
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Logout
                            </a></li>
                        </ul>
                    </li>


                    </ul>

                </nav>
                <!-- End of Topbar -->
                 
       <!-- Modal -->
       <div class="modal fade" id="Profile" tabindex="-1" role="dialog" aria-labelledby="profileModalTitle" aria-hidden="true">
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



                <!-- Begin Page Content -->
                <div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Data Product</h1>

    <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>

            <div class="btn-group" role="group">
                <!-- ปุ่มสำหรับเพิ่มสินค้า -->
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" style="background-color: #16302b;">
    <i class="fas fa-box-open me-2"></i> Add products
</button>

                
                <!-- ปุ่มสำหรับขายสินค้า -->
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#sellProductModal" style="background-color: #16302b;">
  <i class="fas fa-cash-register me-2"></i> Sell Product
</button>

                <!-- ปุ่มเปิด Modal ลบสินค้า -->
<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
  <i class="fas fa-trash-alt"></i> ลบสินค้า
</button>

              
            </div>
        </div>
        <?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Modal: ลบสินค้า -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded">
      <form id="deleteForm" method="POST" action="delete_product.php" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')">
        <div class="modal-header text-white rounded-top" style="background-color: #8B0000;">
          <h5 class="modal-title" id="deleteProductModalTitle">
            <i class="fas fa-trash-alt mr-2"></i> ลบสินค้า
          </h5>
        </div>

        <div class="modal-body bg-light">
          <!-- รหัสสินค้า -->
          <div class="form-group">
            <label class="font-weight-bold" for="deleteProductId">รหัสสินค้า (Product ID)</label>
            <input type="number" class="form-control" name="product_id" id="deleteProductId" placeholder="กรอกรหัสสินค้าที่ต้องการลบ" required>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-danger">ลบสินค้า</button>
        </div>
      </form>
    </div>
  </div>
</div>


   
<!-- Modal สำหรับเพิ่มสินค้า -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded">
      <form id="productForm" method="POST" action="insert_product.php">
        <div class="modal-header text-white rounded-top" style="background-color: #16302b;">
          <h5 class="modal-title" id="exampleModalCenterTitle">
            <i class="fas fa-box-open mr-2"></i> เพิ่มสินค้า
          </h5>
        </div>

        <div class="modal-body bg-light">
          <!-- ชื่อสินค้า -->
          <div class="form-group">
            <label class="font-weight-bold" for="productName">ชื่อสินค้า</label>
            <input type="text" class="form-control" name="name" id="productName" placeholder="กรอกชื่อสินค้า" required>
          </div>

          <!-- รายละเอียดสินค้า -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="productDescription">รายละเอียดสินค้า</label>
            <textarea class="form-control" name="description" id="productDescription" rows="3" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
          </div>

          <!-- หมวดหมู่สินค้า -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="category">หมวดหมู่</label>
            <select name="category_id" class="form-control" required>
              <option value="">เลือกหมวดหมู่</option>
              <?php
              $sql = "SELECT id, name FROM categories";
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
              }
              ?>
            </select>
          </div>

          <!-- ราคา -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="productPrice">ราคา</label>
            <input type="number" class="form-control" name="price" id="productPrice" placeholder="0.00" required>
          </div>

          <!-- จำนวนในสต็อก -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="productStock">จำนวนในสต็อก</label>
            <input type="number" class="form-control" name="stock_quantity" id="productStock" value="0" min="0">
          </div>

          <!-- รูปภาพสินค้า -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="imageUrl">ลิงก์รูปภาพสินค้า</label>
            <input type="url" class="form-control" name="image_url" id="imageUrl" placeholder="https://example.com/image.jpg">
          </div>
        </div>

        <div class="modal-footer bg-light">
            
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-success">บันทึกสินค้า</button>
        </div>
      </form>
    </div>
  </div>
</div>



<div class="modal fade" id="sellProductModal" tabindex="-1" role="dialog" aria-labelledby="sellProductModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded">
      <form id="sellForm" method="POST" action="sell_product.php">
        <div class="modal-header text-white rounded-top" style="background-color: #16302b;">
          <h5 class="modal-title" id="sellProductModalTitle">
            <i class="fas fa-cash-register mr-2"></i> ขายสินค้า
          </h5>
        </div>

        <div class="modal-body bg-light">
          <!-- รหัสสินค้า -->
          <div class="form-group">
            <label class="font-weight-bold" for="productId">รหัสสินค้า (Product ID)</label>
            <input type="number" class="form-control" name="product_id" id="productId" placeholder="กรอกรหัสสินค้า" required>
          </div>

          <!-- จำนวนที่ขาย -->
          <div class="form-group mt-3">
            <label class="font-weight-bold" for="quantity">จำนวนที่ขาย</label>
            <input type="number" class="form-control" name="quantity" id="quantity" placeholder="จำนวนที่ต้องการขาย" required min="1">
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-success">ขายสินค้า</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#sellForm').on('submit', function (e) {
    e.preventDefault(); // หยุดไม่ให้ form ส่งแบบเดิม

    var formData = $(this).serialize();

    $.ajax({
        url: 'sell_product.php',
        type: 'POST',
        data: formData,
        success: function (response) {
            alert(response); // แสดงผลลัพธ์
            $('#sellProductModal').modal('hide'); // ปิด Modal
        },
        error: function () {
            alert("เกิดข้อผิดพลาด");
        }
    });
});
</script>



        <!-- Table -->
        <div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead class="table-dark">
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Date Added</th>
                </tr>
            </thead>

            <tbody>
                <?php
                // เชื่อมต่อกับฐานข้อมูล
                $conn = new mysqli('localhost', 'root', '', 'users_db');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // ดึงข้อมูลสินค้าและหมวดหมู่จากฐานข้อมูล
                $sql = "SELECT p.id, p.name, p.stock_quantity, p.price, p.description, p.created_at, c.name AS category_name
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.id";

                // สั่ง Query
                $result = $conn->query($sql);

                // ตรวจสอบว่ามีข้อมูลหรือไม่
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>"; // แสดงรหัสสินค้า
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>"; // แสดงชื่อสินค้า
                        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>"; // แสดงหมวดหมู่
                        echo "<td>" . htmlspecialchars($row['stock_quantity']) . "</td>"; // แสดงจำนวนในสต็อก
                        echo "<td>" . htmlspecialchars($row['price']) . " บาท</td>"; // แสดงราคา
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>"; // แสดงรายละเอียด
                        echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['created_at']))) . "</td>"; // แสดงวันที่สร้าง
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>ไม่มีข้อมูลสินค้า</td></tr>";
                }

                // ปิดการเชื่อมต่อ
                $conn->close();
                ?>
            </tbody>
        </table>

    </div>
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

                    <a class="btn btn-danger" href="index.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
 

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

<!-- การเพิ่่มข้อมูลสินค้าเข้าตาราง -->
<!-- การเพิ่่มข้อมูลสินค้าเข้าตาราง -->
<!-- การเพิ่่มข้อมูลสินค้าเข้าตาราง -->
<!-- ✅ Bootstrap 5 Bundle (รวม Popper แล้ว) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("productForm").addEventListener("submit", function (e) {
    e.preventDefault(); // ป้องกันการรีเฟรชหน้า

    const form = e.target;
    const formData = new FormData(form);

    fetch("insert_product.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text()) // หรือ .json() ถ้า insert_product.php ส่ง json กลับ
    .then(result => {
        console.log(result);

        // ปิด modal ด้วย Bootstrap 5
        const modal = bootstrap.Modal.getInstance(document.getElementById('exampleModalCenter'));
        modal.hide();

        // รีโหลดหน้าเพื่อให้ตารางรีเฟรชข้อมูลใหม่ (หรือจะใช้ AJAX โหลด table ใหม่ก็ได้)
        location.reload(); 
    })
    .catch(error => {
        console.error("เกิดข้อผิดพลาด:", error);
        alert("เกิดข้อผิดพลาดในการเพิ่มสินค้า");
    });
});
</script>

</body>

</html>