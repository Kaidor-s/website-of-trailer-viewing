let currentID_div = undefined, currentID_form = undefined;
let time_add = 200;

let dataSett = { 
	'questBlock': {
		linkOnElem: function(){ return document.getElementById('questBlock_deleteStory'); },
		elem: function(){ return document.createElement('div') },
		atribute: {
			'id': 'questBlock_deleteStory',
			'className': 'questBlocks_deleteStory',
		},
		addIn: function(){ 	return document.getElementById(currentID_div); },
		count: 0,
		countAddIn: 1,
	},
	'textQuest': {
		linkOnElem: function(){ return document.getElementById('queBl_delSt_span'); },
		elem: function(){ return document.createElement('span') },
		atribute: {
			'id': 'queBl_delSt_span',
			'className': 'questBlocks_deleteStory_span',
			'innerHTML': 'Удалить данный сюжет?',
		},
		addIn: function(){ 	return document.getElementById('questBlock_deleteStory'); },
		count: 0,
		countAddIn: 1,
	},
	'input_yes': {
		linkOnElem: function(){ return document.getElementById('queBl_delSt_input_yes'); },
		elem: function(){ return document.createElement('input') },
		atribute: {
			'id': 'queBl_delSt_input_yes',
			'className': 'deleteStory_buttYes',
			'value': 'удалить',
			'name': 'done_eDF',
			'type': 'submit',
		},
		addIn: function(){ 	return document.getElementById(currentID_form); },
		count: 0,
		countAddIn: 1,
	},
	'input_not': {
		linkOnElem: function(){ return document.getElementById('queBl_delSt_input_not'); },
		elem: function(){ return document.createElement('input') },
		prevElem: function(){ return document.getElementById('questBlock_deleteStory') },
		atribute: {
			'id': 'queBl_delSt_input_not', 
			'className': 'deleteStory_buttNot',
			'value': 'отмена',
			'type': 'submit',
			'onclick': function(){
				createQuestionBlock.count = 0;
				setTimeout(function(){
					let elem_div = this.prevElem();
					let elem_butt = document.getElementById('queBl_delSt_input_yes');
					elem_butt.parentNode.removeChild(elem_butt);
					elem_div.parentNode.removeChild(elem_div);
				}.bind(this), time_add);
			},
		},
		addIn: function(){ 	return document.getElementById('questBlock_deleteStory'); },
		count: 0,
		countAddIn: 1,
	},
};

function createQuestionBlock(elem_div, elem_form){
	currentID_div = elem_div.id, currentID_form = elem_form.id;
	
	if(this.count==0){
		for(let key in dataSett){
			let elem, prevElem; 
			for(let key_prop in dataSett[key]){
				if(key_prop == 'elem'){ elem = dataSett[key][key_prop].call( dataSett[key][key_prop] ); };
				if(key_prop == 'prevElem'){ prevElem = dataSett[key][key_prop](); };
				if(key_prop == 'atribute'){ 
					for(let keyAtr in dataSett[key][key_prop]){ 
						if(typeof dataSett[key][key_prop][keyAtr] == 'function'){
							elem[keyAtr] = dataSett[key][key_prop][keyAtr].bind( dataSett[key] ); 
						}else{ elem[keyAtr] = dataSett[key][key_prop][keyAtr]; };
					};
				};
				if(key_prop == 'method'){ dataSett[key][key_prop].call( dataSett[key] ); };
				if(key_prop == 'addIn'){ setTimeout(function(){ dataSett[key][key_prop]().appendChild(elem) }, time_add); };
			};
		};	
	};
	this.count++;
};
createQuestionBlock.count = 0;
