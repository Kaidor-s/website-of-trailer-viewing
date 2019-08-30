<?php 
	session_start();
	
	$numberNumber = 32;
	$dataSett = array(
		"administrator" => array(
			//настройки для администратора
			"status_code" => 200,
			"status_text" => 'Successful authorization',
			"body" => array(
				"status" => true,
				"token" => "",
				"nameAdmin" => '',
			)
		),
		"user" => array(
			//настройки для пользователя
			"status_code" => 400,
			"status_text" => 'Invalid authorization data',
			"body" => array(
				"status" => false,
				"message" => "Invalid authorization data"
			)
		)
	);
	
	function printResult_auth($result_set){
		$dataObject = Array();
		while( ($row = $result_set->fetch_assoc()) != false ){
			$dataObject['admin_'.$row["id"]] = Array();
			$dataObject['admin_'.$row["id"]]["login"] = $row["login"];
			$dataObject['admin_'.$row["id"]]["password"] = $row["password"];
			$dataObject['admin_'.$row["id"]]["token"] = $row["token"];
			$dataObject['admin_'.$row["id"]]["mail"] = $row["mail"];
			$dataObject['admin_'.$row["id"]]["name"] = $row["name"];
		};
		return $dataObject;
	};
	
	
	$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
	$mysqli->query("SET NAMES 'utf8'");
	//if ($mysqli->connect_errno) { die('Ошибка соединения: ' . $mysqli->connect_error); }else{echo 'Connect true';}
	
	$result_set = $mysqli->query("SELECT * FROM `data_authorizationadmin`");
	//var_dump($result_set);
	$data = printResult_auth($result_set);
	$mysqli->close();
		
	function checkData($dataVal){
		$result = array(); //"counter" => 0, "data"
		global $keyResult;
		
		foreach($dataVal as $keyData => $valueData){
			$result[$keyData] = array();//создаем массив для текущего проверяемого админа
			$result[$keyData]["counter"] = 0; //добавляем текущему админу результирующие свойста
			$result[$keyData]["dataSett"] = ""; 
			
			foreach($valueData as $keyValD => $valueValD){
				if($keyValD != "token" && $keyValD != "dataSett" && $keyValD != "mail" && $keyValD != "name"){
					if($_POST[$keyValD] == $valueValD){ //если пароль и логин в post равен паролю в dataVal
						$result[$keyData]["counter"] = $result[$keyData]["counter"] += 1;
						$keyResult = $keyData;
					}else{ //если нет
						unset( $result[$keyData] ); //удаляем админа из результирующего массива
						
						break;
					};	
				};
			};
		};	
		return $result;
	};
	
	if(isset($_POST["password"]) && isset($_POST["login"])){
		$result = checkData($data);
		
		if(count($result) != 0){ 
			foreach($result as $key => $value){
				$dataSett["administrator"]["body"]["token"] = $data[$key]["token"];
				$dataSett["administrator"]["body"]["nameAdmin"] = $data[$key]["name"];
				$dataSett["administrator"]["body"]["mail"] = $data[$key]["mail"];
			};
			$_SESSION["data_auth"] = $dataSett["administrator"];
			header("Location: ../".$_SESSION['currentPage']);	 //если все правильно возвращает на главную страницу		
		}else{
			$_SESSION["data_auth"] = $dataSett["user"];
			
			header("Location: ../form/form.php"); //если не правильно - возвращает на форму
		};	
		
		exit;
	};
?>