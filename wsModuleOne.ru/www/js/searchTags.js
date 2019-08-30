/* 
	задача 1: если время сессии истечет и файл с данными удалится, а токен сгорит - как обновить данные?
	задача 4: Проблема выборки регулярками - при нажатии одной буквы выдает все значения, которые содержат эту букву, а не те значения,
	на которые они начинаются
	
	.......................................
	ВЫОЛНЕННО:
	- задача 2: Сделать троеточие кнопок погинации если их слишком много. Три точки не являются кнопкой.	
		Пример: 1 2 3 4 ... 16
		при этом при переходе на кнопку 3 итог будет:
		2 3 4 5 ... 16 , то есть цифры двигаются вперед.
	- задача 3: при новом запросе удалять результаты старого
*/

function requestSearchTegs(tags, mode, token){//главная функция
	if(typeof mode != 'string') return false;
	
	let modes = {
		"requestOnSearch": function(){ //формируем объект данных поисковой строки
			this.deletePrevRequest.call(this, mode);
			
			if(typeof tags != 'string') return false;
			tags+=' ';//добавляем последним символом строки пробел чтобы не было ошибки в requestOnSearch
		
			let regExp = /[\-а-яА-Я\w\#]+[,\s\.]/ig;
			let tegsArr = tags.match(regExp);
			if(tegsArr == null || tegsArr.length==0) return false;
			let tegsObj = {};
			tegsArr.forEach(function(item, i, arr){
				item = item.toLowerCase();//переводим все результаты выборки в нижний регистр
				tegsObj['req_'+String(i)] = item.substr(0, item.length - 1);
			});
			tegsObj["token"] = token; //добавляем токен пользователя в запрос
			tegsObj["modes"] = 'requestOnSearch';
			
			//делаем запрос
			this.request.call(this, tegsObj, function(data){//this == requestSearchTegs //делаем запрос
				//передаем функцию которая в случае ответа от сервера с данными выполняет нужные нам действия
				if( this.checkedOfNullInDataRequest(data) ){
					let areaButtonsPag = document.getElementsByClassName("paginationArea");
					
					let obj = this.createButtons.call(this, areaButtonsPag, token, data);
					for(let i=0; i<areaButtonsPag.length; i++){
						for(let key in obj[i]){
							areaButtonsPag[i].appendChild(obj[i][key])
						};
					};
					
					this.addResultSearchTags(data.body); //добавляем блоки	
				};
			}.bind(this)); 
		}.bind(this),
		"requestOnAdditive": function(){ //формируем запрос на получение новой партии данных
			let tegsObj = {};
			tegsObj["token"] = token; //добавляем токен пользователя в запрос
			tegsObj["modes"] = 'requestOnAdditive';
			tegsObj["buttonNumber"] = function(){//получаем значение выбранной пользователем кнопки пагинации
				for(let i=0; i<this.arguments.length; i++){
					if(
						typeof this.arguments[i] == 'object' && 
						this.arguments[i] != null && 
						this.arguments[i].hasOwnProperty != null && 
						this.arguments[i].hasOwnProperty != undefined && 
						this.arguments[i].hasOwnProperty('button')
					){
						let result = this.arguments[i]['button'].innerText;
						if(typeof result != 'string' || result.search(/\d/i) == -1){
							result = '0';
						};
						return Number(result);
					};
				};
			}.call(this);
			
			if(tegsObj["buttonNumber"] == this.settings.currentPage){ 
				//если заходим на одну и ту же страничку то для оптимизации не делаем никаких запросов
				return false; 
			}else{ 
				this.settings.currentPage = tegsObj["buttonNumber"];
				this.deletePrevRequest(mode);
				
				this.request.call(this, tegsObj, function(data){//делаем запрос
					if( this.checkedOfNullInDataRequest(data) ){
						let areaButtonsPag = document.getElementsByClassName("paginationArea");
						
						let obj = this.createButtons.call(this, areaButtonsPag, token, data, Number(data.buttonNumber)/* 3 */);
						for(let i=0; i<areaButtonsPag.length; i++){
							for(let key in obj[i]){
								areaButtonsPag[i].appendChild(obj[i][key])
							};
						};
						
						this.addResultSearchTags(data.body); //добавляем блоки	
					};
				}.bind(this)); 
			};
		}.bind(this),
	};
	modes[mode]();
	
	return true;
};
requestSearchTegs.request = function(objReq, func){//универсальная функция, посылает запрос
	
	if(typeof objReq != 'object') return false;

	let boundary = String(Math.random()).slice(2);
	let boundaryMiddle = '--' + boundary + '\r\n';
	let boundaryLast = '--' + boundary + '--\r\n'
	let body = ['\r\n'];
	for (let key in objReq) {
		// добавление поля
		body.push('Content-Disposition: form-data; name="' + key + '"\r\n\r\n' + objReq[key] + '\r\n');
	}
	body = body.join(boundaryMiddle) + boundaryLast;
	
	// Тело запроса готово, отправляем
	let xhr = new XMLHttpRequest();
	xhr.open('POST', '../php/script_searchTags.php', true);
	xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
	
	xhr.onreadystatechange = function(){
		if (this.readyState != 4) return;
		
		if(xhr.status == 200){
			//console.log('responseText:', xhr.responseText);
			data = JSON.parse(xhr.responseText);
			//console.log('data:', data);
			
			if(data!=undefined && func!=undefined){//добавляем кнопочки
				func(data);
			};
		}else{ console.log('Error', xhr.status, xhr.statusText); };	
	}; 
	
	xhr.send(body);
};
requestSearchTegs.addResultSearchTags = function(data){//функция-шаблонизатор создания структуры блоков по data
	
	let contentResultSearch = document.getElementById("elems_resultSearch");
	//добавляем готовые блоки в объект
	let obj = {};
	for(let key in data){
		if( data[key] != null){
			let storyBlock = document.createElement("a");
			storyBlock.href = data[key].body.story_link;
			storyBlock.className = 'resulrRequestBlock';
			if(typeof this.settings.tmp.heightBlock == 'number'){ storyBlock.style.height = String(this.settings.tmp.heightBlock)+"px"; };
			
			let img = document.createElement("img");
			img.src = data[key].body.story_image.replace(/\.\.\//g, '');
			storyBlock.appendChild(img);
			
			let text_content = document.createElement("div");
			text_content.className = 'text_content';
			storyBlock.appendChild(text_content);
			
			let header = document.createElement("p");
			header.className = 'header';
			header.innerText = data[key].body.story_title;
			text_content.appendChild(header);
			
			let dateVal = document.createElement("span");
			dateVal.className = 'dateVal';
			dateVal.innerText = data[key].body.story_anons;
			text_content.appendChild(dateVal);
			
			let descriprionText = document.createElement("p");
			descriprionText.className = 'descriprionText';
			descriprionText.innerText = data[key].body.story_text;
			text_content.appendChild(descriprionText);
			
			let elem = document.createElement("div");
			elem.innerText = '...';
			elem.id = 'points';
			text_content.appendChild(elem);
			
			obj[key] = storyBlock;	
		};
	};
	
	//отображаем конечный объект с блоками
	for(let key in obj){
		contentResultSearch.appendChild(obj[key]);
	};	
	
	if(typeof this.settings.tmp.heightBlock != 'number'){
		this.settings.tmp.heightBlock = obj[Object.keys(obj)[0]].getBoundingClientRect().height;
	};
};
requestSearchTegs.deletePrevRequest = function(mode){//функция очистки предыдущего результата поиска
	let areaButtonsPag = document.getElementsByClassName("paginationArea");
	let contentResultSearch = document.getElementById("elems_resultSearch");
	
	let modes = {
		"requestOnSearch": function(){
			for(let i=0; i<areaButtonsPag.length; i++){
				areaButtonsPag[i].innerHTML = '';
			};
			if(contentResultSearch != null) contentResultSearch.innerHTML = '';
			this.settings.tmp = {};
			this.settings.currentPage = 1;
		}.bind(this),
		"requestOnAdditive": function(){
			for(let i=0; i<areaButtonsPag.length; i++){
				areaButtonsPag[i].innerHTML = '';
			};
			contentResultSearch.innerHTML = '';	
		}.bind(this),
	};
	modes[mode]();	
};
requestSearchTegs.createButtons = function(areaButtonsPag=[], token, data={countButtonPag: 0,}, numPage=1){//функция создания кнопок пагинации
	if(
		typeof data.countButtonPag != 'number' ||
		typeof numPage != 'number' ||
		numPage > data.countButtonPag
	) return false;
	
	let flag = false;
	let buttonsObj = {}, resultObj = {}, obj = {};
	
	for(let i=0; i<areaButtonsPag.length; i++){//добавляем кнопки пагинации
		buttonsObj[String(i)] = {};
		for(let j=1; j<=data.countButtonPag; j++){
			let buttonElem = document.createElement("button");
			buttonElem.innerHTML = j;
			buttonElem.className = 'buttonPagination';
			buttonElem.onclick = this.bind(this, null, 'requestOnAdditive', token, {"button": buttonElem});
			buttonsObj[String(i)][String(j)] = buttonElem;
		};
	};
	
	// for(let i=0; i<areaButtonsPag.length; i++){//искусственно добавляем кнопки пагинации для теста
		// for(let j=3; j<=10; j++){
			// let buttonElem = document.createElement("button");
			// buttonElem.innerHTML = j;
			// buttonElem.className = 'buttonPagination';
			// buttonsObj[String(i)][String(j)] = buttonElem;
		// };
	// };
	
	for(let index=0; index<areaButtonsPag.length; index++){
		resultObj[String(index)] = {};
		for(let key in buttonsObj[String(index)]){
			if( Number(key) > this.settings.maxLength ){//если колличество кнопок пагинации больше допустимого
				let i = numPage;
				if(i == 1){i=2; numPage=2;};
				
				
				//тут мы делаем сноску на последнюю страницу если слишком много кнопочек
				for(let j = i-1; j < (this.settings.maxLength + numPage) - 1; j++ ){ 
					resultObj[String(index)][String(j)] = buttonsObj[String(index)][String(j)];
					i++;
				};
				let index_skips = 0;
				for(let key in resultObj[String(index)]){
					if(resultObj[String(index)][key] == undefined){
						index_skips++;
						delete resultObj[String(index)][key];
					};
				};
				if(index_skips != 0){
					//если нажата страничка почти близкая к последней то мы добавляем больше чем одну предыдущую страничку 
					//чтобы их общее колличество было не изменным
					let firstElem = Number(Object.keys(resultObj[String(index)])[0]);
					let newObject = {};
					let index_skips_save = index_skips;
					for(let n = 0; n < index_skips_save; n++){
						newObject[firstElem - index_skips] = buttonsObj[String(index)][String(firstElem - index_skips)]; 
						index_skips--;
					};
					for(let key in resultObj[String(index)]){ newObject[key] = resultObj[String(index)][key];};
					resultObj[String(index)] = newObject;
					
					resultObj[String(index)][String(i+2)] = buttonsObj[String(index)][ Object.keys(buttonsObj[String(index)]).length ];					
				}else{
					resultObj[String(index)][String(i+1)] = function(){
						let elem = document.createElement('p');
						elem.innerHTML = '...';
						return elem;
					}();
					resultObj[String(index)][String(i+2)] = buttonsObj[String(index)][ Object.keys(buttonsObj[String(index)]).length ];
				};
				
				flag = true;
				break;
			};
		};
	};
	if(flag){ obj = resultObj; }else{ obj = buttonsObj; };
	return obj;
};
requestSearchTegs.checkedOfNullInDataRequest = function(data){
	for(let key in data.body){
		if(data.body[key]==null || data.body[key]==undefined){ delete data.body[key]; };
	};
	if( Object.keys(data.body).length == 0 ){ return false; };
	return true;
};
requestSearchTegs.collectionStyleMethods = {//коллекция стилитических методов
	"modeOfResultBlock": function(obj){
		
	},
};
requestSearchTegs.settings = {//хранилище настроек
	"tmp": {
		
	},
	"maxLength": 5,//6 > N ? -> [6-1], [...], [N]
	"currentPage": 1,
};
