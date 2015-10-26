<?php 
// application top
require_once 'includes/application_top.php';
require_once('includes/classes/phpmailer/class.phpmailer.php');


// ak je setnute session, sme tu omylom => smerujeme na admin
if(isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit();
}

$tmp->define(array("login" => "login.htm"));
$tmp->assign(array("ROOT_PATH" => ROOT_PATH,
				   "REGISTRATION" => REGISTRATION,
				   "GET_NEW_PASSWORD_BTN" => GET_NEW_PASSWORD_BTN,
				   "BACK_LOGIN_LINK" => BACK_LOGIN_LINK,
				   "NEW_PASS_INFO_TEXT" => NEW_PASS_INFO_TEXT,
				   "FOROGT_EMAIL_MESSAGE" => FOROGT_EMAIL_MESSAGE,
				   "NEW_PASS_INFO_SUCCESS_TEXT" => NEW_PASS_INFO_SUCCESS_TEXT,
				   "EMAIL_NOEXIST_MESSAGE" => EMAIL_NOEXIST_MESSAGE));


if(isset($_GET['newpassword'])){
	$tmp->parse("EMAIL_OK",".forgotpass_email_succes");
}
	

if(isset($_POST['new-password-atempt'])){
	
	if(empty($_POST['email'])){
		header('Location: '.ROOT_PATH.'login.php?forgotpass=1&error=1 ');
		exit;
	}
	
	
	
	$to      = trim($_POST['email']);
	$user = get_user_by_email($to);
	
	if(!$user){
		header('Location: '.ROOT_PATH.'login.php?forgotpass=1&emailnoexist=1 ');
		exit;
	}
	
	$subject = 'Online laboratory manager - new password';
	$newPass = generatePassword();
	$newPassHash = create_pass_general($newPass,$user['login']);
	
	
	$message = '<html>
					<head></head>
					<body>
						<div>
							<p>
								Dobrý deň,
								<br /><br />
								
								na Vašu žiadosť Vám zasielame nové heslo:<br /><br />
								
								<strong>'. $newPass .'</strong><br /><br />
								
								S pozdravom
									
								Online laboratiry manager
							</p>
						</div>
					</body>
				</html>';
	
	
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: webmaster@olm.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();
	
	

	send_mail('OLM admin','noreply.olm@gmail.com',$user,$to,$subject,$message);
	$mysql->query("UPDATE ".TABLE_ADMIN_USERS." SET pass = '". $newPassHash ."' WHERE id = '". $user['id'] ."'  ");
	header('Location: '.ROOT_PATH.'login.php?newpassword=1 ');
	exit();
	
	/*if(mail($to, $subject, $message, $headers)){
		$mysql->query("UPDATE ".TABLE_ADMIN_USERS." SET pass = '". $newPassHash ."' WHERE id = '". $user['id'] ."'  ");
		header('Location: '.ROOT_PATH.'login.php?newpassword=1 ');
		exit();
	}*/
	
}


//formular pre nove heslo
if(isset($_GET['forgotpass'])){
	
	if(isset($_GET['error'])){
		$tmp->parse("EMPTY_EMAIL",".forgotpass_email_empty");
	}
	
	if(isset($_GET['emailnoexist'])){
		$tmp->parse("NOEXIST_EMAIL",".email_no_exist");
	}
		
	$tmp->assign(array("FORM" => ""));
	$tmp->parse("FROGOTPASS",".forgotpass_block");
}
//formular pre prihlasenie
else{
	/*MODUL PRIHLLASENIE*/
	require_once DIR_WS_MODULES.'login/login.php';
	$tmp->assign(array("FORM" => get_auth_form() ));
}


require_once DIR_WS_MODULES.'registration/registration.php';

$tmp->assign(array("REGISTER_BOX" => get_register_form() ));



$tmp->parse("LOGIN",'.login');
$tmp->xprint();

//-------------------------------------------------------

function send_mail($from, $from_addr, $to, $to_addr, $subject, $text){
	$mail             = new PHPMailer();
	
	//$body             = file_get_contents('contents.html');
	$body             = $text;//eregi_replace("[\]",'',$body);
	
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = "mail.yourdomain.com"; // SMTP server
	//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
						// 1 = errors and messages
						// 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
	$mail->Username   = "noreply.olm@gmail.com";  // GMAIL username
	$mail->Password   = "olm123olm";            // GMAIL password
	$mail->CharSet    = 'UTF-8'; 

	$mail->SetFrom($from_addr, $from);
	
	//$mail->AddReplyTo("name@yourdomain.com","First Last");
	
	$mail->Subject    = $subject;
	
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	
	$mail->MsgHTML($body);
	
	$address = $to_addr;
	$mail->AddAddress($address, $to);
	
	//$mail->AddAttachment("images/phpmailer.gif");      // attachment
	//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
	
	if(!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}
}
 
?>