<?php session_start();?>
<?php
	include_once("../php/data.php");
	
	echo "<script>
		function choiceDeleteComment(buttonComDelete){//главная функция - менеджер
			const TIME_END = 500;

			let thisCords = positionElement(buttonComDelete);
			let resultBlocksObj = this.createChoiseWins.call(this, thisCords.posX, thisCords.posY, buttonComDelete.getBoundingClientRect().height, 
			buttonComDelete.getBoundingClientRect().width, buttonComDelete.parentNode);
			buttonComDelete.onclick = undefined;
			
			this.motionObjects(
				{buttonCancel: buttonComDelete, choiseBlock: resultBlocksObj.choiseBlock},
				function(){
					return resultBlocksObj.textArea.getBoundingClientRect().width + 
					resultBlocksObj.consentButton.getBoundingClientRect().width;
				}(),
				-1,
				{loopTime: 10, length: 4.5}, 
				TIME_END
			);
			
			buttonComDelete.onclick = function(resObj, buttonComDelete){
				this.motionObjects(
					{buttonCancel: buttonComDelete, choiseBlock: resultBlocksObj.choiseBlock},
					function(){
						return resultBlocksObj.textArea.getBoundingClientRect().width + 
						resultBlocksObj.consentButton.getBoundingClientRect().width;
					}(),
					1,
					{loopTime: 10, length: 4.5},
					TIME_END
				);	
				setTimeout(function(){
					buttonComDelete.onclick = this.bind(this, buttonComDelete);
					resObj.choiseBlock.parentNode.removeChild(resObj.choiseBlock);
				}.bind(this), TIME_END);
			}.bind(this, resultBlocksObj, buttonComDelete);
		};
		choiceDeleteComment.createChoiseWins = function(x=0, y=0, height=0, width=0, parentBlock=document.body){ //функция создания блоков
			let choiseBlock = createBlock('div', parentBlock, {
				className: 'choiseBlock_deleteComment',
				id: 'choiseBlock_deleteComment',
				style: { height: height+'px', width: (width-2)+'px', left: x+'px' }
			});
			let textArea = createBlock('div', choiseBlock, {
				className: 'textArea_deleteComment',
				innerHTML:'Удалить комментарий?',
				id: 'textArea_deleteComment',
				style: { height: (height-2)+'px'}
			});
			let formAction = createBlock('form', choiseBlock, {
				method: 'post',
				action: '../".$link_php."',
			});
			let currentPage = createBlock('input', formAction, {
				type: 'hidden',
				name: 'id_story',
				'value': '".$_SESSION['currentPage']."'.substr(".SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN.",
				'".$_SESSION['currentPage']."'.length - ".SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN." -
				".SETTING_CLIPPAGE_SUBSTR::TEMP_FOLDERSTORY_LEN."),
			});
			let type = createBlock('input', formAction, {
				type: 'hidden',
				name: 'type',
				'value': 'comment_delete',
			});
			let type_person = createBlock('input', formAction, {
				type: 'hidden',
				name: 'type_person',
				'value': function(parent){
					let type = parent.getAttribute('name');
					console.log(type);
					if(type=='admin'){ 
						type='_administrator'; 
					}else{ type=''; };
					return type;
				}(parentBlock),
			});
			let namePerson = createBlock('input', formAction, {
				type: 'hidden',
				name: 'name'+'_'+parentBlock.getAttribute('name'),
				'value': parentBlock.childNodes[1].innerHTML,
			});
			let idPerson = createBlock('input', formAction, {
				type: 'hidden',
				name: 'id'+'_'+parentBlock.getAttribute('name'),
				'value': parentBlock.childNodes[1].innerHTML,
			});
			let textMessage = createBlock('input', formAction, {
				type: 'hidden',
				name: 'text_comment',
				'value': parentBlock.childNodes[3].innerHTML,
			});
			let consentButton = createBlock('input', formAction, {
				className: 'consentButton_deleteComment',
				id: 'consentButton_deleteComment',
				type: 'submit',
				value: '\u2713',
				style: { height: height+'px' }
			});
			
			return {choiseBlock: choiseBlock, textArea: textArea, consentButton: consentButton};
		};
		choiceDeleteComment.motionObjects = function(dataObjs ={}, distance=0, vector=0, speed={loopTime: 0, length: 0}, timeEnd=4000){ //анимация движения блоков
			let dinamic_defaultSett = {};
			for(let key in dataObjs){//генерируем переменную с отступами всех блоков в dataObjs
				dinamic_defaultSett[key] = dataObjs[key].offsetLeft;
			};
			let values_defaultsett = {};
			for(let key in dataObjs){//сохраняем в статическую переменную первичные значения отступов блоков
				values_defaultsett[key] = dinamic_defaultSett[key];
			};
			let choiseBlock_width = dataObjs.choiseBlock.getBoundingClientRect().width;
			let values_choiseblock_width = choiseBlock_width;
			let values_buttonCancel_width = dataObjs.buttonCancel.getBoundingClientRect().width;
			let currentLength = 0;
			
			let animation = setInterval(function(){
				currentLength += Math.abs(speed.length);
				
				if(currentLength<=Math.abs(distance)){
					for(let key in dataObjs){
						if(currentLength < values_buttonCancel_width){
							if(key == 'buttonCancel'){
								dinamic_defaultSett[key] += (Math.abs(speed.length) * vector);
								dataObjs[key].style.left = String( dinamic_defaultSett[key] )+'px';
							};
						}else{ 
							dinamic_defaultSett[key] += (Math.abs(speed.length) * vector);
							dataObjs[key].style.left = String( dinamic_defaultSett[key] )+'px';	
						}; 
					};
					if(currentLength >= values_buttonCancel_width){
						choiseBlock_width += Math.abs(speed.length) * (vector*-1);
						dataObjs.choiseBlock.style.width = String( choiseBlock_width )+'px';
					};
					
				}else{
					for(let key in dataObjs){
						if(key == 'choiseBlock'){
							dataObjs[key].style.left = String( (values_defaultsett[key] + values_buttonCancel_width) + (distance * vector) )+'px';
						}else{ dataObjs[key].style.left = String( values_defaultsett[key] + (distance * vector) )+'px'; };
					};
					dataObjs.choiseBlock.style.width = String( distance )+'px';
					clearInterval(animation);
				};
			}, speed.loopTime);
			setTimeout(function(){ 
				clearInterval(animation);
			}, timeEnd);
		};
		choiceDeleteComment.formAction = function(){
			
		};
		//---------------------------------------------------
		function positionElement(elem) { //поиск отступов блока относительно родительского блока
			var posX = elem.offsetLeft;  // левый отступ эл-та от родителя
			var posY = elem.offsetTop; // верхний отступ эл-та от родителя
			return {posX: posX, posY: posY};
		};
		function createBlock(typeBlock, adress, props){ //универсальная функция создания и добавления блока
			let block = document.createElement(typeBlock);
			for(let key_props in props){
				if(key_props === 'style'){
					for(let key_props_style in props[key_props]){
						block[key_props][key_props_style] = props[key_props][key_props_style];
					};
				}else{ block[key_props] = props[key_props]; };
			};
			adress.appendChild(block);
			return block;
		};
	</script>";
?>



