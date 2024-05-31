<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";
if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
	header('Location:index.php');
}
?>


<!DOCTYPE html>
<!--[if lt IE 7]>       <html class="no-js lt-ie9 lt-ie8 lt-ie7">   <![endif]-->
<!--[if IE 7]>          <html class="no-js lt-ie9 lt-ie8">          <![endif]-->
<!--[if IE 8]>          <html class="no-js lt-ie9">                 <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Dashboard</title>
	<meta name="description" content="Metis: Bootstrap Responsive Admin Theme">
	<meta name="viewport" content="width=device-width">
	<link type="text/css" rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link type="text/css" rel="stylesheet" href="assets/css/bootstrap-responsive.min.css">
	<link type="text/css" rel="stylesheet" href="assets/Font-awesome/css/font-awesome.min.css">
	<link type="text/css" rel="stylesheet" href="assets/css/style.css">
	<link type="text/css" rel="stylesheet" href="assets/css/calendar.css">
	<link type="text/css" rel="stylesheet" href="assets/css/DT_bootstrap.css" />

	<link rel="stylesheet" href="assets/css/theme.css">

	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!--[if IE 7]>
        <link type="text/css" rel="stylesheet" href="assets/Font-awesome/css/font-awesome-ie7.min.css"/>
        <![endif]-->

</head>
<style>
	body {
		font-size: 10pt;
	}

	#constrainer {
		height: 450px;
		width: 655px;
		overflow-y: auto;
	}

	.scrolltable {

		height: 100%;
	}

	.scrolltable>.body {
		width: 655px;

	}

	.scrolltable {
		display: flex;
		display: -webkit-flex;
		flex-direction: column;
		-webkit-flex-direction: column;
	}

	.scrolltable>.header {}

	.scrolltable>.body {
		flex: 1;
		-webkit-flex: 1;
	}

	th,
	td {}

	.highlight {
		background-color: yellow;
	}
</style>

<?php
/*
	if(isset($_POST['save']))
	{
		$valid=1;
		begin();
		
		
		
		header('Location: print_do_daily.php?tgl1='.$_POST['tgl1'].'&tgl2='.$_POST['tgl2'].'');
		
		
		if($valid==0)
		{  
			rollback();
		}
		else
		{	
			commit();
			$process_status="Saved successfully";
		}
		
		echo "<script type='text/javascript'>alert('".$process_status."')</script>";
								
	}
	*/
?>

