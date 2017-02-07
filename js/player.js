// Global config
var config = {
	mediaURL: '/media/mp3/',
	trackElemID: 'TrackBall',
	ridgeElemID: 'TrackContainer',
	audioElemID: 'MusicPlayerControl',
	queueElemID: 'QueueContainer',
	historyElemID: 'HistoryContainer',
	playlistElemID: 'PlaylistContainer',
	tracksElemID: 'TrackList'
	
};
// MusicPlayer container variable
var MusicPlayer;

// General functions
function isInt(value) {
	if (isNaN(value)) {
		return false;
	}
	var x = parseFloat(value);
	return (x | 0) === x;
}

function toTime(totalSeconds) {
	totalSeconds = Math.floor(totalSeconds);
	if (isInt(totalSeconds)) {
		var minutes = Math.floor(totalSeconds/60);
		var seconds = totalSeconds - (minutes*60);
		if (seconds < 10) {
			seconds = '0' + seconds;
		}
		var time = (minutes + ':' + seconds);
		return time;
	} else {
		return "0:00";
	}
}

// Music Player core object
function Player() {
	//  DOM
	this.control = document.getElementById(config.audioElemID);
	this.track = $('#' + config.trackElemID);
	this.ridge = $('#' + config.ridgeElemID);
	
	
	// Properties
	this.currentSong = void(0);
	this.currentIndex = -1;
	this.queue = new SongCollection(config.queueElemID);
	// this.history = new SongCollection(config.historyElemID);
	this.playlist = new SongCollection(config.playlistElemID);
	this.isInitialized = false;
	this.isMouseOnTrack = false;
	
	this.isPlaying = function () {return !this.control.paused;};
	this.ridgeWidth = function () {return this.ridge.width();};
	
	this.repeatMode = 0;
	this.shuffleMode = 0;
	this.hoverTime = 0;
	
	// Methods
	this.init = function () {
		if (!this.isInitialized) {
			// Attach event handlers
			this.ridge.on('click', function(e) { MusicPlayer.navigate(e); });
			this.ridge.on('mousemove', function(e) { MusicPlayer.getPointerPercentage(e) });
			this.ridge.on('mouseenter', function(e) { MusicPlayer.isMouseOnTrack = true; MusicPlayer.update() });
			this.ridge.on('mouseleave', function(e) { MusicPlayer.isMouseOnTrack = false; MusicPlayer.update() });
			// Set interval to update TrackBall
			setInterval(function (){MusicPlayer.update();},1000);
			
			this.isInitialized = true;
		}
	};
	
	this.setVolume = function(value) {
		this.control.volume = value;
	};
	
	this.getPointerPercentage = function (e) {
		var realOffset;
		if (e.target.id == "TrackText") {
			realOffset = e.target.offsetLeft + e.offsetX;
		} else {
			realOffset = e.offsetX;
		}
		this.hoverTime = realOffset/this.ridgeWidth()*this.control.duration
		this.update();
		return this.hoverTime;
	};
	
	this.update = function() {
		if (this.isPlaying()) {
			var text;
			var percentage = this.control.currentTime/this.control.duration * 100;
			$('#TrackBall').css('width',percentage + '%');
			
			if (this.isMouseOnTrack) {
				text = toTime(this.hoverTime);
			} else {
				text = toTime(this.control.currentTime) + '/' + toTime(this.control.duration);
			}
			$('#TrackText').html(text);
		}
	}
	
	this.previous = function () {
		if (!this.queue.isEmpty()) {
			if (this.currentIndex != 0) {
				this.currentIndex --;
			}
			this.currentSong = this.queue.fetch(this.currentIndex);
			this.updateText();
			this.control.src = config.mediaURL + this.currentSong.File.File;
			this.play()
		}
	};
	this.next = function () {
		if (!this.queue.isEmpty()) {
			if (this.queue.upperBound() > this.currentIndex) {
				this.currentIndex ++;
			}
			this.currentSong = this.queue.fetch(this.currentIndex);
			this.updateText();
			this.control.src =  config.mediaURL + this.currentSong.File.File;
			this.play();
		}
	};
	this.play = function () {
		if (this.currentSong !== void(0)) {
			this.control.play();
		} else if (!this.queue.isEmpty()) {
			this.next();
		}
	};
	this.pause = function () {
		this.control.pause();
	};

	this.navigate = function (e) {
		this.control.currentTime = this.getPointerPercentage(e);
		this.update();
	};
	
	this.updateText = function () {
		$('#SongTitle').html(this.currentSong.Title);
		$('#SongArtist').html(this.currentSong.Artist);
		document.title = this.currentSong.Title + ' - ' + this.currentSong.Artist;
	}
	
	this.queueSong = function(id) {
		$.ajax({
			method: 'POST',
			url: '/media/player/song',
			dataType: 'json',
			data: {'song_id':id},
			success: function (r) {MusicPlayer.queue.addOnBottom(r);}
		});
	};

}

function SongCollection(elemID) {
	this.containerID = elemID;
	this.list = [];
	this.isEmpty = function () {return this.list.length === 0};
	this.currentIndex = 0;
	this.upperBound = function () {return this.list.length - 1};
	
	this.addOnTop = function (item) {this.list.unshift(item); this.listToHTML();};
	this.addOnBottom = function (item) {this.list.push(item); this.listToHTML();};
	
	this.removeFromTop = function () {var item = this.list.shift(); this.listToHTML(); return item;};
	this.removeFromBottom = function () {var item = this.list.pop(); this.listToHTML(); return item;};
	
	this.fetchFromTop = function () {	this.currentIndex = 0; return this.list[this.currentIndex];};
	this.fetchFromBottom = function () {this.currentIndex = this.upperBound; return this.list[this.currentIndex];};
	this.fetch = function (index) { this.currentIndex = index; this.listToHTML(); return this.list[index];};
	// outputs a song into an HTML string
	this.itemToHTML = function (item, selected) {
		var html = '<div class="sf wrapper lh32" '+ (selected ? ' style="color: rgb(52, 152, 219)"' : '') + '>';
		html += '<div class="sf box size-5">' + item.Title + '</div>';
		html += '<div class="sf box size-5">' + item.Artist + '</div>';
		html += '</div>';
		return html;
	};
	// outputs the list of songs into an HTML string
	this.listToHTML = function () {
		var html = '';
		for (var i in this.list) {
			html += this.itemToHTML(this.list[i], this.currentIndex == i);
		}
		$('#' + this.containerID).html(html);
	};	
}