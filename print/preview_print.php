<?php
  session_start();
  include "../lib/koneksi.php";
  include "../lib/appcode.php";
  include "../lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:index.php'); 
    }

    $id_sppa = mysqli_real_escape_string($conn,$_GET['id_sppa']);
    $id_req = mysqli_real_escape_string($conn,$_GET['id_ins']);
    $id_user = $_SESSION['id_user'];

?>

<!DOCTYPE html>
<!-- 
Template Name: Griffin - Responsive Bootstrap 4 Admin Dashboard Template
Author: Hencework
Support: support@hencework.com

License: You must have a valid license purchased only from templatemonster to legally use the template for your project.
-->
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Request Placing</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />


    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body class="bg-white">


    <!-- HK Wrapper -->
        <!-- Main Content -->

            <!-- Container -->
            <div class="container mt-sm-30 py-4 px-30">
                <div class="border border-2 border-dark text-center text-dark">
                    <span><strong><u>
                                <h1>PLACING SLIP</h1>
                            </u></strong></span>
                    <span><strong>
                            <h2>PLC No. 00488/PL.14/02/20</h2>
                        </strong></span>
                </div>
                <br>
                <span>
                    <h5> Herewith, we would like to submit our proposal on the following coverage. Please sign and
                        return as your confirmation if you agreed with our proposal.</h5>
                </span>
                <table id="datable_12" class="tb_laba_jps  text-dark table-sm mt-sm-20">
                    <?php
                    $no = 0;
                    $query_get_sppa = mysqli_query($conn,"SELECT * FROM tb_sppa WHERE id_sppa = '".$id_sppa."' ");
                    if($row_sppa = mysqli_fetch_array($query_get_sppa)){
                        $id_sppa = $row_sppa['id_sppa'];
                        $kasih_client = $row_sppa['kasih_client'];
                        $insurance_type = $row_sppa['ins_type'];
                    }

                    $query_get_rumah = mysqli_query($conn,"SELECT id_rumah FROM tb_rumah WHERE id_sppa = '".$id_sppa."' ");
                    $no = mysqli_num_rows($query_get_rumah);
                    echo'
                    
                    <tr>
                        <td width="20%">TYPE OF COVER</td>
                        <td class="text-center">:</td>
                        <td>';
                            $flexas = "";
                            $tfswd = "";
                            $eqvet = "";
                            $hide_eqvet = "hidden";
                            $query_get_toc = mysqli_query($conn,"SELECT flexas,tfswd,eqvet FROM tb_rumah WHERE id_sppa = '".$id_sppa."' ");
                            while($row_toc = mysqli_fetch_array($query_get_toc)){
                                if($row_toc['flexas'] == "FLEXAS"){
                                    $flexas = "<li>FLEXAS</li>";
                                }
                                if($row_toc['tfswd'] == "TFSWD"){
                                    $tfswd = "<li>TFSWD</li>";
                                }
                                if($row_toc['eqvet'] == "EQVET"){
                                    $eqvet = "<li>EQVET</li>";

                                    $hide_eqvet = "";
                                }
                            }
                        echo'
                            <ul class="list-ul">
                                <li>PAR/IAR INSURANCE, include RSMDCC and TFSWD</li>
                                '.$flexas.'
                                '.$tfswd.'
                                '.$eqvet.'
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td>Propose Backup</td>
                        <td class="text-center">:</td>
                        <td>
                        ';
                        $propose = "";
                        $query_get_propose = mysqli_query($conn,"SELECT open_share FROM tb_placing_req WHERE id_sppa = '".$id_sppa."' ");
                        while($row_propose = mysqli_fetch_array($query_get_propose)){
                            
                            if($row_propose['open_share'] !== "100%" || $row_propose == "Open Share"){
                                $propose = "Share";
                            }else{
                                $propose = "Full";
                            }
                        }
                        echo'
                        '.$propose.'
                        </td>
                    </tr>

                    <tr>
                        <td>FORM</td>
                        <td class="text-center">:</td>
                        <td>
                        <ul class="list-ul">
                        <li>
                            Munich Re Standard Wording (kalau memilih property all risk, ada lagi kalau fire only muncul PSAKI standard wording) amended with
                            <ul class="list-ul">
                                <li>
                                    RSMDCC :  Riots, Strike, Malicious Damage and Civil Commotion 4.1B Endorsement/2007 
                                </li>
                                <li>
                                    TFSWD : wordingnya apa ?
                                </li>
                            </ul>
                            Munich tidak cover RSMDCC </li>
                
                    <li '.$hide_eqvet.'>Indonesian Standard Earthquake Policy (latest version)</li>
                        </td>
                        <ul>
                    </tr>

                    <tr>
                        <td>THE INSURED</td>
                        <td class="text-center">:</td>
                        <td>
                            ';
                            $id_cust = "";
                            $query_get_sppa = mysqli_query($conn,"SELECT id_cust FROM tb_sppa WHERE id_sppa = '".$id_sppa."' ");
                            if($row_sppa = mysqli_fetch_array($query_get_sppa)){
                                $str= $row_sppa['id_cust'];
                                $explode = explode(" | ",$str);
                                $id_cust = $explode[1];
                            }
                            
                            $query_get_cust = mysqli_query($conn,"SELECT c.nm_cust,
                                                                  g.nm_group 
                                                            FROM  m_cust c 
                                                            LEFT JOIN m_group_cust g ON g.id_group = c.id_group
                                                            WHERE c.id_cust = '".$id_cust."' ");
                            if($row_cust = mysqli_fetch_array($query_get_cust)) {
                                echo'
                                <ol class="list-ol">
                                    <li>'.$row_cust['nm_group'].' and/or </li>
                                    <li>'.$row_cust['nm_cust'].'  And/or subsidiary and/or affiliated and/or inter-related companies for their respective rights and interests</li>
                                </ol>
                                ';
                            }
                        echo'
                        </td>
                    </tr>

                    <tr>
                        <td>ADDRESS</td>
                        <td class="text-center">:</td>
                        <td>';
                            $query_get_address = mysqli_query($conn,"SELECT address 
                                                              FROM m_cust c 
                                                              LEFT JOIN m_group_cust g ON g.id_group = c.id_group 
                                                              WHERE c.nm_cust like '%".$row_cust['nm_group']."%'");
                            if($row_address=mysqli_fetch_array($query_get_address)){
                                echo $row_address['address'];
                            }
                        echo'
                        </td>
                    </tr>

                    <tr>
                        <td>INSURANCE PERIOD</td>
                        <td class="text-center">:</td>
                        <td>';
                            $query_get_period = mysqli_query($conn,"SELECT mulai_idemnity,
                                                                    selesai_idemnity 
                                                              FROM tb_rumah  
                                                              WHERE id_sppa = '".$id_sppa."' ");
                            if($row_period=mysqli_fetch_array($query_get_period)){
                                $awal = date('F d,Y', strtotime($row_period['mulai_idemnity']));
                                $akhir = date('F d,Y', strtotime($row_period['selesai_idemnity']));
                                echo $awal.' to '.$akhir;
                            }
                        echo'
                        </td>
                    </tr>

                    <tr>
                        <td>OCCUPANCY</td>
                        <td class="text-center">:</td>
                        <td>
                        All operations of the Insured, including but not limited to ';
                            $query_get_okupasi = mysqli_query($conn,"SELECT r.kode_okupasi,
                                                                     f3.level3_desc,
                                                                     f2.level2_desc
                                                              FROM tb_rumah r 
                                                              LEFT JOIN m_flexas3 f3 ON f3.level3_code = r.kode_okupasi
                                                              LEFT JOIN m_flexas2 f2 ON f3.id_flexas2 = f2.id_flexas2
                                                              WHERE r.id_sppa = '".$id_sppa."' ");
                            while($row_okupasi=mysqli_fetch_array($query_get_okupasi)){
                                $awal = $row_okupasi['level3_desc'].' ( code : '.$row_okupasi['kode_okupasi'].')';
                                echo '<strong>'.$awal.'</strong>';
                            }
                            echo'
                            and all other activities and any other occupation related to the Insured’s nature of business
                        </td>
                    </tr>

                    <tr>
                        <td>RISK LOCATION </td>
                        <td class="text-center">:</td>
                        <td>
                        ';
                        if($no > 1){
                            echo'
                            Details As per list attached 
                            ';
                        }else{
                            $query_get_location = mysqli_query($conn,"SELECT alamat,country,provinsi,kota,kec,kel FROM tb_rumah WHERE id_sppa = '".$id_sppa."' ");
                            if($row_location = mysqli_fetch_array($query_get_location)){
                                echo $row_location['alamat'].', Kel.'.$row_location['kel'].', Kec.'.$row_location['kec'].', '.$row_location['kota'].', '.$row_location['provinsi'].', '.$row_location['country'];
                            }
                        }
                        echo'
                        </td>
                    </tr>

                    <tr>
                        <td>COVERING</td>
                        <td class="text-center">:</td>
                        <td>All risk of Physical Loss or Damage to the Insured’s Property from any cause (Subject to the
                            Policy exclusion).</td>
                    </tr>

                    <tr>
                        <td>INTEREST INSURED</td>
                        <td class="text-center">:</td>
                        <td><div class="row no-gutters">';
                        $query_main_exclusions = mysqli_query($conn,"SELECT desc_placing,sort FROM placing_slip_sppa WHERE p_sppa_id = '".$id_sppa."' and title = 'INTEREST INSURED' ORDER BY sort ASC");
                        while($row_main_exclusions = mysqli_fetch_array($query_main_exclusions)){
                            $desc = $row_main_exclusions['desc_placing'];
                            $sort = $row_main_exclusions['sort'];
                            echo '<div class="col-md-1 text-center">'.$sort.'.</div><div class="col-md-11"> '.$desc.'</div>';
                        }
                        echo'
                            </div></td>
                    </tr>

                    <tr>
                        <td>TOTAL SUM INSURED</td>
                        <td class="text-center">:</td>
                        <td> 
                        ';   
                                $building = "";
                                $machinery = "";
                                $stock = "";
                                $content = "";
                            $query_get_sum_insured = mysqli_query($conn,"SELECT building,machinery,stok,content FROM tb_rumah WHERE id_sppa = '".$id_sppa."' ");
                            if($row_insured = mysqli_fetch_array($query_get_sum_insured)){
                            
                                $building = 'BUILDING : '.money_idr($row_insured['building']);
                                $machinery = 'MACHINERY : '.money_idr($row_insured['machinery']);
                                $stock = 'CONTENT : '.money_idr($row_insured['stok']);
                                $content = 'STOCK : '.money_idr($row_insured['content']);
                            }   

                            if($no > 1){
                                $building = "BUILDING    	(Detail as per list attached)";
                                $machinery = "MACHINERY & EQUIPMENT    	(Detail as per list attached)";
                                $stock = "CONTENT   	(Detail as per list attached)";
                                $content = "STOCK   	(Detail as per list attached)";
                            }
                        echo'
                        <ul>
                        <li>A. '.$building.'</li>
                        <li>B. '.$machinery.'</li>
                        <li>C. '.$content.'</li>
                        <li>D. '.$stock.'</li>
                        </ul>
                        </td>
                    </tr>

                    <tr>
                        <td>MAIN EXCLUSIONS</td>
                        <td class="text-center">:</td>
                        <td>
                        <div class="row no-gutters">
                        ';
                        $query_main_exclusions = mysqli_query($conn,"SELECT desc_placing,sort FROM placing_slip_sppa WHERE p_sppa_id = '".$id_sppa."' and title = 'MAIN EXCLUSIONS' ORDER BY sort ASC");
                        while($row_main_exclusions = mysqli_fetch_array($query_main_exclusions)){
                            $desc = $row_main_exclusions['desc_placing'];
                            $sort = $row_main_exclusions['sort'];
                            echo '<div class="col-md-1 text-center">'.$sort.'.</div><div class="col-md-11"> '.$desc.'</div>';
                        }
                        
                        echo'
                    </div>
                        </td>
                    </tr>

                    <tr>
                        <td>PROPERTY EXCLUDED</td>
                        <td class="text-center">:</td>
                        <td>
                        <div class="row no-gutters">';
                        $query_main_exclusions = mysqli_query($conn,"SELECT desc_placing,sort FROM placing_slip_sppa WHERE p_sppa_id = '".$id_sppa."' and title = 'PROPERTY EXCLUDED' ORDER BY sort ASC");
                        while($row_main_exclusions = mysqli_fetch_array($query_main_exclusions)){
                            $desc = $row_main_exclusions['desc_placing'];
                            $sort = $row_main_exclusions['sort'];
                            echo '<div class="col-md-1 text-center">'.$sort.'.</div><div class="col-md-11"> '.$desc.'</div>';
                        }
                    echo'
                    </div>
                        </td>
                    </tr>

                    <tr>
                        <td>DEDUCTIBLE
                            (any one accident for any one risk location) </td>
                        <td class="text-center">:</td>
                        <td><div class="row no-gutters">
                        ';
                        $query_main_exclusions = mysqli_query($conn,"SELECT desc_placing,sort FROM placing_slip_sppa WHERE p_sppa_id = '".$id_sppa."' and title = 'DEDUCTIBLE' ORDER BY sort ASC");
                        while($row_main_exclusions = mysqli_fetch_array($query_main_exclusions)){
                            $desc = $row_main_exclusions['desc_placing'];
                            $sort = $row_main_exclusions['sort'];
                            echo '<div class="col-md-1 text-center">'.$sort.'.</div><div class="col-md-11"> '.$desc.'</div>';
                        }
                        echo'
                        </div>
                        </td>
                    </tr>

                    <tr>
                        <td>CONDITIONS
                            (applicable for any one location)</td>
                        <td class="text-center">:</td>
                        <td>
                            <ul class="list-ul">
                                <li> Basis of Loss Settlement – Reinstatement Value </li>
                                <li> First Loss Insurance - All Other Contents Clause (Limit IDR 100,000,000.00 per item and IDR. 500.000.000,-  in the aggregate)` bisa diedit/ delete </li>
                                <li> Debris Removal Clause (10% of TSI) </li>
                                <li> Capital Addition Clause (10% of TSI) </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td>SUB LIMIT</td>
                        <td class="text-center">:</td>
                        <td>
                        <p>Isi optional tambahan perluasan jaminan diluar PAR </p>
                            <br>
                        <p>Apakah anda ingin membeli polis lain ? di SPPA ada tambahan apakah pertanyaan ini</p>
                        </td>
                    </tr>

                    <tr>
                        <td>CLAUSES
                            (applicable for any one location)</td>
                        <td class="text-center">:</td>
                        <td> 
                            <ol class="list-ol">';
                        
                        $query_get_clauses = mysqli_query($conn,"SELECT c.ket_clause 
                                                        FROM m_clause c 
                                                        LEFT JOIN m_jenis_jaminan j ON j.m_jaminan_id = c.id_jaminan
                                                        LEFT JOIN m_jenis_asuransi a ON a.m_jenis_id = j.id_asuransi
                                                        WHERE a.nm_jenis = '".$insurance_type." '
                                                        ORDER BY c.ket_clause ASC");
                        while($row_clause = mysqli_fetch_array ($query_get_clauses)){
                                echo'
                                    <li><span>'.$row_clause['ket_clause'].'</span></li>
                                ';
                        }

                        echo'
                            </ol>
                        </td>
                    <tr>
                        <td>RATE</td>
                        <td class="text-center">:</td>
                        <td>
                        <ul class="list-ul">
                        ';
                        if($no == 1){
                            $query_get_rate = mysqli_query($conn,"SELECT after_flexas,
                                                                    after_tfswd,
                                                                    after_eqvet,
                                                                    rate_par,
                                                                    rate_srmdcc,
                                                                    id_rumah
                                                            FROM tb_rumah
                                                            WHERE id_sppa ='".$id_sppa."' ");
                            if($row_rate = mysqli_fetch_array($query_get_rate)){
                                $id_rumah = $row_rate['id_rumah'];
                                echo "
                                <li>Rate FLEXAS : ".$row_rate['after_flexas']."% </li>
                                <li>Rate TFSWD : ".$row_rate['after_tfswd']."% </li>
                                <li>Rate EQVET : ".$row_rate['after_eqvet']."% </li>
                                <li>Rate PAR : ".$row_rate['rate_par']."% </li>
                                <li>Rate SRMDCC : ".$row_rate['rate_srmdcc']."% </li>";
                                $query_get_others_rate = mysqli_query($conn,"SELECT others_text,others_persen FROM tb_ratepremi_others WHERE id_rumah = '.$id_rumah.' ");
                                while($row_other_rate = mysqli_fetch_array($query_get_others_rate)){
                                    echo"<li>".$row__olther_rate['others_text']." : ".$row_other_rate['others_persen']."% </li>";
                                }


                            }
                        }else{
                            echo "
                                <li>Rate FLEXAS : (Detail as per list attached) </li>
                                <li>Rate TFSWD : (Detail as per list attached)</li>
                                <li>Rate EQVET : (Detail as per list attached) </li>
                                <li>Rate PAR : (Detail as per list attached) </li>
                                <li>Rate SRMDCC : (Detail as per list attached) </li>";
                        }

                        
                        echo'
                        <ul>
                        </td>
                    </tr>

                    <tr>
                        <td>TOTAL PREMIUM </td>
                        <td class="text-center">:</td>
                        <td>';
                            $total_premi = "";
                            $after_total = "";
                            $ojk_persen = "";
                            $brokrage = "";
                            $query_get_premi = mysqli_query($conn,"SELECT SUM(r.total_nilai_premi) as total , 
                                                                    SUM(r.after_total_nilai_premi) as after_total,
                                                                    s.max_disc max_ojk
                                                            FROM tb_rumah r 
                                                                LEFT JOIN tb_sppa s ON s.id_sppa = r.id_sppa
                                                            WHERE s.id_sppa = '".$id_sppa."' ");
                                                            if(!$query_get_premi){
                                                                die(mysqli_error());
                                                            }
                            if($row_premi = mysqli_fetch_array($query_get_premi)){
                                $total_premi = money_idr($row_premi['total']);
                                $after_total = money_idr($row_premi['after_total']);
                                $ojk_persen = $row_premi['max_ojk'];
                            }
                            echo $total_premi ;
                        echo'
                        </td>
                    </tr>
                    ';
                    if($kasih_client == "Ya"){
                        echo'
                    <tr>
                        <td>DISCOUNT OJK '.$ojk_persen.'%</td>
                        <td class="text-center">:</td>
                        <td>IDR 887,720,113.00 
                        
                    </td>
                    </tr>

                    <tr>
                        ';
                        $query_get_brokerage = mysqli_query($conn,"SELECT brokerage,brokerage_total FROM tb_sppa WHERE id_sppa = '".$id_sppa."'");
                        if($row_brokerage = mysqli_fetch_array($query_get_brokerage)){
                            $brokerage = $row_brokerage['brokerage'];
                            $brokerage_total = $row_brokerage['brokerage_total'];
                        }
                        echo'
                        <td>BROKERAGE '.$brokerage.'% </td>
                        <td class="text-center">:</td>
                        <td>'.$brokerage_total.' </td>
                    </tr>
                    ';
                    }
                    echo'
                    <tr>
                        <td>TOTAL After discount</td>
                        <td class="text-center">:</td>
                        <td>'.$after_total.'</td>
                    </tr>

                    <tr>
                        <td>POLICY COST</td>
                        <td class="text-center">:</td>
                        <td>IDR 50,000.00  </td>
                    </tr>

                    <tr>
                        <td>SECURITY </td>
                        <td class="text-center">:</td>
                        <td>
                        ';
                        $text = "";
                        $query_get_security = mysqli_query($conn,"SELECT s.class 
                                                            FROM m_security s
                                                            LEFT JOIN tb_sppa t on t.ins_type = s.ins_type
                                                            LEFT JOIN m_asuransi a ON s.ins_list = a.id_asuransi
                                                            WHERE t.id_sppa = '".$id_sppa."' ");
                        if($row_security = mysqli_fetch_array($query_get_security)){
                            $class = $row_security['class'];
                            if($class == "Class One"){
                                $text = "FIRST CLASS INSURANCE COMPANY";
                                
                            }
                            else if($class == "Class Two"){
                                $text = "SECOND CLASS INSURANCE COMPANY";
                            }
                            else if($class == "Class Three"){
                                $text = "THIRD CLASS INSURANCE COMPANY";
                            }
                            
                        }
                        
                        // LOSS RECORD NIL FOR PAST 3 YEARS dari historical loss di SPPA. Kalau untuk renewal ambilnya dari data lost record JPS
                        echo'
                        '.$text.'
                        </td>
                    </tr>

                    <tr>
                        <td>U/W INFORMATION</td>
                        <td class="text-center">:</td>
                        <td>
                        ';
                        if($no > 1){
                        $query_get_info = mysqli_query($conn,"SELECT * FROM tb_rumah WHERE id_sppa = '".$id_sppa."' ");
                        echo'
                        <li>BANGUNAN SEKITAR</li>
                        <li>APAR</li>
                        ';
                        }
                        echo'
                        </td>
                    </tr>

                    <tr>
                        <td>SPECIAL CONDITION</td>
                        <td class="text-center">:</td>
                        <td></td>
                    </tr>
                    ';
                    ?>
                </table>
            </div>

            <!-- Footer -->
            <div class="hk-footer-wrap container-fluid">
            </div>
            <!-- /Footer -->
       
        <!-- /Main Content -->

    
    <!-- /HK Wrapper -->

    <!-- start -->
    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <script src="../vendors/jquery/dist/jquery.mask.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="../dist/js/jquery.slimscroll.js"></script>

    <!-- Jasny-bootstrap  JavaScript -->
    <script src="../vendors/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="../vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Input spinner JavaScript -->
    <script src="../vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js"></script>
    <script src="../dist/js/inputspinner-data.js"></script>

    <!-- Pickr JavaScript -->
    <script src="../vendors/pickr-widget/dist/pickr.min.js"></script>
    <script src="../dist/js/pickr-data.js"></script>

    <!-- Daterangepicker JavaScript -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/daterangepicker/daterangepicker.js"></script>
    <script src="../dist/js/daterangepicker-data.js"></script>


    <!-- Data Table JavaScript -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="../dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="../dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="../vendors/jquery-toggles/toggles.min.js"></script>
    <script src="../dist/js/toggle-data.js"></script>

    <!-- Init JavaScript -->
    <script src="../dist/js/init.js"></script>

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>
    <!-- End -->

    <script>
        $('#datable_12').DataTable({
            responsive: true,
            autoWidth: false,
            "bSort": false,
            "searching": false,
            "info": false,
            "paging": false

        });
    </script>

</body>

</html>