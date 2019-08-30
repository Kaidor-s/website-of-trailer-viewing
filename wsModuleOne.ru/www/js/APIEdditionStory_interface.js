let prevButtonId = undefined;
let time_add = 200;
let dataSett = {
	'titleStory': {
		linkOnElem: function(){ return document.getElementById('title_creationStory'); },
		elem: function(){ return document.createElement('textArea') },
		prevElem: document.getElementById('header_creatStroty'),
		atribute: {
			'cols': '50',
			'rows': '8',
			'name': 'title',
			'id': 'title_creationStory',
			'value': function(){ return this.prevElem.innerHTML; },
		},
		method: function(){ this.prevElem.innerHTML = ''; },
		addIn: function(){ 	return document.getElementById('form_buttEDF'); },
		count: 0,
		countAddIn: 1,
	},
	'announcementStory': {
		linkOnElem: function(){ return document.getElementById('announcement_creationStory'); },
		elem: function(){ return document.createElement('textArea') },
		prevElem: document.getElementById('announcement_creatStroty'),
		atribute: {
			'cols': '20',
			'name': 'anons',
			'rows': '1',
			'id': 'announcement_creationStory',
			'value': function(){ return this.prevElem.innerHTML; },
		},
		method: function(){ this.prevElem.innerHTML = ''; },
		addIn: function(){ 	return document.getElementById('form_buttEDF'); },
		count: 0,
		countAddIn: 1,
	},
	'tagsStory': {
		linkOnElem: function(){ return document.getElementById('tags_creationStory_id'); },
		elem: function(){ return document.createElement('textArea') },
		prevElem: document.getElementById('tags_creatStroty'),
		atribute: {
			'cols': '10',
			'name': 'tags',
			'cols': '20',
			'rows': '3',
			'id': 'tags_creationStory_id',
			'value': function(){ return this.prevElem.innerHTML; },
		},
		method: function(){ this.prevElem.innerHTML = ''; },
		addIn: function(){ 	return document.getElementById('form_buttEDF'); },
		count: 0,
		countAddIn: 1,
	},
	'textStory': {
		linkOnElem: function(){ return document.getElementById('text_creationStory'); },
		elem: function(){ return document.createElement('textArea') },
		prevElem: document.getElementById('description_text_creatStroty'),
		atribute: {
			'cols': '91',
			'rows': '12',
			'name': 'text',
			'id': 'text_creationStory',
			'value': function(){ return this.prevElem.innerHTML; },
		},
		method: function(){ this.prevElem.innerHTML = ''; },
		addIn: function(){ 	return document.getElementById('form_buttEDF'); },
		count: 0,
		countAddIn: 1,
	},
	'imageStory':{
		linkOnElem: function(){ return document.getElementById('loadImageStory'); },
		elem: function(){ return document.createElement('input') },
		prevElem: document.getElementById('image_creatStroty'),
		atribute: {
			'type': 'file',
			'accept': 'image/jpeg,image/png,image/gif',
			'name': 'image',
			'id': 'loadImageStory',
			'value': function(){ return this.prevElem.innerHTML; },
		},
		method: function(){ this.prevElem.innerHTML = ''; },
		addIn: function(){ 	return document.getElementById('form_buttEDF'); },
		count: 0,
		countAddIn: 1,
	},
	// 'done_cF_id': {
		// linkOnElem: function(){ return document.getElementById('done_cF_id'); },
		// elem: function(){ return document.getElementById('done_cF_id'); },
		// addIn: function(){ 	
			// if(this.count==0){
				// this.count++;
				// return document.getElementById('form_buttEDF');
			// }else{
				// this.count=0;
				// return document.getElementById('text_content');
			// };
		// },
		// count: 0,
		// countAddIn: 2,
	// }, 
	'done_eDF_id': {
		linkOnElem: function(){ return document.getElementById('done_eDF_id'); },
		elem: function(){ return document.getElementById('done_eDF_id'); },
		addIn: function(){ 	
			if(this.count==0){
				this.count++;
				return document.getElementById('form_buttEDF');
			}else{
				this.count=0;
				return document.getElementById('text_content');
			};
		},
		count: 0,
		countAddIn: 2,
	}, 
	'cansel_button': {
		linkOnElem: function(){ return document.getElementById('canselButton_id'); },
		elem: function(){ return document.createElement('input'); },
		atribute:{
			'type': 'submit',
			'value': 'Отменить',
			'className': 'canselButton',
			'id': 'canselButton_id', 
			'onclick': function(elem){ return cancel_edDes.bind(elem); },
		},
		addIn: function(){ return document.getElementById('text_content'); },
		count: 0,
		countAddIn: 1,
	},
};

function editngDescript(event){ //функция добавления области для написания нового сюжета
	/* при первом разе кнопка done_eDF_id работает как активатор события editngDescript а при повтором нажатии как кнопка отправки формы с textarea, 
	который мы добавили в активированном событии */
	if(event.target.id == prevButtonId||prevButtonId == undefined){
		if(this.flag == 0){
			for(let key in dataSett){
				let elem, prevElem;
				for(let key_prop in dataSett[key]){
					if(key_prop == 'elem'){ elem = dataSett[key][key_prop].call( dataSett[key][key_prop] ); };
					if(key_prop == 'prevElem'){ prevElem = dataSett[key][key_prop]; };
					if(key_prop == 'atribute'){ 
						for(let keyAtr in dataSett[key][key_prop]){ 
							if(typeof dataSett[key][key_prop][keyAtr] == 'function'){
								if(keyAtr == 'value'){
									this['value_'+key] = dataSett[key][key_prop][keyAtr].call( dataSett[key] );
								}else{ elem[keyAtr] = dataSett[key][key_prop][keyAtr].call( dataSett[key], elem ); }
							}else{ elem[keyAtr] = dataSett[key][key_prop][keyAtr]; };
						};
					};
					if(key_prop == 'method'){ dataSett[key][key_prop].call( dataSett[key] ); };
					if(key_prop == 'addIn'){ setTimeout(function(){ dataSett[key][key_prop]().appendChild(elem) }, time_add); };
				};
			};	
		};
		prevButtonId = event.target.id;	
	}else{
		cancel_edDes();
		prevButtonId = undefined;
		this.flag = 0;
		return console.log('Не та кнопка!');
	};
	
	this.flag++;
};
editngDescript.flag = 0;
// document.getElementById('done_cF_id').addEventListener('click', editngDescript.bind(editngDescript));
document.getElementById('done_eDF_id').addEventListener('click', editngDescript.bind(editngDescript));

function cancel_edDes(){ //функция отмены изменения сюжета
	/* если при первом разе нам перехотелось создавать сюжет - возвращаем предыдущее текстовое значение, сбрасываем кэш-значения и удаляем ненужные элементы*/
	for(let key in dataSett){
		let elem, prevElem; 
		dataSett[key].count = 0;
		for(let key_prop in dataSett[key]){
			if(key_prop == 'linkOnElem'){ elem = dataSett[key][key_prop].call( dataSett[key][key_prop] ); };
			if(key_prop == 'prevElem'){ prevElem = dataSett[key][key_prop]; };
			if(key_prop == 'addIn'){ 
				dataSett[key][key_prop]().removeChild(elem);
				if(dataSett[key].countAddIn > 1){
					dataSett[key][key_prop]().appendChild(elem);
				};
			};
		};
		
		if(editngDescript['value_'+key] != undefined){ prevElem.innerHTML = editngDescript['value_'+key]; };
		editngDescript['value_'+key] = undefined;
	};
	editngDescript.flag = 0;
};