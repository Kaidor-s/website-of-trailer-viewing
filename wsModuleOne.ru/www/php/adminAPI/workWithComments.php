<?php
/*
- Пользователь ранее отправивший имя с почтой больше не может изменить свое имя, а если введет не верное имя - отправить комментарий не получится
- сделать data_sett по методичке первого задания в ворлд скилс, отправлять его через сессию, как и другие, только эту инфу мы будем использовать:2
при получении ошибки - срабатывает на всех правах(пользователя и админа) скрипт, который добавляет поверх формы отправки комента окно с предупреждением об ошибке и кнопку "закрыть" или "ок"
- сделать функцию "подсказать имя" - отправляет имя на почту, если почта существует
*/
session_start();
include_once("../../php/data.php");
if( isset($_POST["text_comment"]) ){ $_POST["text_comment"] = htmlspecialchars($_POST["text_comment"]); };

//---------------------------------------
$dataSett = array(
	"comment_send" => array(
		"status_code" => 203,
		"status_text" => "Successful creation",
		"body" => array(
			"status" => true,
		),
	),
	"comment_error_exiistName" => array(
		"status_code" => 4030,
		"status_text" => "Creating error",
		"body" => array(
			"status" => false,
			"message" => 'Введенное имя существует, придумайте новое имя!',
		),
	),
	"comment_error_notEquality" => array(
		"status_code" => 4031,
		"status_text" => "Creating error",
		"body" => array(
			"status" => false,
			"message" => 'Ваше имя не принадлежит введенной почте! Введите правильное имя!',
		),
	),
	"comment_delete" => array(
		"status_code" => 205,
		"status_text" => "Successful delete",
		"body" => array(
			"status" => true,
			"message" => 'Комментарий удален!',
		),
	),
	"comment_error_incorrectData" => array(
		"status_code" => 405,
		"status_text" => "Delete error",
		"body" => array(
			"status" => false,
			"message" => 'Комментаррий не удаленн, некорректные входные данные!',
		),
	),
);
//---------------------------------------
if(!is_null($_SESSION['data_auth']) && $_SESSION['data_auth']["body"]["token"] == 'admin1_token_id78633'){//если пишет админ
	if($_POST["type"] == 'comment_send'){
		$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
		$mysqli->query("SET NAMES 'utf8'");
		$result_set = $mysqli->query("
			INSERT INTO  `dataadminapi`.`data_comments_administrator` ( `id_comment`, `id_admin`, `id_story`, `name_admin`, `text_comment`, `date_comment`) VALUES (
				NULL, '".substr($_POST["admin_name"], SETTING_CLIPPAGE_SUBSTR::TEMP_APLICADMINFORSENDCOMMENT_LEN,
				strlen($_POST["admin_name"]) - SETTING_CLIPPAGE_SUBSTR::TEMP_APLICADMINFORSENDCOMMENT_LEN)."',
				'".substr($_SESSION['currentPage'], SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN,
				strlen($_SESSION['currentPage']) - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN - SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN)."', 
				'".$_POST["admin_name"]."', '".$_POST["text_comment"]."', '".addCurrentTime()."'
			)
		");
		
		$mysqli->close();
	};
	if($_POST["type"] == 'comment_delete'){
		$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
		$mysqli->query("SET NAMES 'utf8'");
		$result_set = $mysqli->query("SELECT * FROM `data_comments".$_POST["type_person"]."`");
		$data = printResult($result_set, null, "id_comment");
		
		function loopCheck(){
			global $data;
			$checkResult = array(); $counterResult = 0; 
			
			foreach($data as $row => $string){
				foreach($data[$row] as $key => $value){
					if($value == $_POST[$key]){
						$checkResult[$key] = $value;
						$counterResult++;
						
						if($counterResult == LENKEYS_DELCOMMENT){ return $checkResult; };
					}else{ $checkResult[$key] = $value; $counterResult = 0;};
				};
			};	
			return null;
		};
		$result = loopCheck();
		if( !is_null($result) ){
			$keys_result = array_keys($result);
			$mysqli->query("DELETE FROM `dataadminapi`.`data_comments".$_POST["type_person"]."`
			WHERE `data_comments".$_POST["type_person"]."`.`".$keys_result[0]."` = ".$result[$keys_result[0]].";");
			
			$_SESSION["data_resultDeleteComment"] = $dataSett["comment_delete"];
		}else{
			$_SESSION["data_resultDeleteComment"] = $dataSett["comment_error_incorrectData"];
			header("Location: ../../".$_SESSION['currentPage']);
			exit;
		};
		
		$mysqli->close();
	};
}else{//если пишет пользователь
	$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
	$mysqli->query("SET NAMES 'utf8'");
	
	$result_set = $mysqli->query("SELECT * FROM `data_users`");
	$data_users = printResult($result_set, null, "user_id");
	$result_check = checkUser($data_users, $_POST["user_mail"], $_POST["user_name"]); //проверка первичных данных
	
	if($result_check["result"]==false){//если пользователь несуществует - добавляем нового пользователя
		if( !checkExistName($data_users, $_POST["user_name"]) ){ //если введенное пользователем имя уже существует
			$mysqli->query("INSERT INTO `dataadminapi`.`data_users` (`user_id`, `user_name`, `user_mail`, `date_regist`) VALUES (
			'', '".$_POST["user_name"]."', '".$_POST["user_mail"]."', '".addCurrentTime()."');");
			
			$result_set = $mysqli->query("SELECT * FROM `data_users`"); //обновляем переменные с данными
			$data_users = printResult($result_set, null, "user_id");
			$result_check = checkUser($data_users, $_POST["user_mail"], $_POST["user_name"]); //проверка обновленных данных	
		}else{ //возврат с ошибкой
			$_SESSION["data_resultCreateComment"] = $dataSett["comment_error_exiistName"];
			header("Location: ../../".$_SESSION['currentPage']);
			exit;
		};
	};
	//отправляем запрос к бд
	$result_set = $mysqli->query("
		INSERT INTO  `dataadminapi`.`data_comments` ( `id_comment`, `id_user`, `id_story`, `name_user`, `text_comment`, `date_comment`) VALUES (
			NULL , '".$result_check["id"]."', '".substr($_SESSION['currentPage'], SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN,
			strlen($_SESSION['currentPage']) - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN - SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN)."',
			'".$_POST["user_name"]."',
			'".$_POST["text_comment"]."', '".addCurrentTime()."'
		) 
	");
	
	$mysqli->close();
};

//---------------------------------------
function checkUser($data_users, $mail_currentUser, $name_currentUser){//функция проверки существования пользователя
	global $_SESSION, $dataSett;

	$array = array();
	foreach($data_users as $key_string => $value_string){
		$array["id"] = $key_string; //идентификатор пользователя
		foreach($data_users[$key_string] as $key_user => $value_user){
			if($key_user == 'user_mail' && $value_user == $mail_currentUser){ //если почта существует
				if($data_users[$key_string]['user_name'] == $name_currentUser){ //если имя соответствует почте
					$array["result"] = true;
					return $array;	
				}else{
					$_SESSION["data_resultCreateComment"] = $dataSett["comment_error_notEquality"];
					header("Location: ../../".$_SESSION['currentPage']);
					exit;
				};
			};
		};
	};
	
	$array["result"] = false;
	$array["id"] = null;
	return $array;
};

function checkExistName($data_users,  $name_currentUser){
	foreach($data_users as $key_string => $value_string){
		if($data_users[$key_string]["user_name"] === $name_currentUser){
			return true;
		};
	};
	return false;
};
//---------------------------------------
$_SESSION["data_resultCreateComment"] = $dataSett["comment_send"];
header("Location: ../../".$_SESSION['currentPage']);
?>