<body>
	<!-- BEGIN WRAP -->
	<div id="wrap">

		<?php include "header.php"; ?>

		<div id="content">
			<div class="container-fluid outer">
				<div class="row-fluid">
					<div class="span12 inner" style="height:9000px;padding:15px;"" >
                            <div class=" row-fluid">
						<h4>SURAT JALAN LIST</h4>
					</div>
					<div class="row-fluid">
						<div class="span12">

							<form id="modal_form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-horizontal wizardForm">
								<input type="hidden" name="pk" id="pk">
								<div class="row-fluid">
									<div class="span12">
										<div class="control-group">


											<input type="date" name="tgl1" value="<?php if (isset($_GET['tgl1'])) echo $_GET['tgl1']; ?>" tabindex="50" class="TabOnEnter" id="id_kategori" autocomplete="off" style="width:140px;" />


											S/D
											<input type="date" name="tgl2" value="<?php if (isset($_GET['tgl2'])) echo $_GET['tgl2']; ?>" tabindex="50" class="TabOnEnter" id="id_kategori" autocomplete="off" style="width:140px;" />



											<select data-placeholder="Customer" name="kode_c" id="kode_c" autocomplete="off" class="chosen-select chosen-container kode_c" tabindex="2" style="width:260px;">
												<option value="kosong">Pilih Customer</option>
												<?php
												if ($_SESSION['grup'] == 'adm_pajak') {
													$filter = "where no_npwp <> '' ";
												}

												if ($_SESSION['grup'] == 'adm_non') {
													$filter = "where no_npwp = '' ";
												}

												$query = "select id_cust2,no_npwp,id_cust,company from tb_cust where kode = 'C' ";
												$get_barang = mysql_query($query);
												while ($row1 = mysql_fetch_array($get_barang)) {

													echo '<option value="' . $row1['id_cust2'] . '">
												' . $row1['id_cust'] . ' - ' . $row1['company'] . ' - ' . $row1['no_npwp'] . ' 
											</option>';
												}
												?>
											</select>


											<?php

											$jenis_transaksi = "";
											if (isset($_GET['jenis_transaksi'])) {
												$jenis_transaksi = $_GET['jenis_transaksi'];
											}

											?>

											<select name="jenis_transaksi">
												<option value="so" <?php if ($jenis_transaksi == "so") echo 'selected'; ?>>SO</option>
												<option value="mutasi-keluar" <?php if ($jenis_transaksi == "mutasi-keluar") echo 'selected'; ?>>MUTASI KELUAR</option>
												<option value="retur-beli" <?php if ($jenis_transaksi == "retur-beli") echo 'selected'; ?>>RETUR BELI</option>
											</select>


											<input name="invoice_cari" type="text" placeholder="NO.SJ / NO.SO / PO CUST" style="width:200px;" />


											<input type="submit" value="CARI" name="save" class="btn btn-primary btn-md tambah_cust" />




										</div>
									</div>
								</div>
							</form>

						</div>

					</div>

					<div class="row-fluid">
						<div class="span6">
							<div style="overflow-x:scroll;overflow-y:scroll;width:600px;height:400px;">
								<table border=1 style="font-size:10px;width:1000px">
									<tr style="background:#523bf9;color:white;">
										<td width="20px">No</td>
										<td width="50px"></td>
										<td width="150px">NO. SJ</td>
										<td width="100px">&nbsp;TGL</td>
										<td width="350px">&nbsp;NAMA CUSTOMER</td>
										<td width="150px">&nbsp;NO NPWP</td>
										<td width="150px">&nbsp;NO SO</td>

										<td width="150px">&nbsp;NO.PO CUST</td>
										<td width="150px">&nbsp;DIBUAT OLEH</td>
									</tr>
									<?php

									if (isset($_GET['tgl1'])) {


										$limit = ' limit 50 ';
										$filter = "";
										$filter2 = "";
										$filter3 = "and DATE_FORMAT(a.date_i,'%Y-%m-%d')>='" . date('Y-m') . "-01' and DATE_FORMAT(a.date_i,'%Y-%m-%d')<='" . date('Y-m-d') . "'";
										$filter4 = "";

										if ($_GET['kode_c'] != "kosong") {
											$filter = "and a.kode_c = '" . $_GET['kode_c'] . "' ";
										}

										if ($_GET['invoice_cari'] != "") {
											$filter2 = "and (a.invoice like '%" . $_GET['invoice_cari'] . "%' or a.no_po like '%" . $_GET['invoice_cari'] . "%'  or a.no_sj like '%" . $_GET['invoice_cari'] . "%' ) ";
										}

										if ($_GET['tgl1'] != "") {
											$limit = "";
											$filter3 = "
											and DATE_FORMAT(a.date_i,'%Y-%m-%d')>='" . $_GET['tgl1'] . "' 
											and DATE_FORMAT(a.date_i,'%Y-%m-%d')<='" . $_GET['tgl2'] . "' 
										";
										}


										if ($_GET['jenis_transaksi'] != "") {
											$filter4 = " and jenis_transaksi='" . $_GET['jenis_transaksi'] . "' ";
										}





										$query2 = "select a.id_user,c.no_npwp,a.no_sj,a.invoice,a.company,DATE_FORMAT(a.date_i,'%d/%m/%Y') as date_i,a.no_po ,a.jenis_transaksi,a.flag
									from sj a 
									left join tb_cust c on c.id_cust = a.kode_c
									where  (a.flag='4' or a.flag='3' or a.flag='2')
											" . $filter . " " . $filter2 . " " . $filter3 . "  " . $filter4 . " 
									
									group by a.no_sj,a.invoice 
									order by date_i asc ,no_sj asc 
									" . $limit . "
									 ";


										$x = 1;
										//echo $query2;
										$get_barang2 = mysql_query($query2);
										while ($row2 = mysql_fetch_array($get_barang2)) {
											$red = "";
											$color = "";
											$ketbatal = "";

											if ($row2['flag'] == 2) {
												$red = "style='color:red;'";
											}

											if (isset($_GET['invoice'])) {
												if ($row2['no_sj'] == $_GET['invoice']) {
													$color = "style='background:yellow;color:black;'";
												}
											}

											$kode_transaksi = $row2['no_sj'];




											echo "<tr " . $color . " " . $red . " id='" . $row2['no_sj'] . "'>";
											echo "
											<td nowrap>&nbsp" . $x . "</td>
											<td nowrap>&nbsp;<a  href='daily_sj.php?tgl1=" . $_GET['tgl1'] . "&tgl2=" . $_GET['tgl2'] . "&kode_c=" . $_GET['kode_c'] . "&invoice=" . $kode_transaksi . "&jenis_transaksi=" . $_GET['jenis_transaksi'] . "&invoice_cari=" . $_GET['invoice_cari'] . "#" . $row2['no_sj'] . "'>View</a>  
												
													<td nowrap>&nbsp;" . $row2['no_sj'] . "&nbsp;</td>
													<td nowrap>&nbsp;" . $row2['date_i'] . "&nbsp;</td>
													<td nowrap>&nbsp;" . $row2['company'] . "&nbsp;</td>
													<td nowrap>&nbsp;" . $row2['no_npwp'] . "&nbsp;</td>
													
													
													<td nowrap>&nbsp;" . $row2['invoice'] . "&nbsp;</td>
													<td nowrap>&nbsp;" . $row2['no_po'] . "&nbsp;</td>
										<td nowrap>&nbsp;" . $row2['id_user'] . "&nbsp;</td>
													
													";
											echo "</tr>";
											$x++;
										}
									}


									?>
								</table>
							</div>
							<img src="right.gif" width="60px">
						</div>


						<div class="span6">
							<?php if (isset($_GET['invoice'])) { ?>
								<center>
									<h1>SURAT JALAN </h1>
								</center>
								<table border="1" cellpadding="0px" style="font-size:11px;line-height:1.4em;margin-top:5px;text-transform:none;">


									<?php
									//onload="window.print();"

									$x = 1;

									$company = "";
									$nm_cust = "";
									$city =  "";
									$invoice =  "";
									$kode =  "";
									$kode_kasir = "";


									$query = "select  s.no_sj,s.flag,s.ket,c.no_npwp,c.nama_npwp,s.internal_notes,s.persen_pajak,s.amt_pajak,s.disc_tot,s.dpp,s.final_total,s.total,s.person,s.jenis_transaksi,s.id_cabang,s.no_po,u.kode,s.invoice,c.company,c.nm_cust,c.city,s.kode_kasir,c.alamat,c.telp,c.hp,DATE_FORMAT(s.date_i,'%d %M %Y') as tgl,keterangan,hapus_oleh from sj s 
													left join tb_cust c on c.id_cust2 = s.kode_c 
													left join tb_user u on u.id_user = s.id_user 
													where s.no_sj = '" . $_GET['invoice'] . "' ";



									$get_barang = mysql_query($query);
									while ($row1 = mysql_fetch_array($get_barang)) {
										$id_cabang = $row1['id_cabang'];
										$no_sj = $row1['no_sj'];
										$no_po = $row1['no_po'];
										$company = $row1['company'];
										$nm_cust = $row1['nm_cust'];
										$alamat = $row1['alamat'];
										$person = $row1['person'];
										$ket = $row1['ket'];
										$internal_notes = $row1['internal_notes'];
										$persen_pajak = $row1['persen_pajak'];
										$amt_pajak = $row1['amt_pajak'];
										$disc_tot = $row1['disc_tot'];
										$dpp = $row1['dpp'];
										$final_total = $row1['final_total'];
										$total = $row1['total'];
										$city = $row1['city'];
										$telp = $row1['telp'];
										$invoice = $row1['invoice'];
										$kode = $row1['kode'];
										$no_npwp = $row1['no_npwp'];
										$nama_npwp = $row1['nama_npwp'];
										$kode_kasir = $row1['kode_kasir'];
										$jenis_transaksi = $row1['jenis_transaksi'];
										$tgl = $row1['tgl'];
										$keterangan = $row1['keterangan'];
										$hapus_oleh = $row1['hapus_oleh'];
										$hp = $row1['hp'];

										$cancel = "";

										$flag = $row1['flag'];

										if ($row1['flag'] == "3") {
											$cancel = "<font color='red'>CANCEL/DELETED</font>";
										}
									}
									?>

									<tr>
										<th colspan=7 style="text-align:left;padding:10px;">

											<table>

												<tr>
													<td>No. SJ</td>
													<td width="20px">:</td>
													<td> <?php echo  $no_sj; ?> </td>
												</tr>


												<tr>
													<td>Tanggal SJ</td>
													<td>:</td>
													<td> <?php echo  $tgl; ?> </td>
												</tr>

												<tr>
													<td>No. SO</td>
													<td>:</td>
													<td> <?php echo  $invoice; ?> </td>
												</tr>



												<tr>
													<td>No. PO</td>
													<td>:</td>
													<td> <?php echo  $no_po; ?> </td>
												</tr>
												<tr>
													<td>Customer</td>
													<td>:</td>
													<td> <?php echo  $company; ?> </td>
												</tr>
												<tr>
													<td>Alamat Kirim</td>
													<td>:</td>
													<td> <?php echo  $alamat; ?> </td>
												</tr>
												<tr>
													<td>Nama NPWP</td>
													<td>:</td>
													<td> <?php echo  $nama_npwp; ?> </td>
												</tr>
												<tr>
													<td>No NPWP</td>
													<td>:</td>
													<td> <?php echo  $no_npwp; ?> </td>
												</tr>

												<tr>
													<td>ket</td>
													<td>:</td>
													<td> <?php echo  $ket; ?> </td>
												</tr>

												<tr>
													<td>Internal notes</td>
													<td>:</td>
													<td> <?php echo  $internal_notes; ?> </td>
												</tr>
												<?php if ($flag == 2) { ?>
													<tr>
														<td colspan="3" style="padding: 7px 0">TRANSAKSI INI TELAH DIHAPUS</td>
													</tr>
													<tr>
														<td>Dihapus Oleh</td>
														<td>:</td>
														<td> <?php echo $hapus_oleh; ?> </td>
													</tr>

													<tr>
														<td>Keterangan</td>
														<td>:</td>
														<td> <?php echo  $keterangan; ?> </td>
													</tr>
												<?php } ?>
											</table>
											<br>
										</th>
									</tr>

									<tr style="font-size:10pt;border-top:3px solid black;border-bottom:1px solid black;">
										<th width="49px">No</th>
										<th width="186px">Kode Produk</th>
										<th width="331px">Nama Produk</th>
										<th width="91px">Grup</th>
										<th width="51px">Qty</th>
										<th width="91px">Unit</th>

										<th width="91px">Remark</th>
									</tr>




									<?php
									$x = 0;
									$total = 0;



									$query = "select s.tgl_kirim,s.prc_s,s.unit,s.grup,s.kurs,s.curr,s.diskon,s.amount,s.remark,s.flag,s.partno,format(s.qty_s,0) as qty_s,s.desc_barang,s.kode_b,s.unit from sj s 
													where  s.no_sj = '" . $_GET['invoice'] . "'
													and ( s.flag='4' or s.flag='3' or s.flag='2' )
													group by s.partno,s.desc_barang,s.kode_b
													order by s.tgl_input asc
													
													";

									$get_barang = mysql_query($query);
									while ($row1 = mysql_fetch_array($get_barang)) {

										//for($i=0;$i<50;$i++)
										//{


										echo '<tr style="padding-top:0px;padding-bottom:0px;" >';
										echo '<td  style="padding-top:0px;padding-bottom:0px;"><center>' . ($x + 1) . '</center></td>';
										echo '<td style="padding-top:0px;padding-bottom:0px;">' . $row1['kode_b'] . '</td>';
										echo '<td  style="padding-top:0px;padding-bottom:0px;" > ' . substr($row1['desc_barang'], 0, 50) . ' </td>';
										echo '<td style="padding-top:0px;padding-bottom:0px;">' . $row1['grup'] . '</td>';
										echo '<td style="padding-top:0px;padding-bottom:0px;text-align:center;">' . $row1['qty_s'] . '</td>';
										echo '<td style="padding-top:0px;padding-bottom:0px;text-align:center;">' . $row1['unit'] . '</td>';





										echo '<td style="padding-top:0px;padding-bottom:0px;">' . $row1['remark'] . '</td>';

										echo '</tr>';
										$x++;

										//}												

									}


									?>

								</table>


								<br>


								<?php
								if ($flag == 4) {

									//cek invoicenya
									$query = "select no_inv from invoice  
													where  no_sj = '" . $_GET['invoice'] . "'
										";

									$get_barang = mysql_query($query);
									while ($row1 = mysql_fetch_array($get_barang)) {
										$no_inv = $row1['no_inv'];
									}


								?>

									<a class="btn btn-primary" href="print_sj.php?no_sj=<?php echo $_GET['invoice']; ?>" target="_blank">PRINT SURAT JALAN</a>
									<a class="btn btn-warning" href="print_sj_dropship.php?no_sj=<?php echo $_GET['invoice']; ?>" target="_blank">PRINT SURAT JALAN DROPSHIP</a>
									<?php if ($hp !== "" || $hp !== 0) { ?>
										<a class="btn btn-success" target="_blank" href="https://wa.me/<?php echo $hp; ?>?text=GMP%20REMINDER%20:%20Bubble%20wrap%20dengan%20surat%20jalan%20<?php echo $_GET['invoice']; ?>%20telah%20siap%20dikirimkan.">WHATSAPP CUSTOMER</a>
									<?php } ?>
									<!--<a class="btn btn-danger" href="print_inv.php?no_inv=<?php echo $no_inv; ?>">PRINT INVOICE</a>-->

									<br><br><br>



									<!--
								<p>
								  <a class="btn btn-success" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
									EDIT SJ
								  </a>
								
								</p>
								
								
								<div class="collapse" id="collapseExample">
								  <div class="card card-body">
										APAKAH ANDA YAKIN MENGUBAH TRANSAKSI INI ?
										<a href="edit_sj.php?no_sj=<?php echo $_GET['invoice']; ?>" class="btn btn-default"> YA, YAKIN  </a>
								  </div>
								</div>
								
								<p>
								  <a class="btn btn-danger" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false" aria-controls="collapseExample">
									HAPUS SJ
								  </a>
								
								</p>
								<div class="collapse" id="collapseExample2">
								  <div class="card card-body">
										APAKKAH ANDA YAKIN MENGHAPUS TRANSKSI INI ?
										<a href="hapus_sj.php?no_sj=<?php echo $_GET['invoice']; ?>" class="btn btn-default"> YA, YAKIN </a>
								  </div>
								</div>
								
							
								
								<a class="btn btn-info" href="buat_inv.php?no_sj=<?php echo $_GET['invoice']; ?>" target="_blank">
									Buat Invoice
								</a>
								-->

								<?php
								}
								?>

							<?php } ?>
						</div>




					</div>
					<!--row -->
				</div>




			</div>
		</div>

		<div id="push"></div>
	</div>

	<div class="clearfix"></div>


	<link rel="stylesheet" href="chosen/docsupport/prism.css">
	<link rel="stylesheet" href="chosen/chosen.css">


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script>
		window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')
	</script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script>
		window.jQuery.ui || document.write('<script src="assets/js/vendor/jquery-ui-1.10.0.custom.min.js"><\/script>')
	</script>
	<script src="assets/js/vendor/bootstrap.min.js"></script>


	<script src="assets/js/main.js"></script>


	<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
	<script src="chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>


	<script type="text/javascript">
		$(document).on("keypress", "#cr_l", function(e) {

			if (e.keyCode == 13) {
				$(".tambah_cust").focus();
			}
		});

		$(".TabOnEnter").keydown(function(e) {

			$flag = 0;
			switch (e.which) {
				case 40: //arrow dowwn

					event.preventDefault();

					var nextElement = $('input[tabindex="' + (this.tabIndex + 1) + '"]');

					if (nextElement.length) // this is for next element
						nextElement.focus();
					else
						$('input[tabindex="1"]').focus(); //this is for first element

					break;
				case 13: // enter

					event.preventDefault();

					var nextElement = $('input[tabindex="' + (this.tabIndex + 1) + '"]');

					if (nextElement.length) // this is for next element
						nextElement.focus();
					else
						$('input[tabindex="1"]').focus(); //this is for first element

					break;
				case 38: // enter

					event.preventDefault();

					var nextElement = $('input[tabindex="' + (this.tabIndex - 1) + '"]');

					if (nextElement.length) // this is for next element
						nextElement.focus();
					else
						$('input[tabindex="1"]').focus(); //this is for first element

					break;
			}

		});


		var selected_index = 1;
		var max_row = <?php echo $x; ?>;



		$("#search_query").keydown(function(e) {
			switch (e.which) {
				case 13:
					search_query = $("#search_query").val();
					search_query = search_query.toUpperCase();

					if (search_query == "") {
						//window.location.href = "http://localhost:7777/sby_diesel/brand.php";
						window.location.href = "http://localhost/beauty/brand.php";

					} else {
						//window.location.href = "http://localhost:7777/sby_diesel/brand.php?query="+search_query;
						window.location.href = "http://localhost/beauty/brand.php?query=" + search_query;
					}

					//alert(search_query);
					//$('#tabel_customer td').parent().hide();
					//$('#tabel_customer td:contains("'+search_query+'")').parent().show();
					$("#search_query").val("");
					$("#search_query").focus();
					break;

			}
		});


		$("#tabel_customer").keydown(function(e) {
			switch (e.which) {
				case 13:
					search_query = "";
					$('#tabel_customer td').parent().hide();
					$('#tabel_customer td:contains("' + search_query + '")').parent().show();
					break;

			}
		});


		$("#tabel_customer").keydown(function(e) {

			switch (e.which) {
				case 38:


					event.preventDefault();
					var ele = $('#constrainer');
					var scroll = 28;

					$(".baris" + selected_index).removeClass("highlight");
					selected_index = parseInt(selected_index) - 1;
					if (selected_index == 0) {
						selected_index = 1;
					}

					$(".baris" + selected_index).addClass("highlight");
					rowbutton_click(selected_index);

					ele.scrollTop(ele.scrollTop() - scroll);
					break;
				case 40:
					event.preventDefault();
					var ele = $('#constrainer');
					var scroll = 28;



					$(".baris" + selected_index).removeClass("highlight");
					selected_index = parseInt(selected_index) + 1;
					if (selected_index == max_row) {
						selected_index = selected_index - 1;
					}
					$(".baris" + selected_index).addClass("highlight");
					rowbutton_click(selected_index);

					ele.scrollTop(ele.scrollTop() + scroll);
					break;
			}

		});



		$("#id_kategori").change(function() {
			var id_kategori = $('#id_kategori').val();
			var dataString = 'id_kategori=' + id_kategori;

			$.ajax({
				type: "POST",
				url: "check_cust.php",
				data: dataString,
				cache: true,
				success: function(data_return) {

					result = $.trim(data_return);
					if (result > 0) {
						alert("kode customer sudah ada");
						$('#id_kategori').val("");
						$('#id_kategori').focus();
					}
				}
			});
		});


		$(document).ready(function() {
			$("#pk").val("new");
			$(".panah").click();
			$("#rowbutton1").click();
		});

		$(function() {
			metisTable();
		});

		function koma(input) {
			var output = input
			if (parseFloat(input)) {
				input = new String(input); // so you can perform string operations
				var parts = input.split("."); // remove the decimal part
				parts[0] = parts[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
				output = parts.join(".");
			}

			return output;
		}


		$("#master_customer").addClass("active");

		function rowbutton_click(id) {

			$("#id_kategori").prop('readonly', true);
			$(".baris" + selected_index).removeClass("highlight");
			selected_index = id;
			$(".baris" + selected_index).addClass("highlight");

			var id_kategori = $('#rowbutton' + id).data('id_kategori');
			var nm_kategori = $('#rowbutton' + id).data('nm_kategori');


			$("#pk").val(id_kategori);
			$("#id_kategori").val(id_kategori);
			$("#nm_kategori").val(nm_kategori);


		}

		$(document).on("click", '.tambah_button', function(e) {
			$('#modal_form')[0].reset();
			$("#pk").val("new");
			$("#id_kategori").focus();
			$("#id_kategori").prop('readonly', false);
		});

		document.getElementById("cr_l_view").onblur = function() {
			this.value = parseFloat(this.value.replace(/,/g, ""))
				.toFixed(0)
				.toString()
				.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

			document.getElementById("cr_l").value = this.value.replace(/,/g, "")

		}
	</script>

	<script type="text/javascript">
		var config = {
			'.chosen-select': {},
			'.chosen-select-deselect': {
				allow_single_deselect: true
			},
			'.chosen-select-no-single': {
				disable_search_threshold: 10
			},
			'.chosen-select-no-results': {
				no_results_text: 'Oops, nothing found!'
			},
			'.chosen-select-width': {
				width: "95%"
			}
		}
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
	</script>

	<script type="text/javascript" src="assets/js/style-switcher.js"></script>

</body>

</html>