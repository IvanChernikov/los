/** Data and controls **/
// wrapper for invoking object's methods
function invokeMethod(object, method) {
	return object[method]();
}

// binds a field to an object property
// onchange trigger will update the property value
// any changes to the objects will automatically be committed to the server
function bindInput(object, property, id) {
	if (id === undefined) {
		id = '';
	}
	var elem = document.getElementById(property + id);
	if (elem.tagName.toLowerCase() == 'input') {
		switch (elem.type) {
			case 'checkbox':
				elem.checked = (object[property] == '1');
				elem.onchange = function () {object[property] = (elem.checked ? 1 : 0); saveChanges(object, property);};
				break;
			default:
				elem.value = object[property];
				elem.onchange = function () {object[property] = elem.value; saveChanges(object, property);};
				break;
		}
	} else if (elem.tagName.toLowerCase() == 'select') {
		elem.value = object[property];
		elem.onchange = function () {object[property] = elem.value; saveChanges(object, property);};
	}
}

// binds a field to an object method
// adds the field to the CalculatedFields collection
// on form onchange event, all registered fields will re-calculate
function bindInputMethod(object, method, id) {
	if (id === undefined) {
		id = '';
	}
	var elem = document.getElementById(method + id);
	elem.readOnly = true;
	elem.value = object[method]();
	CalculatedFields[CalculatedFields.length] = {'object' : object,'method': method, 'id': id};
}

// commits changes to server/database
function saveChanges(object, property) {
	$.ajax({
		method: 'POST',
		url: '/pathfinder/updateCharacter',
		data: {'id':object.ID, 'type': object.type, 'property': property, 'value': object[property]},
		success: function (data) {console.log(data);}
	});
}

// Contains all fields that have been bound by 'bindInputMethod'
var CalculatedFields = new Array();

// Recalculates fields within CalculatedFields array
function recalculateFields() {
	for (var i in CalculatedFields) {
		var field = CalculatedFields[i];
		var elem = document.getElementById(field.method+field.id);
		elem.value = invokeMethod(field.object,field.method);
	}
}

function updateLevel(direction) {
	var callback;
	var id;
	switch (direction) {
		case 'up':
			callback = function (data) {
				console.log(data);
				var level = JSON.parse(data);
				var root = document.getElementById('Levels');
				Character.Levels.push(level);
				createLevelBox(root, level);
			};
			id = Character.ID;
			break;
		case 'down':
			var oldLevel = Character.Levels.pop();
			callback = function (data) {
				console.log(data);
				var elem = document.getElementById('Level'+oldLevel.ID);
				var parent = elem.parentNode;
				parent.removeChild(elem);
			};
			id = oldLevel.ID;
			break;
	}
	$.ajax({
		method: 'POST',
		url: '/pathfinder/updateCharacterLevel',
		data: {'id':id, 'type': direction },
		success: function (data) {callback(data);}
	});
}
function addSkill() {
	var skill = document.getElementById('SkillList');
	var type = document.getElementById('SkillType');
	$.ajax({
		method: 'POST',
		url: '/pathfinder/addSkill',
		dataType: 'json',
		data: {'CharID': Character.ID, 'SkillID': skill.value, 'SkillType': type.value},
		success: function (data) {
			console.log(data);
			if (data != false) {
				var skills = document.getElementById('Skills');
				Character.Skills[Character.Skills.length] = data;
				createSkillBox(skills, data);
			}
		}
	});
	showSkillSelector();
}
function showSkillSelector () {
	var div = $('#AddSkillForm');
	if (div.css('display') === 'none') {
		$.ajax({
			method: 'POST',
			url: '/pathfinder/getSkillList',
			dataType: 'json',
			success: function (data) {
				var select = document.getElementById('SkillList');
				$(select).empty();
				for (var i in data) {
					var option = document.createElement('option');
					option.value = data[i]['ID'];
					option.innerHTML = data[i]['Name'];
					select.appendChild(option);
					div.show(400);
				}
			}
		});
	} else {
		div.hide(400);
	}
}

function showSkillEdit(id) {
	console.log('Skill#',id, event);
}

/*** DOM Creation ***/

