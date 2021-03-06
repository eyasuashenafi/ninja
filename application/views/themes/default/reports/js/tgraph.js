/**
*	TGraph - JavaScript graphing utility
*/

TGraphEventBinder = function (node, event, callback) {
	if (node.addEventListener) {
		node.addEventListener(event, callback, false);
	} else {
		node.attachEvent('on' + event, callback);
	}
}

var TGraph = function (stops, type, name, max) {
		
		/**
		* @param object stops
		*	@param string type = bar
		* @param number width = 500
		* @param number max = 400
		*/
		
		var w = document.getElementById('tgraph').clientWidth;
		
		console.log(Math.floor(w / 50) * 50);
		
		this.name = name || 'Graph';
		this.stops = stops;
		this.type = type || 'bar';
		this.width = w * 0.8;
		this.max = max || 400;
		this.blocks = [];
		
		this.hoverbox = document.createElement('div');
		this.container = document.createElement('div');
		
		this.container.className = 'tgraph-container';
		this.hoverbox.className = 'tgraph-hoverbox';
		this.label = document.createElement('label');
		
		this.label.innerHTML = name;
		this.label.className = 'tgraph-label';
		
		this.container.style.overflow = 'auto';
		this.container.style.width = '100%';
		this.create();
		this.hookMouseMove();
		
		document.body.appendChild(this.hoverbox);
		
		document.getElementById('tgraph').appendChild(this.container);
	
	 //console.log(this.graph.offsetWidth);
	
};

