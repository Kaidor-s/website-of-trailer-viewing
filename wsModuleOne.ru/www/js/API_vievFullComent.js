let dataSett_commentOpen = {
	textBlock: {
		timeLoop: 10,
		tmeEnd: 850,
		lengthNum: 3,
		startLength: 70,
		border: 300,
	},
};

function openComment(elem){
	if(this.time >= ( (dataSett_commentOpen.textBlock.tmeEnd+200) / 10) ){ this.accessRight = 1; this.time=0; clearInterval(this.timetInterval); };
	
	if(elem.count == undefined && this.accessRight == 1){
		this.timetInterval = setInterval(function(){ this.time++; }.bind(this), 10);
		this.accessRight = 0;
		elem.count = 1;
		openComment.heightEdd_anim.num = dataSett_commentOpen.textBlock.startLength;
		
		let interval = setInterval( this.heightEdd_anim.bind( this.heightEdd_anim, elem, 1, function(num){
			if(num < dataSett_commentOpen.textBlock.border){ return 1; }else{ return 0; };
		}, dataSett_commentOpen.textBlock.border ), dataSett_commentOpen.textBlock.timeLoop );
		setTimeout(function(){ clearInterval(interval); }, dataSett_commentOpen.textBlock.tmeEnd);
	}else{
		if(this.accessRight == 1){
			this.timetInterval = setInterval(function(){ this.time++; }.bind(this), 10);
			this.accessRight = 0;
			elem.count = undefined;
			openComment.heightEdd_anim.num = dataSett_commentOpen.textBlock.border;
			
			let interval = setInterval( this.heightEdd_anim.bind( this.heightEdd_anim, elem, -1, function(num){
				if(num > dataSett_commentOpen.textBlock.startLength){ return 1; }else{ return 0; };
			}, dataSett_commentOpen.textBlock.startLength ), dataSett_commentOpen.textBlock.timeLoop );
			setTimeout(function(){ clearInterval(interval); }, dataSett_commentOpen.textBlock.tmeEnd);	
		};
	};
};
openComment.time = 0;
openComment.accessRight = 1;
openComment.timetInterval = undefined;

openComment.heightEdd_anim = function(elem, vector, func, endVal){
	this.num = this.num + (dataSett_commentOpen.textBlock.lengthNum * vector);
	if(func(this.num)){
		elem.style.height = String(this.num)+'px';
	}else{ elem.style.height = String(endVal)+'px'; };
};
