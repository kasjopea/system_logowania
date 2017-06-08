<?php
	session_start();
	if (isset($_POST['nick']))//jesli jest ta zmienna lub dowolna inna tzn, ze zostal przeslany formularz
	{
		//zakladamy, ze walidacja jest udana
		$walidacja=true;
		$nick=$_POST['nick'];
		
		if (!preg_match('@^[a-zA-Z0-9]{3,20}$@',$nick))
		{
			$walidacja=false;
			$_SESSION['e_nick']="Nick musi posiadać od 3 do 20 znaków i zawierać tylko litery i cyfry.";
		}
			
		$email=$_POST['email'];
		
		if (!preg_match('@^[a-zA-Z0-9_.]{1,25}[\@][a-z]{1,20}[.][a-z]{1,20}$@',$email))
		{
			$walidacja=false;
			$_SESSION['e_email']="Nieprawidłowy format adresu e-mail.";
		}
		
		$password1=$_POST['password1'];
		$password2=$_POST['password2'];
		if (!preg_match('@^[a-zA-Z0-9_.\@]{8,25}$@',$password1))
		{
			$walidacja=false;
			$_SESSION['e_password1']="Hasło musi posiadać 8 znaków.";
		}
		
		if ($password1!=$password2)
		{
			$walidacja=false;
			$_SESSION['e_password2']="Podane hasła nie są identyczne.";
		}
		
		//$secret_key="6LfoygsUAAAAAMAiAxAhXFoBbeNloRBR4ZEk2pZu";
		//$answer=file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
		//$recaptcha=json_decode($answer);
		
		//if ($recaptcha->succes==false)
		//{
		//	$walidacja=false;
		//	$_SESSION['e_recaptcha']="Potwierdź, że nie jesteś robotem.";
		//}
		
		//Łączymy się z bazą, chcemy sprawdzić czy jest email i login w bazie
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);//nie wyswietla nam warningów
		//podaje nam exceptions, wyjątki za pomoca try, catch
		try
		{
			$polaczenie=new mysqli($host,$db_user,$db_password,$db_name);
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$email_in_baza=$polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				if (!$email_in_baza) throw new Exception($polaczenie->error);
				$ile_email_baza=$email_in_baza->num_rows;
				if ($ile_email_baza>0)
				{
					$walidacja=false;
					$_SESSION['e_email']="Już istnieje konto przypisane do tego adresu e-mail.";
				}
				
				$nick_in_baza=$polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
				if (!$nick_in_baza) throw new Exception($polaczenie->error);
				$ile_nick_baza=$nick_in_baza->num_rows;
				if ($ile_nick_baza>0)
				{
					$walidacja=false;
					$_SESSION['e_nick']="Nick zajęty.";
				}
				
				if($walidacja==true)
				{	$hash=password_hash($password1, PASSWORD_DEFAULT);
					//jeśli wszystkie testy pozytywny wynik, zakładamy konto
					if ($polaczenie->query("INSERT INTO uzytkownicy VALUES(NULL, '$nick','$hash', '$email',100,100,100,14)"))
						
						{
							$_SESSION['udana_rejestracja']=true;
							header('Location: index.php');
							exit();
						}
					else
					{
						throw new Exception($polaczenie->error);
					}
					exit();			
				}
				
				$polaczenie->close();			
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
		<script src="hrrps://www.google.com/recaptcha/api.js"></script>
</head>
<body>
<div class="row page">
	<div class="small-12 medium-6 medium-centered large-4 large-centered columns form" id="form_account">
		<img src="css/img/logomale.png" alt="Logo ForBE"/>
		<form action="" method="POST">
			<?php 
			if (isset($_SESSION['e_nick']))
			{
				echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
				unset($_SESSION['e_nick']);
			}
			?>
			<input placeholder="Nickname" type="text" name="nick"/>
			<?php 
			if (isset($_SESSION['e_email']))
			{
				echo '<div class="error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
			?>
			<input placeholder="Adres e-mail" type="text" name="email"/>
			<?php 
			if (isset($_SESSION['e_password1']))
			{
				echo '<div class="error">'.$_SESSION['e_password1'].'</div>';
				unset($_SESSION['e_password1']);
			}
			?>
			<input placeholder="Hasło" type="password" name="password1"/>
			<?php 
			if (isset($_SESSION['e_password2']))
			{
				echo '<div class="error">'.$_SESSION['e_password2'].'</div>';
				unset($_SESSION['e_password2']);
			}
			?>
			<input placeholder="Potwierdź hasło" type="password" name="password2"/>
			<p class="p_form">Klikając przycisk Utwórz konto, akceptujesz nasz <a class="link" href="">Regulamin.</a> </p>
			<input class="log_in" type="submit" value="Utwórz konto"/>
			<?php 
			if (isset($_SESSION['e_recaptcha']))
			{
				echo '<div class="error">'.$_SESSION['e_recaptcha'].'</div>';
				unset($_SESSION['e_recaptcha']);
			}
			?>
			<div class="g-recaptcha" data-sitekey="6LfoygsUAAAAADSfI9X0P3re5oRzkPS7y5sIuOvk"></div>
			</form>
	</div>
	
</div>


</body>

</html>