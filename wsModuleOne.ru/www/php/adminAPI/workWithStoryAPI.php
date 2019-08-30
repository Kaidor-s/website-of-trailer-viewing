<?php
session_start();
include_once("../../php/data.php");

$dataSett = Array(//объект с информационными параметрами
	"create_story" => Array(
		"true" => Array(
			"status_code" => 201,
			"status_text" => 'Successful creation',
			"body" => Array( "status" => true, "post_id" => '', ),
		),
		"false" => Array(
			"status_code" => 401,
			"status_text" => 'Creating error',
			"body" => Array( "status" => false, "message" => '', ),
		)
	),
	"eddition_story" => Array(
		"true" => Array(
			"status_code" => 202,
			"status_text" => 'Successful creation',
			"body" => Array(
				"status" => true,
				"post" => array( "title" => '', "date" => '', "anons" => '', "text" => '', "tags" => "", "image" => '', ),
			),
		),
		"false" => Array(
			"status_code" => 402,
			"status_text" => 'Creating error',
			"body" => Array( "status" => false, "message" => '', ),
		),
		"notFound" => array(
			"status code" => 4020,
			"status text" => 'Post not found',
			"body" => array( "message" => 'Post not found', ),
		)
	)
);
$idCurrentStory = substr($_SESSION['currentPage'], SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN,
strlen($_SESSION['currentPage']) - SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN);
//---------------------------------------------------------------------------------------------
foreach($_POST as $key => $value){//проводим очистку от лишних пробелов
	$_POST[$key] = trim($value);
	if($key != 'tags'){
		$_POST[$key] = htmlspecialchars($value);
	};
};