TGraph.prototype = {
	
	addHover: function (stop, block, time) {
	
		var that = this;
	
		TGraphEventBinder(block, 'mouseover', function () {
			that.hoverbox.style.display = 'block';
			
			that.hoverbox.innerHTML = '<b class="title">' + stop['label'] +'</b><br />'+ 
				'<small>' + that.parseNiceTime(new Date(time)) + ' - ' + 
				that.parseNiceTime(new Date(time + stop.duration)) + 
				'</small>' +
				((stop['short']) ? '<br />' + stop['short'] : '');
				
		});
		
		TGraphEventBinder(block, 'mouseout', function () {
			that.hoverbox.style.display = 'none';
			that.hoverbox.innerHTML = '';
		});
		
	},
	
	hookMouseMove: function () {
	
		var that = this;
	
		TGraphEventBinder(window, 'mousemove', function (e) {
			var posx = 0;
			var posy = 0;
			if (!e) var e = window.event;
			if (e.pageX || e.pageY) 	{
				posx = e.pageX;
				posy = e.pageY;
			}
			else if (e.clientX || e.clientY) 	{
				posx = e.clientX + document.body.scrollLeft
					+ document.documentElement.scrollLeft;
				posy = e.clientY + document.body.scrollTop
					+ document.documentElement.scrollTop;
			}
			
			that.hoverbox.style.left = (posx + 15) + 'px';
			that.hoverbox.style.top = (posy + 15) + 'px';
		});
		
	},
	
	formatNumber: function (n) {
		if (n < 10) {
			return '0' + n;
		} else {
			return n;
		}
	},
	
	parseNiceTime: function (date) {
		return this.formatNumber(date.getHours()) + ':' + 
			this.formatNumber(date.getMinutes()) + ' ' + 
			date.getFullYear() +'-'+ 
			this.formatNumber((date.getMonth() + 1)) + '-' + 
			this.formatNumber(date.getDate());
	},
	
	parseNiceTimeHigh: function (date) {
		return date.getFullYear() +'-'+ 
			this.formatNumber((date.getMonth() + 1)) + '-' + 
			this.formatNumber(date.getDate());
	},
	
	parseNiceTimeSuperHigh: function (date) {
		return this.formatNumber((date.getMonth() + 1)) + '-' + 
			this.formatNumber(date.getDate());
	},
	
	createBlock: function (stop) {
		var block = document.createElement('div');
		
		block.style.width = ((stop.duration / this.max) * 100) + '%';
		block.style.background = stop.color;
		block.className = 'tgraph-block';
		
		return block;
	},
	
	addNote: function (stop, skew) {
		note = document.createElement('div');
		note.className = 'tgraph-note';
		note.style.background = stop['color'];
		note.style.marginTop = (parseInt(note.style.marginTop || '-6', 10) - skew) + 'px';
		note.style.marginLeft = (parseInt(note.style.marginLeft || '-4', 10) + skew) + 'px';
		stop.block.appendChild(note);
	},
	
	drawScale: function () {
	
		var line = null, 
			graph = document.createElement('div'),
			el = null,
			shigh = ((new Date(this.start)).getFullYear() == (new Date(this.start + this.max)).getFullYear());
			
		graph.className = 'tgraph';
		//graph.style.width = this.width + 'px';
		graph.style.height = '40px';
		graph.style.border = 'none';
		
		clone = this.label.cloneNode();
		clone.innerHTML = "";
		
		for (var i = 0; i <= 6; i += 1) {
			
			line = document.createElement('div');
			line.className = 'tgraph-time-line';
			line.style.height = (this.height) + 'px';
			line.style.marginTop = '-' + (this.height + 0) + 'px';
			
			el = document.createElement('div');
			el.style.width = ((1 / 7) * 100) + '%';
			el.className = 'tgraph-time';
			
			if (shigh) {
				el.innerHTML = '&nbsp;&nbsp;' + this.parseNiceTimeSuperHigh(new Date(this.start + ((this.max / 7) * i)));
			} else {
				el.innerHTML = '&nbsp;&nbsp;' + this.parseNiceTimeHigh(new Date(this.start + ((this.max / 7) * i)));
			}
			
			el.appendChild(line);
			
			graph.appendChild(el);
		}
		
		this.container.appendChild(clone);
		this.container.appendChild(graph);
		
	},
	
	create: function () {
	
		var time = null,
			note = null,
			line = null,
			graph = null,
			lclone = null,
			subline = document.createElement('div'),
			skew = 0,
			i = 0;
			
		this.start = this.max * 1000;
		this.height = 0;

		this.max = 0;
		for (i; i < this.stops[0].length; i += 1) {
			this.max += this.stops[0][i].duration * 1000;
		}
		
		for (var y = 0; y < this.stops.length; y += 1) {
			
			graph = document.createElement('div');
			line = document.createElement('div');
			i = 0;	
			
			graph.className = 'tgraph';
			subline.className = 'tgraph-subline';
			line.className = 'tgraph-block-line';
			
			lclone = this.label.cloneNode();
			lclone.innerHTML = this.name[y];
			time = this.start;
			laststate = '';
			
			this.height += 40;
			for (i; i < this.stops[y].length; i += 1) {
			
				this.stops[y][i].duration = this.stops[y][i].duration * 1000;	
				this.stops[y][i].index = i;
				
				this.stops[y][i].block = this.createBlock(this.stops[y][i]);
				
				if ((this.stops[y][i].duration / this.max) < 0.05) {
					
					clone = this.stops[y][i].block.cloneNode(true);
					swidth = (parseFloat(this.stops[y][i].block.style.width) * 100);
					if (swidth < 2) swidth = 2;
					
					clone.style.width =  swidth + '%';
					subline.appendChild(clone);
	
					this.stops[y][i].block.style.background = "#333";

					this.addHover(this.stops[y][i], clone, time);
					
				} else {
	
					if (subline.children.length > 0) {
						
						nwidth = 0;
						
						if (this.stops[y][i - 1]) {
		
							this.stops[y][i - 1].block.appendChild(subline);
							children = this.stops[y][i - 1].block.children[0].children;
							
							for (var x = 0; x < children.length; x += 1) {
								child = children[x]
								if (child.className) {
									nwidth += parseFloat(child.style.width);
									child.style.width = Math.floor(parseFloat(child.style.width)) + 'px';
								}
							}
							
							subline.style.width = Math.ceil(nwidth) + 'px';
							subline.style.marginLeft = '-' + ((nwidth / 2) + 1) + 'px'; 
						}
						
						subline = document.createElement('div');
						subline.className = 'tgraph-subline';
					}
					
					this.addHover(this.stops[y][i], this.stops[y][i].block, time);
				}
				
				line.appendChild(this.stops[y][i].block);
				
				time += this.stops[y][i].duration;
				
			}
			
			if (!document.addEventListener) {
				var clear = document.createElement('div');
				clear.style.clear = 'both';
				line.appendChild(clear);
			}
			
			graph.appendChild(line);
			this.container.appendChild(lclone);
			this.container.appendChild(graph);
		}
		
		this.drawScale();
		
	}
	
};
