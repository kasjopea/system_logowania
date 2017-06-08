<?php
session_start();
  function __autoload($class) {
    require("Classes/$class.php");
  }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
		<meta charset="utf-8">
		<title>ForBE- Aby żyło się łatwiej
		</title>
		<link rel="stylesheet" href="css/foundation.min.css" />
		<link rel="stylesheet" href ="css/style.css" type="text/css">
		<script src="hrrps://www.google.com/recaptcha/api.js"></script>
</head>
<body>
<div class="row page">
	<div class="small-12 medium-6 medium-centered large-4 large-centered columns form" id="form_account">
		<img src="css/img/logomale.png" alt="Logo ForBE"/>
		<table>
			<?php
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
					echo "przed insertem";
					if ($polaczenie->query("SELECT * FROM uzytkownicy WHERE idUser='$_SESSION["idUser"]'"))
						
						{
							$_SESSION["echo"]="Dodano produkt do bazy";
						}
					else
					{
						throw new Exception($polaczenie->error);
					}
					exit();			
			}




			?>
		</table>
		<form action="" method="POST">
			<input placeholder="Nazwa produktu" type="text" name="product"/>
			<input placeholder="Data zakupu" type="date" name="dateIn"/>
			<input placeholder="Data końca gwarancji" type="date"
			name="dateOut"/>
			<input placeholder="Sklep" type="text" name="brand"/>
			<input placeholder="Cena" type="number" name="price"/>
			<input placeholder="Typ obuwia" type="text" name="type"/>
			<input placeholder="Wybierz plik" type="file" name="img" accept="image/*"/> 
			<input class="log_in" type="submit" value="Dodaj artykuł"/>
			</form>
	</div>
	
</div>
<?php
if(isset($_POST["product"])){
	$name= $_POST["product"];
	$brand= $_POST["brand"];
	$dateIn = $_POST["dateIn"];
	$dateOut = $_POST["dateOut"];
	$price = $_POST["price"];
	$type= $_POST["type"];

	$product = new product($name, $brand, $dateIn, $dateOut, $price, $type);
	echo $product->name;


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
				echo "przed insertem";
				if ($polaczenie->query("INSERT INTO products VALUES(NULL,'$product->name', '$product->brand','$product->dateIn', '$product->dateOut', '$product->price','$product->type')"))
						
						{
							$_SESSION["echo"]="Dodano produkt do bazy";
						}
					else
					{
						throw new Exception($polaczenie->error);
					}
					exit();			
			}
			$polaczenie->close;
			unset($_POST["product"]);
					}
		catch(Exception $e) //$e-rodzaj bledu
		{
			echo "Błąd serwera. Przepraszamy za niedogodności.";
		}
		
	}

?>


</body>

</html>