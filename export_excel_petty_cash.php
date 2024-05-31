<?php
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";
session_start();

$tgl_from = mysqli_real_escape_string($conn,$_GET['tgl_from']);
$tgl_to = mysqli_real_escape_string($conn,$_GET['tgl_to']);

$saldo = 0;
$sql_get_saldo = mysqli_query($conn,"SELECT SUM(in_cash) AS ttl_in,SUM(out_cash) AS ttl_out FROM tb_petty_cash WHERE tgl < '" . $tgl_from . "'");
if ($row_saldo = mysqli_fetch_array($sql_get_saldo)) {
    $saldo = $row_saldo['ttl_in'] - $row_saldo['ttl_out'];
}
$sisa_saldo = $saldo;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan Petty Cash (" . date("d/m/Y", strtotime($tgl_from)) . " S/D " . date("d/m/Y", strtotime($tgl_to)) . ").xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Excel Petty Cash</title>
    <style>
        th,
        td {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <h2>Laporan Petty Cash</h2>
    <h4>Tgl : <?php echo date("d/m/Y", strtotime($tgl_from)); ?> S/D <?php echo date("d/m/Y", strtotime($tgl_to)); ?></h4>
    <h4>Saldo awal : <?php echo number_format($saldo, 2); ?></h4>
    <table>
        <thead>
            <th style="text-align:center;">No.</th>
            <th style="text-align:center;">Tgl</th>
            <th style="text-align:center;">Keterangan</th>
            <th style="text-align:center;">In</th>
            <th style="text-align:center;">Out</th>
            <th style="text-align:center;">Saldo</th>
        </thead>
        <tbody>
            <?php
            $no_petty = 1;
            $ttl_in = 0;
            $ttl_out = 0;
            $sql_petty_cash = mysqli_query($conn,"SELECT * FROM tb_petty_cash WHERE tgl BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' ORDER BY tgl ASC");
            while ($row_petty = mysqli_fetch_array($sql_petty_cash)) {
                $in_cash = $row_petty['in_cash'];
                $out_cash = $row_petty['out_cash'];

                $ttl_in += $in_cash;
                $ttl_out += $out_cash;

                $sisa_saldo = $sisa_saldo + $in_cash - $out_cash;

                $keterangan = nl2br($row_petty['keterangan']);

                echo '
                    <tr>
                        <td style="text-align:center;">' . $no_petty . '</td>
                        <td style="text-align:center;">' . date("d/m/Y", strtotime($row_petty['tgl'])) . '</td>
                        <td>' . $keterangan . '</td>
                        <td style="text-align:right;">' . number_format($row_petty['in_cash'], 2) . '</td>
                        <td style="text-align:right;">(' . number_format($row_petty['out_cash'], 2) . ')</td>
                        <td style="text-align:right;">' . number_format($sisa_saldo, 2) . '</td>
                    </tr>
                ';
                $no_petty++;
            }
            ?>
            <tr>
                <td style="text-align:center;" colspan="3">Grand Total</td>
                <td style="text-align:right;"><span style="font-weight:bold;"><?php echo number_format($ttl_in, 2); ?></span></td>
                <td style="text-align:right;"><span style="font-weight:bold;">(<?php echo number_format($ttl_out, 2); ?>)</span></td>
                <td style="text-align:right;"><span style="font-weight:bold;"><?php echo number_format($sisa_saldo, 2); ?></span></td>
            </tr>
        </tbody>
    </table>
</body>

</html>