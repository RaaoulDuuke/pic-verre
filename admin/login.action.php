<?php
	session_start();
	require_once ("../includes/connect.php");
	
	/* USER LOGIN */
	if (!empty($_REQUEST["action"]) && $_REQUEST['action'] == "login") {
		
		$email = (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : '';
		$pwd = (!empty($_REQUEST['pwd'])) ? $_REQUEST['pwd'] : '';
		
		if (empty($email) || empty($pwd)) {
			header("location:login.php?action=login_errForm");
			
		} else {
	
			$rq_admin = "SELECT * FROM admin WHERE email='{$email}'";
			$rs_admin = mysqli_query ($connexion, $rq_admin);
			$nb_admin = mysqli_num_rows($rs_admin);
			
			if($nb_admin){

				$rq_adminMdp = "SELECT * FROM admin WHERE pwd='{$pwd}' AND email='{$email}'";
				$rs_adminMdp = mysqli_query ($connexion, $rq_adminMdp);
				$nb_adminMdp = mysqli_num_rows($rs_adminMdp);
				
				if($nb_adminMdp){
					
					$_SESSION['logged'] = true;
					$admin = mysqli_fetch_assoc($rs_adminMdp);
					$_SESSION['adminID'] = $admin['id'];
					
					header("location:index.php?action=login");

				}else{
					header("location:login.php?action=login_errMdp");
				}
			}else{
				header("location:login.php?action=login_errEmail");
			}
	
		}
	}
	
	/* USER LOGOUT*/
	if (!empty($_REQUEST["action"]) && $_REQUEST['action'] == "logout") {

		if(!$_SESSION["logged"]){ 
			header("Location: login.php");
			
		} else {
			$_SESSION['adminID']="";
			$_SESSION['logged'] = false;
			session_destroy();
			header("Location:login.php?action=logout");
		}
	}
?>