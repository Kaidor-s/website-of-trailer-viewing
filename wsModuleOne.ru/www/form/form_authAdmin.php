<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8"/>
	<link href="../css/form.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<?php 
		//$dataRes_authorizatiionAdminAPI = $_SESSION['data_auth'];
		//echo 'result: '.$dataRes_authorizatiionAdminAPI;
		$dataRes_authorizatiionAdminAPI = $_SESSION['data_auth'];
		if(!is_null($dataRes_authorizatiionAdminAPI)){
			echo "<script>console.log('Результат авторизации: ', ".$dataRes_authorizatiionAdminAPI["status_code"].");</script>";
		};
	?>
	<form name="formLogin" action="../php/script.php" method="post">
		<label>
			<p>Логин: <br>
			<input type="text" name="login"/>
			</p>
		</label>
		<label>
			<p>Пароль: <br>
			<input type="text" name="password"/>
			</p>
		</label>
		<input type="submit" name="done" value="Отправить"/>
	</form>
	<br/>
	<a href="../<?=$_SESSION['currentPage']?>" class="exitForm"><input type="submit" name="done" value="Вернуться назад"/></a> 
</body>
</html>