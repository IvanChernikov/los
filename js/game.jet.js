var Game = {
	Config : {
		Quality : 1,
		SoundEnabled : true,
		SoundVolume : 1,
		FPS: 30,
		AspectRatio: (2),
		ResourcePath: '/res/jet/'
	},
	Window : {
		ID : "GameWindow",
		Element : function() { return document.getElementById(this.ID); },
		Resize: function () {
			var width = Math.min($(this.Element()).width(),1920);
			this.Element().width = width; this.Element().height = width/Game.Config.AspectRatio;
		},
		Scale: function () { return this.Window().width/1000; },
		Clear: function () {
			this.Element().getContext('2d').clearRect(0,0,Game.Window.Element().width,Game.Window.Element().height);
			Background(10);
		},
		MousePosition: [0,0]
	},
	Process: {
		UpdateEvent: new CustomEvent('update', {'detail': '?!', bubbles: false, cancelable: false}),
		DrawEvent: new CustomEvent('draw', {'detail':{ message: "Game Draw Step", time: new Date() }, bubbles: false, cancelable: false}),
		Update: function () { Game.Window.Element().dispatchEvent(Game.Process.UpdateEvent);},
		Draw: function () { Game.Window.Clear(); Game.Window.Element().dispatchEvent(Game.Process.DrawEvent);},
		Subscribe: function (instance) {
			Game.Window.Element().addEventListener('update', instance.onUpdate);
			Game.Window.Element().addEventListener('draw', instance.onDraw);
		},
		UnSubscribe: function (instance) {
			Game.Window.Element().removeEventListener('update', instance.onUpdate);
			Game.Window.Element().removeEventListener('draw', instance.onDraw);
		},
		Loop: void(0)
	},
	Keys: {
		List: {},
		Pressed: function (key) {
			if (this.List[key] === void(0)) {
				return false;
			} else {
				return this.List[key];
			}
		},
		Handler: function (e) {
			Game.Keys.List[e.key] = (e.type === 'keydown' ? true : false );
			// console.log(e.type, e.key, Game.Keys.Pressed(e.key));
		},
		Init: function () {
			document.addEventListener('keyup', Game.Keys.Handler);
			document.addEventListener('keydown', Game.Keys.Handler);
		}
	},
	Objects: {
		Player: void(0)
	}
}

function Initialize() {
	console.log("Initializing...");
	// Init Window
	Game.Window.Resize();
	$('body')[0].onresize = function () { Game.Window.Resize(); };
	// Init key listener
	Game.Keys.Init();
	Game.Window.Element().addEventListener('mousemove',function (e) {
			var rect = Game.Window.Element().getBoundingClientRect();
			Game.Window.MousePosition = [e.clientX - rect.left, e.clientY - rect.top];
		});
	// Init Player
	Game.Objects.Player = new Player(); Game.Objects.Player.Init();

	// Start game Loop
	Game.Process.Loop = window.setInterval(function () { Game.Process.Update(); Game.Process.Draw(); }, 1000/Game.Config.FPS); //Game.Config.FPS);

}
var Sprite = function (unit, config) {
	this.unit = unit;
	this.image = new Image();
	this.image.src = Game.Config.ResourcePath + config.image;
	this.scale = 1;

	this.config = config;

	this.frames = [4,4];
	this.frameIndex = [0,0];

	this.x = function () {return unit.x;};
	this.y = function () {return unit.y;};

	this.coords = function () {return [unit.x, unit.y];};

	this.width = function () {return unit.width;};
	this.height = function () {return unit.height;};

	this.Animations = [[0,0]];
	this.Animation = 0;
	this.AnimationStep = 0;
	this.Animate = function (id, reverse) {

		if (reverse) {
			if (this.AnimationStep > 0) {
				this.AnimationStep --;
				this.frameIndex = this.Animations[this.Animation][this.AnimationStep];
			}
		} else {
			if (this.AnimationStep < this.Animations[this.Animation].length-1) {
				this.AnimationStep ++;
				this.frameIndex = this.Animations[this.Animation][this.AnimationStep];
			}
		}
	};

	this.Draw = function() {
		var canvas = Game.Window.Element();
		var context = canvas.getContext("2d");
		context.drawImage(
			this.image,
			this.frameIndex[0] * this.width(),
			this.frameIndex[1] * this.height(),
			this.width(),
			this.height(),
			this.x(),
			this.y(),
			this.width()*this.scale,
			this.height()*this.scale
		)
	};
}
var Unit = function () {
	// Set self reference
	var self = this;
	// Unit definition
	this.Name = "UNIT";
	this.x = 0;
	this.y = 0;
	this.width = 0;
	this.height = 0;

	this.Sprite = void(0);

	this.onUpdate = function (e) {
		//self.x = ~~(Math.random()*1000); self.y = ~~(Math.random()*750); console.log(self.Name);
	};
	// DO NOT OVERRIDE THIS METHOD
	this.onDraw = function (e) {
		if (self.Sprite !== void(0)) {
			self.Sprite.Draw();
		}
	};
	this.onInit = function () {
		// do things before subscribing to events
	};
	this.onKill = function () {
		// do things before unSubscribing to an event
	};

	this.Init = function () { this.onInit(); Game.Process.Subscribe(this); };
	this.Kill = function () { this.onKill(); Game.Process.UnSubscribe(this); };

}

