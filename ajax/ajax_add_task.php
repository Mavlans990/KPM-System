<?php
	ob_start();
	session_start();

	include "../../lib/koneksi.php";
	include "../../lib/appcode.php";
	include "../../lib/format.php";

if (isset($_POST['id_asnwer'])) {
    $info=mysql_real_escape_string($_POST['id_answer']);
    $query_get_subject = mysql_query("SELECT nm_question,m_question_id FROM m_sop_question WHERE m_question_id = '".$info."' "); 
    if($row_subject = mysql_fetch_array($query_get_subject)){
        $id = $row_subject['m_question_id'];
        $name = $row_subject['nm_question'];
    }
    echo'
    <div class="input-group-prepend">
        <span style="width:100px;"
            class="form-control filled-input form-control-sm input-group-text "
            id="inputGroup-sizing-sm"> Subject </span>
    </div>
    <input type="text" name="subject" id="to" class="form-control form-control-sm filled-input" value="'.$name.'" required readonly>
    ';
    
}

ob_flush();
?>
