/* Snake JS engine
 * by ShadowBlade
 * Using canvas to generate graphics
 */
/*Config*/
window.onload = function () {
	//Canvas handler
	canvas = document.getElementById('gameWindow');
	$(canvas).height($(canvas).width()*3/4); canvas.width=$(canvas).width(); canvas.height=$(canvas).height();
	//Size of each square in pixels
	pSize = 16*canvas.width/640;
	//Size of playable area
	xMax = 30;
	yMax = 30;
	//Bind controls
	initControls();
	//Color settings
	snakeColor = '#33AA33';
	snakeTrim = '#0A290A';
	fruitColor = '#FF4747';
	fruitTrim = '#FFC300';
	sideBarColor = '#04396C';
	sideBarSeparator = '#9C6AD6';
	labelColor = '#A67F00';
	textColor = '#FFC300';
	//Bonus stuff
	rainbowCounter = 0;
	rainbowColors = ['#FF0000', '#FF8800', '#FFFF00', '#88FF00', '#00FF00', '#00FF88', '#00FFFF', '#0088FF', '#0000FF', '#8800FF', '#FF00FF', '#FF0088'];
}

/*Functions*/
function initControls() {
	//Keyboard controls
	//This setup uses both WASD and arrow keys
	document.addEventListener('keydown', function (event) {
		switch (event.keyCode) {
		case 87: //W
		case 38: //Up
			if (snake.direction != 3) {
				snake.dir(1)
			};
			break;
		case 65: //A
		case 37: //Left
			if (snake.direction != 0) {
				snake.dir(2)
			};
			break;
		case 83: //S
		case 40: //Down
			if (snake.direction != 1) {
				snake.dir(3)
			};
			break;
		case 68: //D
		case 39: //Right
			if (snake.direction != 2) {
				snake.dir(0)
			};
			break;
		}
	});
	//Touch screen controls
	//Only available if os matches
	document.addEventListener('touchstart', function (event) {
		var wx = window.innerWidth;
		if (event.changedTouches[0].screenX > (wx / 2)) {
			snake.turn('right');
		} else {
			snake.turn('left');
		}
	});
}
function setDifficulty() {
	var e = document.getElementById('difficulty');
	var d = e[e.selectedIndex].text;
	switch (d) {
	case 'Easy':
		difficulty = 1;
		break;
	case 'Medium':
		difficulty = 2;
		break;
	case 'Hard':
		difficulty = 3;
		break;
	case 'Hardcore':
		difficulty = 4;
		break;
	}
}
function strDif() {
	switch (difficulty) {
	case 1:
		return 'Easy';
		break;
	case 2:
		return 'Medium';
		break;
	case 3:
		return 'Hard';
		break;
	case 4:
		return 'Hardcore';
		break;
	}
}
function startGame() {
	points = 0;
	setDifficulty();
	clear();
	//Create objects
	field = new Field(xMax, yMax);
	snake = new Snake(3 * difficulty);
	//Create sidebar
	drawSideBar();
	//Generate fruits
	newFruit();
	newFruit();
	//Start game loop
	var t = 150
		if (difficulty != 4) {
			t = 150 / difficulty
		}
		if (typeof ticker != 'undefined') {
			window.clearInterval(ticker);
		}
		window.setTimeout("ticker = setInterval('tick()', " + t + ")", 150);
}
function gameOver() {
	window.clearInterval(ticker);
	setTimeout('clear()', 1000);
	setTimeout('drawGameOverScreen()', 1000);
}
function tick() {
	snake.tick();
	drawSideBar();
}
function newFruit() {
	var valid = false;
	do {
		var xr = Math.floor(Math.random() * field.width);
		var yr = Math.floor(Math.random() * field.height);
		if (field.check(xr, yr) == 0) {
			valid = true;
			field.array[xr][yr] = 2;
			drawPixel(xr, yr, fruitColor, fruitTrim);
		}
	} while (!valid);
}
function drawPixel(x, y, color, trim) {
	var x = x * pSize;
	var y = y * pSize;
	var r = canvas.getContext('2d');
	r.beginPath();
	r.rect(x, y, pSize, pSize);
	r.fillStyle = trim;
	r.fill();
	r.beginPath();
	r.rect(x + 1, y + 1, pSize - 2, pSize - 2);
	r.fillStyle = color;
	r.fill();
}
function erasePixel(x, y) {
	var x = x * pSize;
	var y = y * pSize;
	var r = canvas.getContext('2d');
	r.beginPath();
	r.clearRect(x, y, pSize, pSize);
}
function drawSideBar() {
	var r = canvas.getContext('2d');
	r.beginPath();
	r.rect(30*pSize, 0, 10*pSize, 30*pSize);
	r.fillStyle = sideBarColor;
	r.fill();
	r.beginPath();
	r.moveTo(30*pSize+1, 0);
	r.lineTo(30*pSize+1, 30*pSize);
	r.lineWidth = 1;
	r.strokeStyle = sideBarSeparator;
	r.stroke();
	//Write score and length
	r.beginPath();
	r.font = '21px monospace';
	r.fillStyle = labelColor;
	r.textAlign = 'left';
	//Difficulty
	r.fillText('Difficulty:', 30*pSize+10, 30);
	//Score
	r.fillText('Score:', 30*pSize+10, 90);
	//Snake length
	r.fillText('Length:', 30*pSize+10, 150);
	//Different Color
	r.fillStyle = textColor;
	r.fillText(strDif(), 30*pSize+10, 60);
	r.fillText(points, 30*pSize+10, 120);
	r.fillText(snake.length + 1, 30*pSize+10, 180);
}
function drawGameOverScreen() {
	var r = canvas.getContext('2d');
	r.beginPath();
	r.textAlign = 'center';
	r.font = 'bold 109px monospace';
	r.fillStyle = rainbowColors[0];
	r.fillText('Game Over', 320, 100);
	r.font = '55px monospace';
	r.fillStyle = textColor;
	r.fillText('Final Score: ' + points, 320, 180);
	r.fillText('Final Length: ' + (snake.length + 1), 320, 240);
}
function clear(side) {
	var r = canvas.getContext('2d');
	r.beginPath();
	if (side === undefined) {
		r.clearRect(0, 0, 40*pSize, 30*pSize);
	} else {
		r.clearRect(30*pSize, 0, 10*pSize, 30*pSize);
	}
}
/*Classes*/
function Field(w, h) {
	this.width = w;
	this.height = h;
	
	this.array = [];
	
	this.check = function (x, y) {
		//Cell value = cv
		var cv;
		if (typeof this.array[x] === 'undefined') {
			cv = 0;
		} else {
			cv = this.array[x][y];
		}
		if (cv == 1) {
			return 1;
		} else if (cv == 2) {
			return 2;
		} else {
			return 0;
		}
	}
	for (i = 0; i <= w; i++) {
		this.array[i] = [];
	}
}
function Snake(length) {
	this.length = length;
	//Directions: 0 = right; 1 = up; 2 = left; 3 = down;
	this.direction = 0;
	this.posX = 7;
	this.posY = 7;
	
	this.segments = [];
	this.dirChanged = false;
	this.tick = function () {
		this.move();
		if (this.dirChanged == true) {
			this.dirChanged = false;
		}
	}
	this.move = function () {
		switch (this.direction) {
		case 0:
			this.posX += 1;
			break;
		case 1:
			this.posY -= 1;
			break;
		case 2:
			this.posX -= 1;
			break;
		case 3:
			this.posY += 1;
			break;
		}
		this.checkWarp();
		this.checkLength()
		this.checkCol();
		this.newSegment();
	}
	this.turn = function (side) {
		switch (side) {
		case 'right':
			this.direction -= 1;
			break;
		case 'left':
			this.direction += 1;
			break;
		}
		this.checkDir();
	}
	this.dir = function (d) {
		if (this.dirChanged == false) {
			this.direction = d;
			this.dirChanged = true;
		}
	}
	this.checkDir = function () {
		if (this.direction > 3) {
			this.direction = 0;
		} else if (this.direction < 0) {
			this.direction = 3;
		}
	}
	this.checkWarp = function () {
		if (this.posX == field.width) {
			this.posX = 0;
		}
		if (this.posX == -1) {
			this.posX = field.width - 1;
		}
		if (this.posY == field.height) {
			this.posY = 0;
		}
		if (this.posY == -1) {
			this.posY = field.height - 1;
		}
	}
	this.checkCol = function () {
		switch (field.check(this.posX, this.posY)) {
		case 0: //No collision
			if (difficulty == 4) {
				points += 10;
			}
			break;
		case 1: //Collides with a segment
			gameOver();
			break;
		case 2: //Collides with fruit cell
			points += 100 * difficulty * difficulty;
			newFruit();
			this.length += 1;
			break;
		}
	}
	this.checkLength = function () {
		if (this.segments.length > this.length && difficulty != 4) {
			seg = this.segments[this.length];
			seg.erase();
			field.array[seg.x][seg.y] = 0;
			this.segments.pop();
			
		}
	}
	this.newSegment = function () {
		this.segments.unshift(new Segment(this.posX, this.posY));
		field.array[this.posX][this.posY] = 1;
	}
}
function Segment(x, y) {
	this.x = x;
	this.y = y;
	
	this.draw = function () {
		if (points > 50000) {
			getRainbow();
		}
		drawPixel(this.x, this.y, snakeColor, snakeTrim);
	}
	this.erase = function () {
		erasePixel(this.x, this.y);
	}
	this.draw();
}
/*Bonus*/
function getRainbow() {
	snakeColor = rainbowColors[rainbowCounter];
	rainbowCounter += 1;
	if (rainbowCounter >= rainbowColors.length) {
		rainbowCounter = 0;
	}
}