var Player = function() {
	// Set Inheritance and reset self
	Unit.call(this);
	var self = this;
	// Player definition
	this.Name = "PLAYER";
	this.width = 128;
	this.height = 64;

	this.origin = function () {return [this.x + 64, this.y + 32];};
	this.speed = 15;

	this.Sprite = new Sprite(this, {
		image: 'mig.png',
		frames: [4,4]
	});

	this.onUpdate = function (e) {
		if (Game.Keys.Pressed('w')) {
			self.y -= 5;
			self.y = Math.max(self.y, 0);
			self.Sprite.Animate(0, true);
		}
		if (Game.Keys.Pressed('s')) {
			self.y += 5;
			self.y = Math.min(self.y, Game.Window.Element().height - self.height);
			self.Sprite.Animate(0, false);
		}
		if (Game.Keys.Pressed('a')) {
			self.x -= 5;
			self.x = Math.max(self.x, 0);
		}
		if (Game.Keys.Pressed('d')) {
			self.x += 5;
			self.x = Math.min(self.x, Game.Window.Element().width - self.width);
		}
		if (!Game.Keys.Pressed('w') && !Game.Keys.Pressed('s')) {
			self.Sprite.Animate(0, true);
		}
		if (false) {
			var h = Game.Window.Element().height;
			self.y = h/2 + h/4 * Math.sin(((totalSeconds)/7)*Math.PI/180);
			var w = Game.Window.Element().width;
			self.x = w/2 + w/4 * Math.cos((totalSeconds/7)*Math.PI/180);
		}
		if (true) {
			var o = self.origin();
			var m = Game.Window.MousePosition;
			var a = Math.atan((o[1]-m[1])/(o[0]-m[0]));

			var vx = Math.cos(a)*self.speed*(m[0]>=o[0] ? 1 : -1);
			var vy = Math.sin(a)*self.speed*(m[0]>=o[0] ? 1 : -1);

			if (!((vx < 0 && o[0] + vx < m[0]) || (vx > 0 && o[0] + vx > m[0]))) {
				self.x += vx;
			}
			if (!((vy < 0 && o[1] + vy < m[1]) || (vy > 0 && o[1] + vy > m[1]))) {
				self.y += vy;
			}
		}
				var smoke = new Smoke();
		smoke.y = self.y;
		smoke.x = self.x-16;
		smoke.Init();
		var fire = new Fire();
		fire.y = self.y+16;
		fire.x = self.x-16;
		fire.Init();

	};
	this.onInit = function () {
		this.Sprite.Animations = [[[0,0],[1,0],[2,0],[3,0]]]; // + ,[0,1],[1,1],[2,1]
	}
}
var Smoke = function () {
	Unit.call(this);
	var self = this;
	this.Name = "SMOKE";
	this.width = 64;
	this.height = 64;
	this.Sprite = new Sprite(this, {
		image: 'smoke.png',
		frames: [1,10],
		random: true
	});

	this.life = 10;
	this.onUpdate = function (e) {
		self.Sprite.Animate(0, false)
		self.life --;
		self.x = Game.Objects.Player.x - Math.pow(10-self.life,1.7)-32;
		if (self.life < -20) {
			self.x -= Math.random()*4;
		}
		//self.y = (self.y + Game.Objects.Player.y)/2;
		self.y += Math.random()*4-2;
		if (self.x <= -Game.Window.Element().width) {
			self.Kill();
		}
	};

	this.onInit = function () {
		this.Sprite.Animations = [[[0,0],[1,0],[2,0],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0],[9,0]]];
	}
}
var Fire = function() {
	Unit.call(this);
	var self = this;
	this.Name = "FIRE";
	this.width = 32;
	this.height = 32;
	this.Sprite = new Sprite(this, {
		image: 'fire.png',
		frames: [1,10],
		random: true
	});

	this.life = 10;
	this.onUpdate = function (e) {
		self.Sprite.Animate(0, false)
		self.life --;
		self.x = Game.Objects.Player.x - (10-self.life)*4-16;
		if (self.life === 0) {
			self.Kill();
		}
	};

	this.onInit = function () {
		this.Sprite.Animations = [[[0,0],[1,0],[2,0],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0],[9,0]]];
	}
}
var img = new Image();
img.src = Game.Config.ResourcePath + 'background.jpg';
var totalSeconds = 0;
function Background(delta) {
	totalSeconds += delta;


	var vx = 100; // the background scrolls with a speed of 100 pixels/sec
	var numImages = Math.ceil(Game.Window.Element().width / img.width) + 1;
	var xpos = totalSeconds * vx % img.width;

	var context = Game.Window.Element().getContext('2d');
	context.save();
	context.translate(-xpos, 0);
	for (var i = 0; i < numImages; i++) {
		context.drawImage(img, i * img.width, 0, img.width ,Game.Window.Element().height );
	}
	context.restore();
}

$(document).ready( Initialize );
