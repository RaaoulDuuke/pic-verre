<?php

	session_start();
	
	require_once ("../../includes/connect.php");
	require_once ("../../includes/common.functions.php");
	require_once ("../../includes/mails.functions.php");

	// USER LOGIN
	if (!empty($_REQUEST["action"])&&$_REQUEST['action']=="in"){
		
		$email = (!empty($_POST['email'])) ? $_POST['email'] : '';
		$pwd = (!empty($_POST['pwd'])) ? $_POST['pwd'] : '';
		
		if (!empty($email) && !empty($pwd)) {
			// USER EMAIL REQUEST
			//$user_rq = "SELECT * FROM users WHERE email='{$email}' AND valid=1";
			$user_rq = "SELECT * FROM users WHERE email='{$email}'";
			$user_rs = mysqli_query ($connexion, $user_rq);
			$user_nb = mysqli_num_rows($user_rs);	
			if($user_nb){
				$user = mysqli_fetch_array($user_rs);
				// SALT
				$userSalt_rq = "SELECT salt FROM testUser WHERE userID='{$user['id']}'";
				$userSalt_rs = mysqli_query ($connexion, $userSalt_rq);
				$userSalt = mysqli_fetch_array($userSalt_rs);
				$salt = $userSalt['salt'];				
				$pwdenc = hash_hmac('sha256', $pwd, $salt);
				// USER PWD TEST
				//$userValid_rq = "SELECT * FROM users WHERE pwdenc='{$pwdenc}' AND email='{$email}' AND valid=1";
				$userValid_rq = "SELECT * FROM users WHERE pwdenc='{$pwdenc}' AND email='{$email}' ";
				$userValid_rs = mysqli_query ($connexion, $userValid_rq);
				if(mysqli_num_rows($userValid_rs)){
					$userValid = mysqli_fetch_array($userValid_rs);
					// SESSION VAR
					$_SESSION['logged'] = true;
					$_SESSION['userID'] = $userValid['id'];
					$_SESSION['action'] = "login";

				}else{
					$_SESSION['action'] = "login_errMdp";
				}
			}else{
				$_SESSION['action'] = "login_errEmail";
			}
		}else{
			$_SESSION['action'] = "login_errForm";
		}
	}
	
	// USER LOGOUT
	if (!empty($_REQUEST["action"])&&$_REQUEST['action']=="out"){
		if($_SESSION["logged"]){ 

			$_SESSION['id_user']="";
			$_SESSION['logged'] = false;
			session_destroy();

			session_start();
			$_SESSION['action'] = "logout";
		}
	}
	
	// ASK RESET PASSWORD
	if (!empty($_REQUEST["action"])&&$_REQUEST['action']=="askreset"){
		
		$email = (!empty($_POST['email'])) ? $_POST['email'] : '';
		
		if (!empty($email)) {
			// USER EMAIL REQUEST
			//$user_rq = "SELECT * FROM users WHERE email='{$email}' AND valid=1";	
			$user_rq = "SELECT * FROM users WHERE email='{$email}'";
			
			$user_rs = mysqli_query ($connexion, $user_rq);
			if(mysqli_num_rows($user_rs)){
				
				$token =  random_str(64);
				
				$resetDelete_rq = "DELETE FROM reset WHERE email='{$email}'";
				$resetDelete_rs = mysqli_query($connexion, $resetDelete_rq) or die();
				
				$resetCreate_rq = "INSERT INTO reset (email, token) VALUES ('{$email}','{$token}')";
				$resetCreate_rs = mysqli_query($connexion, $resetCreate_rq) or die();
				
				
				$title = "Réinitialisation du mot de passe";
				$lead = "Cliquez sur le lien ci-dessous pour être redirigé vers la page permettant de réinitialiser votre mot de passe.<br><small>Si vous n'avez pas fait cette demande, ignorez cet email.</small>";
				
				$content = "<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#13d485; display:block;' href='https://mon-compte.pic-verre.fr/reset.php?action=resetpwd&token={$token}&email={$email}'><strong>Réinitialiser mon mot de passe</strong></a>";
				
				sendmail($email, $title, $lead, $content);
				
				$_SESSION['action'] = "askreset_valid";
				

			}else{
				$_SESSION['action'] = "askreset_errEmail";
			}
		}else{
			$_SESSION['action'] = "askreset_errForm";
		}

	}
	
	// RESET PASSWORD
	if (!empty($_REQUEST["action"])&&$_REQUEST['action']=="resetpwd"){
		
		$token = (!empty($_POST['token'])) ? $_POST['token'] : '';
		$pwd = (!empty($_POST['pwd'])) ? $_POST['pwd'] : '';
		$pwdconfirm = (!empty($_POST['pwdconfirm'])) ? $_POST['pwdconfirm'] : '';
		
		if(!empty($pwd)&&!empty($pwdconfirm)&&!empty($token)&&$pwd==$pwdconfirm){
		
			// USER EMAIL REQUEST
			$reset_rq = "SELECT * FROM reset WHERE token='{$token}'";
			$reset_rs = mysqli_query ($connexion, $reset_rq);
			if(mysqli_num_rows($reset_rs)){
				
				$reset = mysqli_fetch_array($reset_rs);
				
				// USER EMAIL REQUEST
				//$user_rq = "SELECT * FROM users WHERE email='{$reset['email']}' AND valid=1";
				$user_rq = "SELECT * FROM users WHERE email='{$reset['email']}'";
				$user_rs = mysqli_query ($connexion, $user_rq);
				if(mysqli_num_rows($user_rs)){
					
					$user = mysqli_fetch_array($user_rs);
					
					// SALT PWD
					$salt = uniqid(mt_rand());
					$pwdenc = hash_hmac('sha256', $pwd, $salt);			
					// UPDATE USER
					$pwdUpdate_rq = "UPDATE users SET pwdenc='{$pwdenc}' WHERE id={$user['id']}";
					$pwdUpdate_rs = mysqli_query($connexion, $pwdUpdate_rq) or die();

					$saltDelete_rq = "DELETE FROM testUser WHERE userID='{$user['id']}'";
					$saltDelete_rs = mysqli_query($connexion, $saltDelete_rq) or die();
					
					$saltCreate_rq = "INSERT INTO testUser(salt, userID) VALUES ('{$salt}',{$user['id']})";
					$saltCreate_rs = mysqli_query($connexion, $saltCreate_rq) or die();	
					
					$title = "Réinitialisation<br>du mot de passe";
					$lead = "Votre mot de passe a bien été réinitialisé !";
					
					sendmail($email, $title, $lead);
					
					// SESSION VAR
					$_SESSION['logged'] = true;
					$_SESSION['userID'] = $user['id'];
					$_SESSION['action'] = "resetpwd_valid";
					
				}
				
			}
		}else{
			// REDIRECT VAR
			$_SESSION['action'] = "resetpwd_err";
		}
	}
	
	// REDIRECTION
	header("Location:../connexion.php");
	exit;
	
?>