if( !is_null($_POST["done_eDF"]) ){
	//echo $_POST["done_eDF"];
	$funcManager; $result_WWT;
	
	if($_POST["done_eDF"] == '+'){ //создание сюжета
		$funcManager = function($data_sql, $mysqli){
			global $dataSett;
			
			$id_story = "newStory";//по хорошемму это надо сделать константой, хз
			$_SESSION['currentPage'] = "pagesStories/".$id_story.".php"; //при создании страницы мы сохраняем в сессии_текущей_страницы newStory
			
			if(!file_exists('../../pagesStories/newStory.php')){//если файла не существует
				$file_templateStories = fopen("../../templates/pageStory.php", "r+t");//открываем файл-шаблон
				fseek($file_templateStories, 0);//устанавливаем регистр в начало файла
				$strFile = '';
				while(!feof($file_templateStories)){  //считываем данные файла
					$strFile = $strFile.fread($file_templateStories, 1);			
				};
				fclose($file_templateStories); //закрываем файл
				
				$file_newPage = fopen("../../pagesStories/newStory.php", "a+t"); //создаем новый файл
				fwrite($file_newPage, $strFile); //записываем туда шаблон
				fclose($file_newPage);///закрываем	
				
				//добавляем новую страницу истоии в data_stories
				$resultSQLCommand_createStory = $mysqli->query("
					INSERT INTO `dataadminapi`.`data_stories` (
						`story_id`,`story_date_creation`, `story_link`
					) VALUES ('".$id_story."', '".addCurrentTime()."', '".$_SESSION['currentPage']."');"
				);
				
				$result_WWT = workWithTags($data_sql, $mysqli, 'create', $id_story, null);
			};
		};
	};
	if($_POST["done_eDF"] == 'Изменить сюжет'){ //изменение сюжета
		$funcManager = function($data_sql, $mysqli){
			global $idCurrentStory, $dataSett;
			$newName = createFileName($_POST['title']);
			$newName = preg_replace("/\s/", '_', $newName);
			$newName = $newName.'_story';
			$previousName = $data_sql["story_id"]; 
			
			$resultCommandSQL_str = createCommandSQL_edditionStory($_POST, $data_sql); //тут мы должны исключать story_tags
			$resultSQLCommand_createStory = $mysqli->query(//меняем данные таблицы бд
				"UPDATE  `dataadminapi`.`data_stories` SET ".$resultCommandSQL_str." WHERE `data_stories`.`story_id` =  '".$idCurrentStory."'".';'
			);	
			//меняем название файла на то, которое указанно в POST["title"]
			if( isset($_POST["title"]) && !is_null($_POST["title"]) && $_POST["title"] != false && $_POST["title"] != ' '){
				rename("../../".$_SESSION['currentPage'], '../../pagesStories/'.$newName.'.php');
				$_SESSION['currentPage'] = 'pagesStories/'.$newName.'.php';
				$resultSQLCommand_createStory = $mysqli->query(//смена имени файла экземпляра шаблона в бд
					"UPDATE  `dataadminapi`.`data_stories` SET  `story_id` =  '".$newName."', `story_link` = '".$_SESSION['currentPage']."', `story_date_eddition` = '".addCurrentTime()."'
					WHERE  `data_stories`.`story_id` = '".$previousName."';"
				);	
			};
			
			//работа с тегами
			workWithTags($data_sql, $mysqli, 'eddition', $idCurrentStory, $newName); //сначала переименовываем историю а уже потом таблицу тегов
			
			if($resultSQLCommand_createStory == true){//отправляем ответные данные через сессию(надо сделать через ajax)
				foreach($dataSett["eddition_story"]["true"]["body"]["post"] as $key => $val){
					$dataSett["eddition_story"]["true"]["body"]["post"][$key] = $data_sql["story_".$key];
				};
				$_SESSION["data_resultEdditionStory"] = $dataSett["eddition_story"]["true"];
			}else{  $_SESSION["data_resultEdditionStory"] = $dataSett["eddition_story"]["false"]; };
		};
	};
	if($_POST["done_eDF"] == 'удалить'){ //удаление сюжета
		$funcManager = function($data_sql, $mysqli){
			global $dataSett;
			
			$mysqli->query("DELETE FROM `dataadminapi`.`data_stories` WHERE `data_stories`.`story_id` = '".
			substr($_POST["link"], SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN, 
			strlen($_POST["link"]) - SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN)."';");
			unlink("../../".$_POST["link"]);
			
			$result_WWT = workWithTags(
				$data_sql,
				$mysqli,
				'delete',
				substr($_POST["link"], SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN,
					strlen($_POST["link"]) - SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN),
				null
			);
		};
	};
	
	if(is_uploaded_file($_FILES["image"]["tmp_name"])){//Если файл загружен успешно, перемещаем его из временной директории в конечную
		move_uploaded_file($_FILES["image"]["tmp_name"], "N:/home/wsModuleOne.ru/www/uploads/".$_FILES["image"]["name"]);
		$_POST["image"] = '../uploads/'.$_FILES["image"]["name"];
	}; //else { echo("Ошибка загрузки файла!"); };
	
	$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
	$mysqli->query("SET NAMES 'utf8'");
	$result_set = $mysqli->query("SELECT * FROM `data_stories`");
	$data_sql = printResult($result_set, $idCurrentStory, "story_id"); //получаем данные из бд
	$funcManager($data_sql, $mysqli);//исполняем выбранную функцию
	$mysqli->close();
};

