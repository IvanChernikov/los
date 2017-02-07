var Inputs = {
	Artist : void(0),
	Title  : void(0),
	URL : void(0),
	Description : void(0),
	TimeFrom : void(0),
	TimeTo : void(0),
	StartTime : void(0),
	EndTime : void(0),
	TooltipFrom : void(0),
	TooltipTo : void(0),
	TotalTime : -1
}


$( document ).ready(function() {
	Inputs.Artist = $('#Artist');
	Inputs.Title = $('#Title');
	Inputs.URL = $('#URL');
	Inputs.Description = $('#Description');
	Inputs.TimeFrom = $('#TimeFrom');
	Inputs.TimeTo = $('#TimeTo');
	Inputs.StartTime = $('#StartTime');
	Inputs.EndTime = $('#EndTime');
	Inputs.TooltipFrom = $('#TooltipFrom');
	Inputs.TooltipTo = $('#TooltipTo');
	Inputs.TotalTime = -1;
	$('#URL').on('change', function() {
		try {
			var url = $('#URL').val();
			var urlSplit = url.split("v=");

			$('#vidId').val(urlSplit[1]);

			getYouTubeInfo(urlSplit[1]);

			$('#previewBox').html('<object style="width: 100%; height: 400px;margin: 0 auto;" data="http://www.youtube.com/v/'+urlSplit[1]+'" type="application/x-shockwave-flash"><param name="src" value="http://www.youtube.com/v/'+urlSplit[1]+'" /></object>');

		}
		catch(err) {
			Inputs.TotalTime = -1;
			SetRange();
		}
	});

});

function SetRange() {
	if (Inputs.TotalTime !== -1) {
		Inputs.TimeFrom.val(0);
		Inputs.TimeFrom.attr('max',Inputs.TotalTime - 1);
		Inputs.TimeTo.attr('max',Inputs.TotalTime);
		Inputs.TimeTo.val(Inputs.TotalTime);
	}
	var st = ToTime(Inputs.TimeFrom.val());
	var et = ToTime(Inputs.TimeTo.val());

	Inputs.StartTime.val(st);
	Inputs.EndTime.val(et);
}
function UpdateRange(elem, value) {
	value = parseInt(value);
	var tf = parseInt(Inputs.TimeFrom.val());
	var tt = parseInt(Inputs.TimeTo.val());
	if (elem.id === Inputs.TimeFrom.attr('id')) {
		elem.value = Math.min(tt-1,value)
	} else if (elem.id === Inputs.TimeTo.attr('id')) {
		elem.value = Math.max(tf+1,value)
	}

	var st = ToTime(Inputs.TimeFrom.val());
	var et = ToTime(Inputs.TimeTo.val());

	Inputs.StartTime.val(st);
	Inputs.EndTime.val(et);
}
function UpdateTooltip(event) {
	var x = event.pageX - $(event.srcElement).offset().left + 1;
	var w = $(event.srcElement).width();
	var r = x/w * Inputs.TotalTime;
	if (event.srcElement.id === 'TimeFrom') {
		Inputs.TooltipFrom.text(ToTime(r));
	} else {
		Inputs.TooltipTo.text(ToTime(r));
	}
	// console.log(x,w);
}

/*
function downloadVideo(video_id){
	var timeNow = Date.now().toString();
	//var i = 'http://www.youtube-mp3.org/get?ab=128&video_id='+video_id+"&h="+info.h+"&r="+timeNow+"."+cc(video_id+timeNow)video_id+timeNow)'
	var i = 'http://www.youtube-mp3.org/get?ab=128&video_id='+video_id+'&r='+timeNow;//+'.'+cc(video_id+timeNow)video_id+timeNow)';
}*/

function cc(a){
	var __AM=65521; // the largest prime less than 2^16...
	var c = 1, b = 0, d, e;
	for(e = 0; e < a.length; e++){
		d = a.charCodeAt(e);
		c = (c+d)%__AM;
		b = (b+c)%__AM;
	}
	return b<<16|c;
}

function YTDurationToSeconds(duration) {
  var match = duration.match(/PT(\d+H)?(\d+M)?(\d+S)?/)

  var hours = (parseInt(match[1]) || 0);
  var minutes = (parseInt(match[2]) || 0);
  var seconds = (parseInt(match[3]) || 0);

  return hours * 3600 + minutes * 60 + seconds;
}

function ToTime(value) {
	value = parseInt(value);
	var mins = Math.floor(value/60);
	var seconds = value-(mins*60);

	seconds = (seconds.toString().length === 1 ? '0' + seconds.toString() : seconds);

	return mins.toString() + ':' + seconds;
}

function getYouTubeInfo(video_id) {
		var ytApiKey = "AIzaSyDf-i6jQ9ErAuv-wEAF-XUUAfTX7qGOM-k"

		var query = 'https://www.googleapis.com/youtube/v3/videos?id='+ video_id +'&key='+ ytApiKey +'&part=snippet,contentDetails,statistics,status' ;

		$.ajax({
			  url: query,
			  dataType: "jsonp",
			  success: function(data){
					   parseresults(data)
			  },
			  error: function(jqXHR, textStatus, errorThrown) {
				  alert (textStatus, + ' | ' + errorThrown);
			  }
		  });


}

function parseresults(data) {
		Inputs.Title.val(data.items[0].snippet.title);
		Inputs.Description.val(data.items[0].snippet.description);
		Inputs.Artist.val(data.items[0].snippet.channelTitle);
		Inputs.TotalTime = YTDurationToSeconds(data.items[0].contentDetails.duration);
		SetRange();
		// console.log(data);
		// console.log(YTDurationToSeconds(data.items[0].contentDetails.duration));
}
