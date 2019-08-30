<?php
session_start();

define('LENKEYS_DELCOMMENT', 3);
/* Объект настроек для функции substr */
class SETTING_CLIPPAGE_SUBSTR{
	const TEMP_FOLDERSTORY = 'pagesStories/';
	const TEMP_FOLDERSTORY_LEN = 13;
	const TEMP_EXISTFILEPHP = '.php';
	const TEMP_EXISTFILEPHP_LEN = 4;
	const TEMP_APLICADMINFORSENDCOMMENT = 'admin_';
	const TEMP_APLICADMINFORSENDCOMMENT_LEN = 6;
	const TEMP_APLICADMINFORDELCOMMENT_LEN = 5;
	const TEMP_APLICKEYSDATABASEFORCREATESTORY = 'story_';
	const TEMP_APLICKEYSDATABASEFORCREATESTORY_LEN = 6;
	const TEMP_APLICKEYSSQLFORLOADCOMMENTINPAGESTORIES = 'name_';
	const TEMP_APLICKEYSSQLFORLOADCOMMENTINPAGESTORIES_LEN = 5;
	const TEMP_CREATETABLETAGS = 'tags_';
	const TEMP_CREATETABLETAGS_LEN = 5;
	const TEMP_TAGKEY = 'tag_';
	const TEMP_TAGKEY_LEN = 4;
}
new SETTING_CLIPPAGE_SUBSTR;
const TIMESESSION = 1800; //30 минут - если время жизни сессии другое, то нужно поменять значение
//--------------------------------------------
function getToken(){//присваивание пользователю токена
	$numLength_token = strlen(getrandmax());//token_ + NNNNNNNNNNNN , где N-это число
	$loop = 2;
	$getNumToken = 'token_';
	
	for($i = 0; $i < $loop; $i++){
		$getNumToken .= rand(0, getrandmax());
	};
	
	if( !isset($_SESSION["user_token"])){
		$_SESSION["user_token"] = $getNumToken;
	};
};
getToken();
//--------------------------------------------
function printResult($result_set, $id, $keyID){//функция вывода данных таблицы базы данных в переменную-объект
	//$result_set - выведенная база данных, $id - конкретная строка бд(без нее выведется вся бд), $keyID - 
	$dataObject = Array();
	$count = 0;
	while( ($row = $result_set->fetch_assoc()) != false ){
		//наполняем объект со всеми строками из бд
		$objRow = Array();
		foreach($row as $key => $value){
			$objRow[$key] = $row[$key];
		};
		
		if(isset($dataObject[$row[$keyID]])){
			$dataObject[$row[$keyID].'||'.$count] = $objRow;
		}else{ $dataObject[$row[$keyID]] = $objRow; };
		
		$count++;
	};
	if(gettype($id)=='string'){ 
		//потом выводим или нужную строку или сам объект
		return $dataObject["$id"];
	}else{ return $dataObject; };
};

function addCurrentTime(){//функция вывода текущего времени
	date_default_timezone_set("UTC"); // Устанавливаем часовой пояс по Гринвичу
	$time = time(); // Вот это значение отправляем в базу
	$offset = 3; // Допустим, у пользователя смещение относительно Гринвича составляет +3 часа
	$time += $offset * 3600; // Добавляем 3 часа к времени по Гринвичу
	return date('Y/F/d H:i:s', $time);
};
function createFileName($str){ //функция преобразования русских символов в латиницу
	$valueSave_str = $str;
	$data_simbol = array("А" => 'A', "Б" => 'B', "В" => 'V', "Г" => 'G', "Д" => 'D', "Е" => 'E', "Ё" => 'E', "Ж" => 'G', "З" => 'Z', "И" => 'I', "Й" => 'Y', "К" => 'K', "Л" => 'L',
	"М" => 'M', "Н" => 'N', "О" => 'O', "П" => 'P', "Р" => 'R', "С" => 'S', "Т" => 'T', "У" => 'U', "Ф" => 'F', "Х" => 'H', "Ц" => 'TZ', "Ч" => 'CH', "Ш" => 'SH', "Щ" => 'SCH',
	"Ъ" => '', "Ы" => 'I', "Ь" => 'I', "Э" => 'A', "Ю" => 'U', "Я" => 'Y', "а" => 'a', "б" => 'b', "в" => 'v', "г" => 'g', "д" => 'd', "е" => 'e', "ё" => 'e', "ж" => 'g', "з" => 'z', "и" => 'i', "й" => 'y', "к" => 'k', "л" => 'l',
	"м" => 'm', "н" => 'n', "о" => 'o', "п" => 'p', "р" => 'r', "с" => 's', "т" => 't', "у" => 'u', "ф" => 'f', "х" => 'h', "ц" => 'tz', "ч" => 'ch', "ш" => 'sh', "щ" => 'sch',
	"ъ" => '', "ы" => 'i', "ь" => 'i', "э" => 'a', "ю" => 'u', "я" => 'y', " " => '%', "-" => '-', "0" => '0', "1" => '1', "2" => '2', "3" => '3', "4" => '4', "5" => '5'
	, "6" => '6', "7" => '7', "8" => '8', "9" => '9', "?" => '?', "!" => '!', "." => '.', "," => ',');
	
	$result = '';
	for($i=0;$i<strlen($str);$i++){
		for($j=0;$j<count($data_simbol);$j++){
			$result = $result.$data_simbol[strtolower(substr($str, 0, 2))];
			$str = substr($str, 1, strlen($str)-1);
		};
	};
	if(strlen($result) == 0 || $result == false || $result == '' || is_null($result) || $result == ' '){
		return $valueSave_str;
	}else{ return $result; };
};
function helper_downloadComment($data, $keysData, $id_story){//функция загрузки комментариев
	global $dataRes_authorizatiionAdminAPI; 
	//new SETTING_CLIPPAGE_SUBSTR;
	
	foreach($data as $key => $value){
		if($data[$key][$keysData[0]] == $id_story){
			echo "<div class='commentBlock' name='".substr($keysData[1], SETTING_CLIPPAGE_SUBSTR::TEMP_APLICKEYSSQLFORLOADCOMMENTINPAGESTORIES_LEN)."'>
				<span class='commentBlock_span'>".$data[$key][$keysData[1]]."</span>
				<p class='commentBlock_p' onclick='openComment.call(openComment, this)'>".$data[$key][$keysData[2]]."</p>";
				if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"] == 'admin1_token_id78633'){
					echo "<button id='buttComDelete' onclick='choiceDeleteComment.call(choiceDeleteComment, this)'>&#10006;</button>";
				};
			echo "</div>";
		};
	};
};
?>