//---------------------------------------------------------------------------------------------
function createCommandSQL_edditionStory($input_data, $sql_data){ //функция изменения сюжета
	//генерирует часть SQL командуы
	$str = '';
	foreach($sql_data as $key => $value){
		/*
			Мы берем ключ таблицы бд - это $key, и обрезаем от него story в $resData. Если результат обрезания существует - 
			то мы просто добавляем значение из input_data(это POST) по обрезанному ключу из таблицы. Если такого ключа в POST нет - соответственно
			ниче и не обавится, и выведется пустая строка. 
		*/
		$resData = $input_data[substr($key, SETTING_CLIPPAGE_SUBSTR::TEMP_APLICKEYSDATABASEFORCREATESTORY_LEN,
		strlen($key) - SETTING_CLIPPAGE_SUBSTR::TEMP_APLICKEYSDATABASEFORCREATESTORY_LEN)];
		if( !is_null( $resData ) && $resData != '' && $resData!=' '){
			$str = $str."`$key` = '".$resData."', "; //склеиваем значения в строку
		};
	};
	$str = substr($str, 0, strlen($str) - 1 - 1); //убираем конечную лишнюю запятую и пробел
	return $str;
};
function workWithTags($data_sql, $mysqli, $commandText, $idCurrentStory, $newId){//функция работы с тегами
	global $dataSett;
	//$_POST["tags"] = trim($_POST["tags"]); //удаляем лишние пробелы
	
	$data = array(//коллекция функций по ключу $commandText
		"create" => function($data_sql, $mysqli, $idCurrentStory, $newId){//работа с таблицей тегов при создании таблицы сюжета
			//создаем таблицу тегов для нового сюжета из data_stories
			$mysqli->query("
				CREATE TABLE `dataadminapi`.`tags_".strtolower($idCurrentStory)."`(
					`story_id` VARCHAR(64) NOT NULL,
					`tag_id` VARCHAR(128) NOT NULL,
				PRIMARY KEY(`story_id`, `tag_id`));
			");
			//устанавливаем кодировку таблицы такую же как и в data_stories
			$mysqli->query("
				ALTER TABLE `dataadminapi`.`tags_".strtolower($idCurrentStory)."`
				CHANGE  `story_id` `story_id` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE `tag_id` `tag_id` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
			");
			//устанваливаем отношение таблицы тегов с data_stories.story_id
			$mysqli->query("
				ALTER TABLE  `dataadminapi`.`tags_".strtolower($idCurrentStory)."` 
				ADD FOREIGN KEY (  `story_id` ) REFERENCES  `dataadminapi`.`data_stories` (
					`story_id`
				) ON DELETE CASCADE ON UPDATE CASCADE ;
			");
			return true;
		},
		"eddition" => function($data_sql, $mysqli, $idCurrentStory, $newId){//работа с таблицей тегов при изменении сюжета
			$name = $newId;
			//echo 'func: '.$idCurrentStory.' '.$newId."<br/>";
			
			//если изменилось название сюжета - переименовываем таблицу тегов, если нет - меняем $name на текущее название сюжета
			if( !is_null($_POST["title"]) && $_POST["title"]!='' && $_POST["title"]!=' ' ){
				$mysqli->query("
					RENAME TABLE  `dataadminapi`.`tags_".strtolower($idCurrentStory)."` TO  `dataadminapi`.`tags_".strtolower($name)."`
				");
			}else{ $name = $idCurrentStory; };
			
			if( !is_null($_POST["tags"]) && $_POST["tags"]!='' && $_POST["tags"]!=' ' ){
				//очищаем таблицу тегов
				$mysqli->query("
					TRUNCATE TABLE `dataadminapi`.`tags_".strtolower($name)."`
				"); 
				
				$tagsArray = json_decode($_POST["tags"]);
				//заполняем таблицу тегов тегами из $_POST["tags"]
				foreach($tagsArray as $key => $value){
					$mysqli->query("
						INSERT INTO  `dataadminapi`.`tags_".strtolower($name)."` (
							`story_id` ,
							`tag_id`
						) VALUES ('".$name."', '".$value."');
					");
				}; 	
			}else{ return false; };
			return true;
		},
		"delete" => function($data_sql, $mysqli, $idCurrentStory, $newId){//работа с таблицей тегов при удалении сюжета
			//удаляем таблицу тегов для соответствующего сюжета из data_stories
			$mysqli->query("
				DROP TABLE `dataadminapi`.`tags_".strtolower($idCurrentStory)."`;
			");
			return true;
		}
	);
	
	$result = $data[$commandText]($data_sql, $mysqli, $idCurrentStory, $newId);
	return $result;
};
//---------------------------------------------------------------------------------------------

header("Location: ../../".$_SESSION['currentPage']);
exit;
?>