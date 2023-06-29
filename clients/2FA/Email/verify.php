<?php

	// require "mail.php";
	require "functions.php";
	check_login();

	$errors = array();

	if($_SERVER['REQUEST_METHOD'] == "GET"){

		//send email
		$vars['code'] =  rand(1000,9999);

		//save to database
		$vars['expires'] = (time() + (60 * 1));
		$vars['email'] = $_SESSION['USER']->email;

		$query = "insert into f_clients (code,expires,email) values (:code,:expires,:email)";
		database_run($query,$vars);

		$message = "your code is " . $vars['code'];
		$subject = "Email verification";
		$recipient = $vars['email'];
		send_mail($recipient,$subject,$message);
	}

	if($_SERVER['REQUEST_METHOD'] == "POST"){

		if(!check_verified()){

			$query = "select * from f_clients where code = :code && email = :email";
			$vars = array();
			$vars['email'] = $_SESSION['USER']->email;
			$vars['code'] = $_POST['code'];

			$row = database_run($query,$vars);

			if(is_array($row)){
				$row = $row[0];
				$time = time();

				if($row->expires > $time){

					$id = $_SESSION['USER']->id;
					$query = "update f_clients set email_verified = email where id = '$id' limit 1";
					
					database_run($query);

					header("Location: profile.php");
					die;
				}else{
					echo "Code expired";
				}

			}else{
				echo "wrong code";
			}
		}else{
			echo "You're already verified";
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Verify</title>
</head>
<body>

	<h1>Verify</h1>

	<?php 
	// include('header.php');
	?>
  
	<br><br>
 	<div>
			<br>An email was sent to your email address. paste the code from the email here<br>

		</div><br>
		<form method="post">
			<input type="text" name="code" placeholder="Enter your Code"><br>
 			<br>
			<input type="submit" value="Verify">
		</form>
	</div>

</body>
</html>