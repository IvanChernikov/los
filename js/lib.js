function SpinnerOptions() {
	var optsMiddle = {
	lines: 8, // The number of lines to draw
	length: 10, // The length of each line
	width: 4, // The line thickness
	radius: 4, // The radius of the inner circle
	corners: 1, // Corner roundness (0..1)
	rotate: 0, // The rotation offset
	direction: 1, // 1: clockwise, -1: counterclockwise
	color: '#f0a080', // #rgb or #rrggbb or array of colors
	speed: 1.2, // Rounds per second
	trail: 40, // Afterglow percentage
	shadow: true, // Whether to render a shadow
	hwaccel: false, // Whether to use hardware acceleration
	className: 'spinner', // The CSS class to assign to the spinner
	zIndex: 2e9, // The z-index (defaults to 2000000000)
	top: '50%', // Top position relative to parent
	left: '50%' // Left position relative to parent
};
	return optsMiddle;
}

function $id(id) {
	return document.getElementById(id);
}
// Server Object Class
function Server(id) {
	this.id = id;
	this.getStatus = function () {
		var spinner = new Spinner(SpinnerOptions()).spin($id('Server' + this.id));
		$.ajax({
			type: 'GET',
			url: '/server/getStatus/' + this.id,
			dataType: 'json',
			success: function (data) {
				spinner.stop();
				$('#Server' + data['id'] + ' .Count').val(data['count']);
				if (data['status']) {
					$('#Server' + data['id'] + ' .Status').val('Online').removeClass('red green').addClass('green');
				} else {
					$('#Server' + data['id'] + ' .Status').val('Offline').removeClass('red green').addClass('red');
				}
			}
		});
	};
	this.getInfo = function () {
		if ($('#Server' + this.id + ' .Detail').css('display') == 'none') {
			var spinner = new Spinner(SpinnerOptions()).spin($id('Server' + this.id));
			$.ajax({
				type: 'GET',
				url: '/server/getInfo/' + this.id,
				dataType: 'json'
			}).done(function (data) {
				spinner.stop()
				$('#Server' + data['id'] + ' .Detail').html(data['html']).slideDown();
			})
		} else {
			$('#Server' + this.id + ' .Detail').slideUp(400, function () {});
		}
	};

}

function getBlock(blockName, callback, params) {
	$.ajax({
		method: 'POST',
		url: '/ajax/block/' + blockName,
		dataType: 'html',
		data: params,
		success: callback
	});
}
function renderFlyout(html) {
	var modal = findFlyout();
	modal.innerHTML = html;
	$(modal).slideDown();
}
function findFlyout() {
	var modal = document.getElementById('Flyout');
	if ((modal === undefined) || modal === null) {
		modal = document.createElement('div');
		modal.id = 'Flyout';
		$(modal).insertAfter('nav');
	}
	$(modal).slideUp(0);
	return modal;
}

function renderReplyForm(postID) {
	if ($('#WallReplyForm' + postID).length === 0) {
		var params = {ID: postID};
		getBlock('WallReplyForm', function(html) {
			$('#Post' + postID).append(html);
		}, params);
	}
}
function replaceElement(elem, newTag) {
	var newElem = document.createElement(newTag);
	for(var i = 0, l = elem.attributes.length; i < l; ++i) {
		var nodeName  = elem.attributes.item(i).nodeName;
		var nodeValue = elem.attributes.item(i).nodeValue;

		newElem.setAttribute(nodeName, nodeValue);
	}
	newElem.innerHTML = elem.innerHTML;
	elem.parentNode.replaceChild(newElem, elem);
	return newElem;
}

function editPost(button, postID) {
	var contentElem = $id('Content'+postID);
	if (button.value == 'Save') {
		button.value = 'Edit';

		var value = contentElem.value;
		var par = replaceElement(contentElem, 'p');
		par.value = void(0);
		par.type = void(0);
		par.innerHTML = value;
		par.className = "sf box size-8 edge end darkish";
		par.style.minHeight = '';
		par.style.minWidth = '';
		par.style.maxWidth = '';

		$.ajax({
			method: 'POST',
			url: '/wall/update/' + postID,
			dataType: 'json',
			data: {'content' : value},
			success: function (data) { console.log(data); },
			error: function (a,b,c) {console.log(a,b,c); }
		});
	} else {
		button.value = 'Save';

		var value = contentElem.innerHTML;
		var input = replaceElement(contentElem, 'textarea');
		input.innerHTML = '';
		input.value = value;

		input.className = "sf box size-8 edge end";
		input.style.minHeight = '200px';
		input.style.minWidth = '80%';
		input.style.maxWidth = '80%';
	}
}

function tabSwitch(groupID, elemNum) {
	var group = $('#' + groupID);
	group.children('*:not(.tab-controls)').hide(); // Hides all tab-items
	$(group.children()[elemNum]).show(); // Shows selected tab
	var buttons = group.find('.tab-button');
	buttons.removeClass('tab-active');
	$(buttons[elemNum-1]).addClass('tab-active');
}
