<?php
	session_start();
	
	if(isset($_SESSION['udana_rejestracja']))
	{
		$_SESSION['e_rejestracja']="Dziękujemmy za rejestrację. Teraz możesz się zalogować.";
		unset($_SESSION['udana_rejestracja']);
	}
	
	if (isset($_POST['email_log']))
	{	
		
		$logowanie=true;
		$email_log=$_POST['email_log'];
	
		if (!preg_match('@^[a-zA-Z0-9_.]{1,25}[\@][a-z]{1,20}[.][a-z]{1,20}$@',$email_log))
		{
			$logowanie=false;
			$_SESSION['e_email_log']="Nieprawidłowy format adresu e-mail.";
		}
		
		$password_log=$_POST['password_log'];
		if (!preg_match('@^[a-zA-Z0-9_.\@]{8,25}$@',$password_log))
		{
			$logowanie=false;
			$_SESSION['e_password_log']="Hasło musi posiadać 8 znaków.";
		}
		
		$hash_log=password_hash($password_log, PASSWORD_DEFAULT);
		
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);//nie wyswietla nam warningów
		//podaje nam exceptions, wyjątki za pomoca try, catch
		try
		{
			$polaczenie_log=new mysqli($host,$db_user,$db_password,$db_name);
			if ($polaczenie_log->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$sql=$polaczenie_log->query("SELECT * FROM uzytkownicy WHERE email='$email_log'  AND pass='$hash_log'");
				if (!$sql) throw new Exception($polaczenie_log->error);
				$ile=$sql->num_rows;
				print_r(mysql_fetch_assoc($sql));

				$polaczenie_log->close();	
				if ($ile>0)
				{
					$logowanie=false;
					$_SESSION['e_password_log']="Logowanie nie powiodło się. Nieprawidłowy login lub hasło.";
				}
				else
				{
					if($logowanie==true)
							{
							$_SESSION['udane_logowanie']=true;
							$_SESSION['idUser']=$id;
							header('Location: forbe.php');
							exit();
							}
				}
				$sql->free_result();
			}
				
						
		}
		catch(Exception $e) //$e-rodzaj bledu
		{
			echo "Błąd serwera. Przepraszamy za niedogodności.";
		}
	}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
		<meta charset="utf-8">
		<title>ForBE- Rehabilitacja dla ludzi biznesu
		</title>
		<link rel="stylesheet" href="css/foundation.min.css" />
		<link rel="stylesheet" href ="css/style.css" type="text/css">
</head>
<body>
<div class="row page half">
	<div class="small-12 medium-6 medium-centered large-4 large-centered columns form">
		<img src="css/img/logomale.png" alt="Logo ForBE"/>
		<form action="" method="POST">
			<?php
			if (isset($_SESSION['e_rejestracja']))
			{
				echo '<div class="ok">'.$_SESSION['e_rejestracja'].'</div>';
				unset($_SESSION['e_rejestracja']);
			}
			if (isset($_SESSION['e_email_log']))
			{
				echo '<div class="error">'.$_SESSION['e_email_log'].'</div>';
				unset($_SESSION['e_email_log']);
			}
			?>
			<input placeholder="Adres e-mail" type="text" name="email_log"/>
			<?php 
			if (isset($_SESSION['e_password_log']))
			{
				echo '<div class="error">'.$_SESSION['e_password_log'].'</div>';
				unset($_SESSION['e_password_log']);
			}
			?>
			<input placeholder="Hasło" type="password" name="password_log"/>
			<input class="log_in" type="submit" value="Zaloguj się"/>
		</form>
			
		<div> 
			<p class="p_form">Nie posiadasz konta? <a href="account.php" class="link">Załóż konto.</a></p>
		</div>
	</div>
</div>



</body>

</html>