<?php

$timestamp = date("YmdHis", time());

//echo "<pre>";
//print_r($_REQUEST);
//
//print_r($_FILES);
////exit;

if ($_FILES['image']['name'] != '') {
    $Imagename = $timestamp . $_FILES["image"]["name"];
    move_uploaded_file($_FILES["image"]["tmp_name"], "upload/" . $Imagename);
}

if ($_FILES['presentation']['name'] != '') {
    $presentationname = $timestamp . $_FILES["presentation"]["name"];
    move_uploaded_file($_FILES["presentation"]["tmp_name"], "upload/" . $presentationname);
}


$content = "";
$content .= "<br /><br /><br />";
$content .= "<table cellpadding=5 cellspacing=5 border=1 style=font-family:arial; font-size=12px; >";

//$content .= "<tr>";
//$content .= "<td>Upload your picture :  </td>";
//$imageUpload = ($_FILES['image']['name'] != '') ? 'Done' : 'No';
//$content .= "<td>" . $imageUpload . "</td>";
//$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>Name : </td>";
$content .= "<td>" . $_REQUEST['name'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>Email : </td>";
$content .= "<td>" . $_REQUEST['email'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>Company Name :  </td>";
$content .= "<td>" . $_REQUEST['companyname'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>Service : </td>";
$content .= "<td>" . $_REQUEST['servicemobile'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td></td>";
$content .= "<td>" . $_REQUEST['servicewebapp'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td</td>";
$content .= "<td>" . $_REQUEST['serviceecomm'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td</td>";
$content .= "<td>" . $_REQUEST['serviceopensource'] . "</td>";
$content .= "</tr>";


$content .= "<tr>";
$content .= "<td</td>";
$content .= "<td>" . $_REQUEST['servicehirebase'] . "</td>";
$content .= "</tr>";


$content .= "<tr>";
$content .= "<td>Summery : </td>";
$content .= "<td>" . $_REQUEST['summery'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>Country :  </td>";
$content .= "<td>" . $_REQUEST['country'] . "</td>";
$content .= "</tr>";

$content .= "<tr>";
$content .= "<td>ZipCode : </td>";
$content .= "<td>" . $_REQUEST['zipcode'] . "</td>";
$content .= "</tr>";


$content .= "<tr>";
$content .= "<td>Upload your presentation :  </td>";
$presiontaionUpload = ($_FILES['presentation']['name'] != '') ? 'Done' : 'No';
$content .= "<td>" . $presiontaionUpload . "</td>";
$content .= "</tr>";

$content .= "</table>";

require 'phpmailer/class.phpmailer.php';

$to = 'bhadresh@webever.co';
$bcc = 'bhadresh.genie@gmail.com';
$from = 'connect@webever.co';
$fromName = 'Webever Technologies';
$subject = "Inquiry from Webever Technologies Website";

$mail = new PHPMailer();

$mail->SMTPAuth = true;                  // enable SMTP authentication
$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
$mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port = 587;                   // set the SMTP port for the GMAIL server
$mail->Username = "animotion.it@gmail.com";  // GMAIL username
$mail->Password = "anim0ti0n";            // GMAIL password

$mail->SetFrom($from, $fromName);

$mail->Subject = $subject;

$mail->MsgHTML($content);

$mail->AddAddress($to);

$mail->AddBCC($bcc);

if ($_FILES['image']['name'] != '') {
    $mail->AddAttachment("upload/" . $Imagename);      // attachment
}

if ($_FILES['presentation']['name'] != '') {
    $mail->AddAttachment("upload/" . $presentationname);      // attachment
}

if (!$mail->Send()) {
    //echo "Mailer Error: " . $mail->ErrorInfo;
	$status=0;
} else {
   // echo "Message sent!";
		$status=1;
}


//header('Location: upload-your-presentation.html');

?>
<script>
window.location='thankyou.php?message=<?php echo $status; ?>';
</script>
