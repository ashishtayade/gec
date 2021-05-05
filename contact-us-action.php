<?php

/*echo "<pre>";
	print_r($_POST);*/
	
	

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	exit;*/

	session_start();
//	include("./phpincludes/connection.php");
	require_once("./phpincludes/commonfunctions.php");
	include("./phpincludes/GenericClass.php");
	//include("./phpincludes/class.phpmailer.php");
	
	
	
	// validation
	/*if(trim($_POST['Name'])=='' || trim($_POST['Email'])=='' || trim($_POST['Mobile'])=='')
	{
		$_SESSION['Error'] = "Invalid Data";
        $_SESSION['Post'] = $_POST;
        header("Location:".$_SERVER['HTTP_REFERER']);
        exit();
	}
	
	if(!preg_match("/[A-Za-z]/", $_POST['Name']))
    {
        $_SESSION['Error'] = "Invalid Data";
        $_SESSION['Post'] = $_POST;
        header("Location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    if(!preg_match("/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/", $_POST['Email']))
    {
        $_SESSION['Error'] = "Invalid Data";
        $_SESSION['Post'] = $_POST;
        header("Location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    if(!preg_match("/[0-9-()]+/", $_POST['Mobile']))
    {
        $_SESSION['Error'] = "Invalid Data";
        $_SESSION['Post'] = $_POST;
        header("Location:".$_SERVER['HTTP_REFERER']);
        exit();
    }*/

	
	if(isset($_POST['name']) && $_POST['name'] != "")
	{	
		$fp=fopen("./mailer/ContactMailer.html","r");
		$message= fread($fp,filesize("./mailer/ContactMailer.html"));
		
		$DateTime = convertDBDateTime(date("Y-m-d H:i:s"));
		$Subject = "GEC Enquiry by ".$_POST['name']."  Dated ".$DateTime;
		$_POST['RegTime']=date("Y-m-d H:i:s");

		$message=str_replace('$Name', $_POST['name'],$message);
		$message=str_replace('$Companyname',$_POST['companyname'],$message);
		$message=str_replace('$Designation',$_POST['designation'],$message);
		$message=str_replace('$City',$_POST['city'],$message);
		$message=str_replace('$Mobile',$_POST['mobile'],$message);
		$message=str_replace('$Email',$_POST['email'],$message);
		$message=str_replace('$Describe',$_POST['describe'],$message);
		
		//$message=str_replace('$Comments', str_replace("\n","<br>",$_POST['Comments']),$message);
		$message=str_replace('$DateTime', $DateTime,$message);
		
		//echo $message; exit;
		
		include("./class.phpmailer.php");
		$mail             = new PHPMailer1();
		$body             = $message;
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host       = "smtp.gmail.com"; // SMTP server
		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												  // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = "smtp.gmail.com";      // sets  as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the server
		$mail->Username   = "xxxx@gmail.com";  	   // username
		$mail->Password   = "xxxxxxxxxx";          // password
		
		$mail->SetFrom('xxxx@gmail.com', 'GEC');
		$mail->Subject    = $Subject;
		$mail->MsgHTML($body);
		
		//$mail->AddBCC("");
		//$mail->AddBCC(""); 
		//$mail->AddAddress(""); 
		$mail->AddAddress("xxxx@gmail.com");
		//$mail->AddCC("ashish.tayade4@gmail.com");
		//$mail->AddBCC("");
		 
		if(!$mail->Send()) 
	
		{
		  //echo "Mailer Error: " . $mail->ErrorInfo; exit;
		} 
		else 
		{
		 //echo "Message sent!"; exit;
		}
		unset($_SESSION['Post']);
		header("Location:./thank-you.html");
		exit();
	}
?>