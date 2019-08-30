<?php
include_once("../php/data.php");

const NUM_PAGES = 6;//колличество блоков на одной странички пагинации

//$token = 'token_46346452';
//$data_request = array("req_0" => 'аниме', "req_1" => 'акира'); //вместо массива POST - для тестирования, временно
//print_r($data_request); echo "<br/>";
//print_r($_POST); echo "<br/>";
$token = $_POST["token"];
$data_request = array();
foreach($_POST as $key => $value){
	if( preg_match("/req/", $key) ){
		$data_request[$key] = $value;
	};
};

$modes = array(
	"requestOnSearch" => function($data_request, $token){
		$tmp_link = 'tmp/searchStory';
		
		$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//запускаем бд
		$mysqli->query("SET NAMES 'utf8'");
		$result_set = $mysqli->query("SELECT * FROM `data_stories`");
		$data_sql_story = printResult($result_set, null, "story_id"); //получаем список существующих историй
		$data_result = array();//объект с результатом проверки
		
		foreach($data_sql_story as $key_story => $value_story){//проверяем, принадлежат ли какой-либо истории какие-либо введенные теги 
			$lowKey_story = strtolower($key_story);
			$data_result[$lowKey_story] = array();
			
			$result_set = $mysqli->query("SELECT * FROM `".SETTING_CLIPPAGE_SUBSTR::TEMP_CREATETABLETAGS.$lowKey_story."`");
			$data_sql_tableTags = printResult($result_set, null, "story_id"); 
			
			foreach($data_sql_tableTags as $key_tagsTable => $value_tagsTable){
				foreach($data_request as $key_data => $value_data){
					$str_input = $value_tagsTable["tag_id"];
					$checked = preg_match("/$value_data/", $str_input);
					
					if( $checked ){
						$data_result[$lowKey_story][$value_data] = true;
						if( !isset($data_result[$lowKey_story]["body"]) ){ 
							$data_result[$lowKey_story]["body"] = $data_sql_story[$key_story];
						};
					}; 
				};	
			};
			if( count($data_result[$lowKey_story]) === 0 ){ unset($data_result[$lowKey_story]); };
		};	
		$mysqli->close();
		
		$parse_data_result = json_encode($data_result); //парсим результат поиска
		$file_cashDatabase_data_result = fopen($tmp_link.'/'."parse_data_result_".$token.".txt", "w+t"); //создаем новый файл
		fwrite($file_cashDatabase_data_result, $parse_data_result); //записываем туда наш парс
		fclose($file_cashDatabase_data_result);///закрываем	
		
		tmpSearch_garbage_collector($tmp_link); //чистит старые запросы
		
		$index = 0;
		foreach($data_result as $key => $value){ $index++; };
		$num_pagination = ceil($index / NUM_PAGES);
		
		$object_answer = array();
		$object_answer["countButtonPag"] = $num_pagination;
		$object_answer["body"] = array();
		//$num_startPage = 2; - это введенная кнопка пагинации
		//$i=($num_startPage * NUM_PAGES)-NUM_PAGES;  $i<$num_startPage * NUM_PAGES;  $i++ | $i = 0; $i < NUM_PAGES; $i++
		for( $i = 0; $i < NUM_PAGES; $i++ ){ //всегда начинает с первой странички пагинации
			$keyaArr = array_keys($data_result);
			$object_answer["body"][$keyaArr[$i]] = $data_result[$keyaArr[$i]];
		};	
		
		echo json_encode($object_answer);
		exit;
	},
	"requestOnAdditive" => function($data_request, $token){
		//$num_startPage = 2; - это введенная кнопка пагинации
		$tmp_link = 'tmp/searchStory';
		
		$cashFile = fopen($tmp_link.'/'."parse_data_result_".$token.".txt", "r");
		fseek($cashFile, 0);//устанавливаем регистр в начало файла
		$strCashFile = '';
		while(!feof($cashFile)){  //считываем данные файла
			$strCashFile = $strCashFile.fread($cashFile, 1);			
		};
		fclose($cashFile);
		$data_result = json_decode($strCashFile, true);
		
		$index = 0;
		foreach($data_result as $key => $value){ $index++; }; //тут все верно потому что мы берем data_result из парса от requestOnSearch
		$num_pagination = ceil($index / NUM_PAGES); //поэтому num_pagination будет одинаковый
		
		$keysArr = array_keys($data_result);
		$num_startPage = $_POST["buttonNumber"];
		$object_answer = array();
		$object_answer["countButtonPag"] = $num_pagination;
		$object_answer["buttonNumber"] = $num_startPage;
		$object_answer["body"] = array();
		for( $i=($num_startPage * NUM_PAGES)- NUM_PAGES;  $i<$num_startPage * NUM_PAGES;  $i++ ){ //всегда начинает с первой странички пагинации
			//$keysArr = array_keys($data_result);
			$object_answer["body"][$keysArr[$i]] = $data_result[$keysArr[$i]];
		};	
		echo json_encode( $object_answer );
	}
);
$modes[$_POST["modes"]]($data_request, $token);

//--------------------------------------------
function tmpSearch_garbage_collector($folder_adress){//если кэш файлы запросов долго не изменяли относительно времени жизни сессии - удаляет их
	$currentTime = time() - 3600;
	$data_file = scandir($folder_adress);
	
	foreach($data_file as $key => $value){
		if( preg_match("/.txt/", $value) ){
			$timeFile = filemtime($folder_adress.'/'.$value) - 3600;
			if( !(($timeFile + TIMESESSION) >= $currentTime) ){
				unlink($folder_adress.'/'.$value);
			};
		};
	};
};
?>