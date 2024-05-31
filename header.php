        <!-- Top Navbar -->

        <!-- <nav class="navbar navbar-expand-xl navbar-light fixed-top hk-navbar">
            <a id="navbar_toggle_btn" class="navbar-toggle-btn nav-link-hover" href="javascript:void(0);"><span class="feather-icon"><i data-feather="menu"></i></span></a>
            <a class="navbar-brand" href="#">
                <img class="brand-img d-inline-block" src="" width="150px" alt="brand" />
            </a>
            <ul class="navbar-nav hk-navbar-content">
                <li class="nav-item">
                    <a id="navbar_search_btn" class="nav-link nav-link-hover" href="javascript:void(0);"><span class="feather-icon"><i data-feather="search"></i></span></a>
                </li>
                <li class="nav-item">
                    <a id="settings_toggle_btn" class="nav-link nav-link-hover" href="javascript:void(0);"><span class="feather-icon"><i data-feather="settings"></i></span></a>
                </li>
                <li class="nav-item dropdown dropdown-notifications">
                    <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="feather-icon"><i data-feather="bell"></i></span><span class="badge-wrap"><span class="badge badge-primary badge-indicator badge-indicator-sm badge-pill pulse"></span></span></a>
                    <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                        <h6 class="dropdown-header">Notifications <a href="javascript:void(0);" class="">View all</a></h6>
                        <div class="notifications-nicescroll-bar">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="media">
                                    <div class="media-img-wrap">
                                        <div class="avatar avatar-sm">
                                            <img src="dist/img/avatar1.jpg" alt="user" class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="notifications-text"><span class="text-dark text-capitalize">Evie Ono</span> accepted your invitation to join the team</div>
                                            <div class="notifications-time">12m</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="media">
                                    <div class="media-img-wrap">
                                        <div class="avatar avatar-sm">
                                            <img src="dist/img/avatar2.jpg" alt="user" class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="notifications-text">New message received from <span class="text-dark text-capitalize">Misuko Heid</span></div>
                                            <div class="notifications-time">1h</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="media">
                                    <div class="media-img-wrap">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-primary rounded-circle">
                                                <span class="initial-wrap"><span><i class="zmdi zmdi-account font-18"></i></span></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="notifications-text">You have a follow up with<span class="text-dark text-capitalize"> Mintos head</span> on <span class="text-dark text-capitalize">friday, dec 19</span> at <span class="text-dark">10.00 am</span></div>
                                            <div class="notifications-time">2d</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="media">
                                    <div class="media-img-wrap">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-success rounded-circle">
                                                <span class="initial-wrap"><span>A</span></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="notifications-text">Application of <span class="text-dark text-capitalize">Sarah Williams</span> is waiting for your approval</div>
                                            <div class="notifications-time">1w</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <div class="media">
                                    <div class="media-img-wrap">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-text avatar-text-warning rounded-circle">
                                                <span class="initial-wrap"><span><i class="zmdi zmdi-notifications font-18"></i></span></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="notifications-text">Last 2 days left for the project</div>
                                            <div class="notifications-time">15d</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown dropdown-authentication">
                    <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <div class="media-img-wrap">
                                <div class="avatar">
                                    <img src="dist/img/avatar12.jpg" alt="user" class="avatar-img rounded-circle">
                                </div>
                                <span class="badge badge-success badge-indicator"></span>
                            </div>
                            <div class="media-body">
                                <span><?php echo $_SESSION['nm_user']; ?><i class="zmdi zmdi-chevron-down"></i></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                        <a class="dropdown-item" href="profile.html"><i class="dropdown-icon zmdi zmdi-account"></i><span>Profile</span></a>
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-card"></i><span>My balance</span></a>
                        <a class="dropdown-item" href="inbox.html"><i class="dropdown-icon zmdi zmdi-email"></i><span>Inbox</span></a>
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-settings"></i><span>Settings</span></a>
                        <div class="dropdown-divider"></div>
                        <div class="sub-dropdown-menu show-on-hover">
                            <a href="#" class="dropdown-toggle dropdown-item no-caret"><i class="zmdi zmdi-check text-success"></i>Online</a>
                            <div class="dropdown-menu open-left-side">
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-check text-success"></i><span>Online</span></a>
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-circle-o text-warning"></i><span>Busy</span></a>
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-minus-circle-outline text-danger"></i><span>Offline</span></a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-power"></i><span>Log out</span></a>
                    </div>
                </li>
            </ul>
        </nav> -->
        <form role="search" class="navbar-search">
            <div class="position-relative">
                <a href="javascript:void(0);" class="navbar-search-icon"><span class="feather-icon"><i data-feather="search"></i></span></a>
                <input type="text" name="example-input1-group2" class="form-control" placeholder="Type here to Search">
                <a id="navbar_search_close" class="navbar-search-close" href="#"><span class="feather-icon"><i data-feather="x"></i></span></a>
            </div>
        </form>
        <!-- /Top Navbar -->

        <!-- Vertical Nav -->
        <nav class="hk-nav hk-nav-light">
            <a href="javascript:void(0);" id="hk_nav_close" class="hk-nav-close"><span class="feather-icon"><i data-feather="x"></i></span></a>
            <div class="nicescroll-bar">
                <div class="navbar-nav-wrap">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item active">
                            <a class="nav-link" href="dashboard.php" data-target="#dash_drp">
                                <span class="feather-icon"><i data-feather="activity"></i></span>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                    <hr class="nav-separator">
                    <div class="nav-header">
                        <span style="color:black;">Inventory System</span>
                        <span style="color:black;">IS</span>
                    </div>
                    <ul class="navbar-nav flex-column">
                        <?php
                        $query_get_menu = "SELECT mnn.menu_id,
                        mnn.parent,
                        mnn.nm_menu,
                        mnn.link,
                        mnn.status
                    FROM m_menu mnn 
                    join m_akses aa on aa.id_menu = mnn.menu_id
                where mnn.parent = '0' 
                and mnn.status = 'Y'
                and aa.id_user = '" . $_SESSION['id_user'] . "' 
                and aa.status = '1'
                ORDER BY no_urut asc ";
                        $sql_get_menu = mysqli_query($conn, $query_get_menu);
                        while ($row_menu = mysqli_fetch_array($sql_get_menu)) {
                            if ($row_menu['link'] == "#") {

                                echo '
                            <li class="nav-item active">
                                <a class="nav-link" href="javascript:void(0);" data-toggle="collapse" data-target="#x' . $row_menu['menu_id'] . '">
                                    <span class="feather-icon"><i data-feather="home"></i></span>
                                    <span class="nav-link-text"><strong>' . $row_menu['nm_menu'] . '</strong></span>
                                </a>
                                <ul id="x' . $row_menu['menu_id'] . '" class="nav flex-column collapse collapse-level-1">
                                    <li class="nav-item active">
                                        <ul class="nav flex-column">
                                            ';
                                $query_get_menu_body = "SELECT mn.menu_id,
                                                                            mn.parent,
                                                                            mn.nm_menu,
                                                                            mn.link,
                                                                            mn.status,
                                                                            ak.id_user,
                                                                            ak.status
                                                                    FROM m_menu mn JOIN m_akses ak ON mn.menu_id = ak.id_menu
                                                                    where mn.parent = '" . $row_menu['menu_id'] . "' AND mn.status = 'Y'  AND ak.id_user = '" . $_SESSION['id_user'] . "' AND ak.status = '1'
                                                                    ORDER BY no_urut asc ";
                                $sql_get_menu_body = mysqli_query($conn, $query_get_menu_body);
                                while ($row_menu_body = mysqli_fetch_array($sql_get_menu_body)) {


                                    echo '
                                                        <li class="nav-item active">
                                                            <a class="nav-link" href="' . $row_menu_body['link'] . '">' . $row_menu_body['nm_menu'] . '</a>
                                                        </li>
                                                    ';
                                }
                                echo '
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            ';
                            } else {
                                echo '
                                    <li class="nav-item active">
                                        <a class="nav-link" href="' . $row_menu['link'] . '"><span class="feather-icon"><i data-feather="home"></i></span>
                                        <span class="nav-link-text"><strong>' . $row_menu['nm_menu'] . '</strong></span></a>
                                    </li>
                                ';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
        <!-- /Vertical Nav -->

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-xl navbar-light fixed-top hk-navbar">
            <a id="navbar_toggle_btn" class="navbar-toggle-btn nav-link-hover" href="javascript:void(0);"><span class="feather-icon"><i data-feather="menu"></i></span></a>
            <a class="navbar-brand" href="#">
                <img class="brand-img d-inline-block" src="logo_inventory-removebg-preview (1).png" width="150px" alt="brand" />
            </a>
            <ul class="navbar-nav hk-navbar-content">

                <li class="nav-item dropdown dropdown-authentication">
                    <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <div class="media-img-wrap">
                                <div class="avatar">
                                    <img src="logo_inventory-removebg-preview (1).png" width="24px" alt="user" class="avatar-img rounded-circle">
                                </div>
                                <span class="badge badge-success badge-indicator"></span>
                            </div>
                            <div class="media-body">
                                <span><?php echo $_SESSION['nm_user']; ?><i class="zmdi zmdi-chevron-down"></i></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-account"></i><span>Profile</span></a>
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-email"></i><span>Inbox</span></a>
                        <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-settings"></i><span>Change Password</span></a>

                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#ReportModal"><i class="dropdown-icon zmdi zmdi-alert-circle"></i><span>Bugs Report</span></a>

                        <div class="dropdown-divider"></div>
                        <div class="sub-dropdown-menu show-on-hover">
                            <a href="#" class="dropdown-toggle dropdown-item no-caret"><i class="zmdi zmdi-check text-success"></i>Online</a>
                            <div class="dropdown-menu open-left-side">
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-check text-success"></i><span>Online</span></a>
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-circle-o text-warning"></i><span>Busy</span></a>
                                <a class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-minus-circle-outline text-danger"></i><span>Offline</span></a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php"><i class="dropdown-icon zmdi zmdi-power"></i><span>Log out</span></a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- <form role="search" class="navbar-search">
            <div class="position-relative">
                <a href="javascript:void(0);" class="navbar-search-icon"><span class="feather-icon"><i data-feather="search"></i></span></a>
                <input type="text" name="example-input1-group2" class="form-control" placeholder="Type here to Search">
                <a id="navbar_search_close" class="navbar-search-close" href="#"><span class="feather-icon"><i data-feather="x"></i></span></a>
            </div>
        </form> -->
        <!-- /Top Navbar -->

        <?php
        if (isset($_POST['report'])) {
            // Kode Otomatis
            $query = mysqli_query($conn, "SELECT max(id_report) as kodeTerbesar FROM tb_report");
            $data = mysqli_fetch_array($query);
            $kodeBarang = $data['kodeTerbesar'];
            $urutan = (int) substr($kodeBarang, 8, 6);
            $urutan++;
            $slip = "-";
            $tgl = date('Y-m');
            $kodereport = $tgl . $slip . sprintf("%06s", $urutan);
            // End 

            $path2 = "";
            if ($_FILES['gambar']['name'] !== "") {
                $ekstensi_diperbolehkan = array('png', 'jpg', 'bmp', 'jpeg', 'gif', 'PNG', 'JPG', 'BMP', 'JPEG', 'GIF');
                $gambar = $_FILES['gambar']['name'];
                $x = explode('.', $gambar);
                $ekstensi = strtolower(end($x));
                $ukuran = $_FILES['gambar']['size'];
                $file_tmp = $_FILES['gambar']['tmp_name'];

                if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                    if ($ukuran < 2044070) {
                        move_uploaded_file($file_tmp, 'img_report/' . $gambar);
                        $path = "img_report/" . $gambar;
                    } else {
                        echo "<script type='text/javascript'>alert('UKURAN FILE TERLALU BESAR, MAX 2 MB')</script>";
                    }
                } else {
                    if ($jml == 0) {
                        echo "<script type='text/javascript'>alert('EKSTENSI FILE YANG DI UPLOAD TIDAK DI PERBOLEHKAN')</script>";
                    }
                }

                $path2 = $path;
            }

            // Add m_report
            $query_set = mysqli_query($conn, "INSERT INTO tb_report(
                                                            id_report,
                                                            img_report,
                                                            ket_report,
                                                            dibuat_tgl,
                                                            dibuat_oleh
                                                            )
                                                    VALUES (
                                                            '" . $kodereport . "',
                                                            '" . $path2 . "',
                                                            '" . mysqli_real_escape_string($conn, $_POST['ket_report']) . "',
                                                            '" . date('Y-m-d h:i:s') . "',
                                                            '" . $_SESSION['nm_user'] . "'
                                                            )
                        ");
            echo "<script type='text/javascript'>alert('Report Bugs Success')</script>";
        }
        ?>

        <!-- Modal -->
        <div class="modal fade" id="ReportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalReport" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalReport"><i class="dropdown-icon zmdi zmdi-alert-circle"></i> Bugs Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" style="padding-right:5px !important">
                                    <div class="input-group input-group-sm">
                                        <input type="file" value="" name="gambar">
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span style="width:100px;" class="form-control filled-input form-control-sm mt-15 input-group-text" id="inputGroup-sizing-sm">Deskripsi</span>
                                        </div>
                                        <textarea name="ket_report" id="" cols="30" rows="10" class="form-control form-control mt-15"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger" name="report">Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>