// creates an input, with option to bind to property.
// @bind : object to bind to or false
function createInput(parent, name, id, size, bind) {
	var input = document.createElement('input');
	input.id = name+id;
	input.type = "text";
	input.className = "sf box size-" + size;
	parent.appendChild(input);
	if (bind) {
		bindInput(bind, name, id);
	}
	return input;
}
// creates an input, with option to bind to property.
// @bind : object to bind to or false
// @options : array of options
function createSelect(parent, name, id, size, bind, options) {
	var select = document.createElement('select');
	select.id = name+id;
	select.className = "sf box size-" + size;
	for (var i in options) {
		var option = document.createElement('option');
		option.value = i;
		option.innerHTML = options[i];
		select.appendChild(option);
	}
	parent.appendChild(select);
	if (bind) {
		bindInput(bind, name, id);
	}
	return select;
}
function createCheckbox(parent, name, id, size, bind) {
	var input = document.createElement('input');
	input.id = name+id;
	input.type = "checkbox";
	input.className = "sf box size-" + size;
	parent.appendChild(input);
	if (bind) {
		bindInput(bind, name, id);
	}
	return input;
}

// generates level detail view
function GetLevelsHtml(levels) {
	var root = document.getElementById('Levels');
	for (var i in levels) {
		createLevelBox(root,levels[i]);
	}
}
// generates view for a level instance
function createLevelBox(parent, level) {
	var root = document.createElement("div");
	root.className = "sf box";
	root.id = "Level" + level.ID;
	parent.appendChild(root);
	level.type = "level";
	createInput(root,"Class",level.ID,2, level);
	createInput(root,"HitDie",level.ID,1, level);
	createInput(root,"HP",level.ID,1, level);
	createSelect(root,"BAB",level.ID,1, level, ['Slow','Medium','Fast']);
	createSelect(root,"Fort",level.ID,1, level, ['Poor','Good']);
	createSelect(root,"Ref",level.ID,1, level, ['Poor','Good']);
	createSelect(root,"Will",level.ID,1, level, ['Poor','Good']);
	createInput(root,"Skills",level.ID,1, level);
	createSelect(root,"FC",level.ID,1, level, ['No','HP','Skill','Racial']);
}
function GetSkillsHtml(skills) {
	var root = document.getElementById('Skills');
	for (var i in skills) {
		createSkillBox(root, skills[i]);
	}
}
function createSkillBox(parent, skill) {
	var root = document.createElement("div");
	root.className = "sf box child-txt-center";
	root.id = "Skill" + skill.ID;
	parent.appendChild(root);
	skill.type = "skill";
	skill.Mod = function() {
		var abilityName = skill.Ability + 'Mod';
		var abilityMod = (Character.Abilities)[abilityName]();
		var skillMod = abilityMod + parseInt(skill.Ranks);
		skillMod += (skill.IsClass == 1 && skill.Ranks > 0 ? 3 : 0);
		skillMod += (skill.Skill.Name === 'Fly' ? Calculator.getFlyMod() : 0);
		skillMod += (skill.Skill.Name === 'Stealth' ? Calculator.getStealthMod() : 0);
		return skillMod;
	};

	createCheckbox(root,"IsClass",skill.ID,1,skill);
	var name = createInput(root,"Name",skill.ID,3);
	name.readOnly = true;
	name.value = skill.Skill.Name;
	if (skill.Skill.HasType === "1") {
		name.value += " (" + skill.Type + ")";
	}
	// createInput(root,"Ability",skill.ID,1,skill);
	createSelect(root,"Ability",skill.ID,1,skill, {'STR': 'STR', 'DEX': 'DEX', 'CON': 'CON', 'INT': 'INT', 'WIS': 'WIS', 'CHA': 'CHA'});

	var mod = createInput(root,"Mod",skill.ID,1);
	bindInputMethod(skill, 'Mod', skill.ID);

	createInput(root,"Ranks",skill.ID,1,skill);

	var edit = document.createElement('button');
	edit.className = "sf box size-1 button light";
	edit.onclick = function() { showSkillEdit(skill.ID);};
	edit.type = "button";
	edit.innerHTML = 'Edit';
	root.appendChild(edit);
}

/*** Math and Mechanics ***/
// Pathfinder general function container
var pf = {
    getMod : function (num) {
        num = parseInt(num);
        return Math.floor((num-10)/2);
    }

}
