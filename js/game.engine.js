function Config(id, width, height, allowResize, gameSpeed, resPath) {
    this.id = id;
    this.width = width;
    this.height = height;
    this.fixed = !allowResize;
    this.speed = gameSpeed;
    this.path = resPath;
}
// Engine container
var engine = {
    Init: function(config) {
        this.window = document.getElementById(config.id);
        this.width = config.width;
        this.height = config.height;
        this.speed = config.gameSpeed;
        this.path = '/res/' + config.path + '/';

        // Events
        this.window.addEventListener('mousemove', this.mouse.handler);
        this.window.addEventListener('mousedown', this.mouse.handler);
        this.window.addEventListener('mouseup', this.mouse.handler);
        document.addEventListener('keyup', this.keys.handler);
        document.addEventListener('keydown', this.keys.handler);
    },
    window: {},
    width: 0,
    height: 0,
    speed: 0,
    path: '',
    // Resources
    images: {},
    LoadImage: function(name, path) {
        var img = new Image();
        img.src = this.path + path;
        this.images[name] = img;
    },
    // Objects
    objects: {},

    // Mouse
    mouse: {
        x: 0,
        y: 0,
        pressed: false,
        handler: function (e) {
            if (e.type === 'mouseup') {
                engine.mouse.pressed = false;
            } else if (e.type === 'mousedown') {
                engine.mouse.pressed = true;
            }
            var rect = e.target.getBoundingClientRect();
			engine.mouse.x = e.clientX - rect.left;
            engine.mouse.y = e.clientY - rect.top;
        }
    },
    UpdateMouse: function (e) {
        console.log(e);
    },
    // Keyboard
    keys: {
		list: {},
		pressed: function (key) {
			if (this.list[key] === void(0)) {
				return false;
			} else {
				return this.list[key];
			}
		},
		handler: function (e) {
			engine.keys.list[e.key] = (e.type === 'keydown' ? true : false );
			// console.log(e.type, e.key, Game.Keys.Pressed(e.key));
		}
    },

    Start: function () {
        i = 0;
        window.setInterval(function () {}, 1000/this.speed);
    }
};
