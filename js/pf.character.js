var CalculatedFields = new Array();

var Lookup = {
	Abilities: ['STR','DEX','CON','INT','WIS','CHA'],
	PointBuy: {'7':-4,'8':-2,'9':-1,'10':0,'11':1,'12':2,'13':3,'14':5,'15':7,'16':10,'17':13,'18':17},
	BonusTypes: ['Untyped','Alchemical','Armor','Circumstance','Competance','Deflection','Dodge','Enhancement','Inherent','Insight','Luck','Morale','Natural Armor','Profane','Racial','Resistance','Sacred','Shield','Size','Trait'],
	StackingBonusTypes: ['Untyped','Dodge','Circumstance','Racial'],
	Alignment: ['NA','LG','NG','CG','LN','N','CN','LE','NE','CE'],
	AlignmentFull: ['None','Lawful Good','Neutral Good','Chaotic Good','Lawful Netural','True Neutral','Chaotic Neutral','Lawful Evil','Neutral Evil','Chaotic Evil']
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
// wrapper for invoking object's methods
function invokeMethod(object, method) {
	return object[method]();
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

function GetMod(num) {
	return Math.floor((num-10)/2);
}
function recalculateFields() {
	for (var i in CalculatedFields) {
		var field = CalculatedFields[i];
		var elem = document.getElementById(field.method+field.id);
		elem.value = invokeMethod(field.object,field.method);
	}
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
// Calculates complex fields
var Calculator = {
	GetClassString : function(levels) {
		var classes = this.parseLevels(levels,"Class");
		var counter = new Array();
		for (var i in classes) {
			if (counter[classes[i]] === undefined) {
				counter[classes[i]] = 1;
			} else {
				counter[classes[i]] += 1;
			}
		}
		var classString = '';
		for (var f in counter) {
			if (classString != '') {
				classString += '/';
			}
			classString += f + ' ' + counter[f];
		}
		return classString;
	},
	GetHD : function(levels) {
		return levels.length;
	},
	GetMaxHP : function(levels) {
		var max = 0;
		var maxArray = this.parseLevels(levels,"HitDie");
		for (var i in maxArray) {
			max += parseInt(maxArray[i]);
		}
		return max;
	},
	GetHP : function(levels) {
		var hp = 0;
		var hpArray = this.parseLevels(levels,"HP");
		for (var i in hpArray) {
			hp += parseInt(hpArray[i]);
		}
		return hp;
	},
	GetBAB : function(levels) {
		var bab = 0;
		var babArray = this.parseLevels(levels,"BAB");
		for (var i in babArray) {
			if (babArray[i] == 0) {
				bab += (1/2);
			} else if (babArray[i] == 1) {
				bab += (3/4);
			} else {
				bab += 1;
			}
		}
		return Math.floor(bab);
	},
	GetFort: function(levels) {
		return this.GetSave(this.parseLevels(levels,"Fort"));
	},
	GetRef: function(levels) {
		return this.GetSave(this.parseLevels(levels,"Ref"));
	},
	GetWill: function(levels) {
		return this.GetSave(this.parseLevels(levels,"Will"));
	},
	// Returns the BASE SAVE value from sub array of saves
	GetSave: function(arr) {
		var save = 0;
		var isGood = false;
		for (var i in arr) {
			if (arr[i] == 0) {
				save += (1/3);
			} else {
				if (!isGood) {
					save += 2;
					isGood = true;
				}
				save += (1/2);
			}
		}
		return Math.floor(save);
	},
	// Returns a sub array of property values from a collection of levels
	parseLevels: function(levels, property) {
		var values = new Array();
		for (var i in levels) {
			var lvl = levels[i];
			values[values.length] = lvl[property];
		}
		return values;
	},
	getFlyMod: function() {
		var ManeuverabilityArray = [0,-8,-4,0,4,8];
		var SizeArray = [8,6,4,2,0,-2,-4,-6,-8];

		return ManeuverabilityArray[Character.FlyAbility] + SizeArray[Character.Size];
	},
	getStealthMod: function() {
		var SizeArray = [16,12,8,4,0,-4,-8,-12,-16];
		return SizeArray[Character.Size];
	},
	GetSkillPointTotal: function(levels) {
		var total = this.parseLevels(levels, 'Skills').reduce(function (total,val) {return parseInt(total)+parseInt(val);});
		var HD = parseInt(this.GetHD(levels));
		total += Character.Abilities.INTMod() * HD;
		total += levels.filter(function (lvl) { return lvl.FC == 2 }).length;
		total += (parseInt(Character.HasBackgroundSkills) === 1 ? HD * 2 : 0);
		return total;
	},
	GetUsedSkillPoints: function(skills) {
		return skills.reduce(function (total, skill) {return (typeof(total) === 'object' ? parseInt(total.Ranks) : total) + parseInt(skill.Ranks);});
	},
	GetPointBuy: function(abilities) {
		var total = 0;
		for (var i in Lookup.Abilities) {
			var a = Lookup.Abilities[i];
			var v = parseInt(abilities[a]);
			total += Lookup.PointBuy[v];
		}
		return total;
	},
	GetBonuses : function(targetType,targetName,bonuses) {
		var resultArray = [];
		for (var i in bonuses) {
			var bonus = bonuses[i];
			if (bonus.TargetName === targetName && bonus.TargetType === targetType) {
				resultArray.push(bonus);
			}
		}
		return resultArray;
	},
	GetBonusValue : function(bonuses) {
		var total = 0;
		// filter active bonuses only
		var filteredArray = bonuses.filter(function(bonus) {return !!bonus.IsActive;});
		filteredArray.sort(function (a,b) {return b.Value - a.Value;});
		var stackingArray = [];
		for (var i in filteredArray) {
			var bonus = filteredArray[i];
			// If bonus stacks, add to total
			if (Lookup.StackingBonusTypes.indexOf(bonus.Type) !== -1) {
				total += parseInt(bonus.Value);
			// If first instance of non-stacking bonus type, add to stackingArray and add to total
			} else if (stackingArray.indexOf(bonus.Type) === -1) {
				total += parseInt(bonus.Value);
				stackingArray[(stackingArray.length)] = bonus.Type;
			}
		}
		return total;
	}
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
function InitCharacter(Char) {
    Char.type = "character";
    Char.Abilities.type = "abilities";

    Char.Abilities.STRTotal = function() {return parseInt(this.STR) + Character.GetBonusValue('AbilityScore','STR');};
    Char.Abilities.DEXTotal = function() {return parseInt(this.DEX) + Character.GetBonusValue('AbilityScore','DEX');};
    Char.Abilities.CONTotal = function() {return parseInt(this.CON) + Character.GetBonusValue('AbilityScore','CON');};
    Char.Abilities.INTTotal = function() {return parseInt(this.INT) + Character.GetBonusValue('AbilityScore','INT');};
    Char.Abilities.WISTotal = function() {return parseInt(this.WIS) + Character.GetBonusValue('AbilityScore','WIS');};
    Char.Abilities.CHATotal = function() {return parseInt(this.CHA) + Character.GetBonusValue('AbilityScore','CHA');};

    Char.Abilities.STRMod = function() {return GetMod(this.STRTotal());};
    Char.Abilities.DEXMod = function() {return GetMod(this.DEXTotal());};
    Char.Abilities.CONMod = function() {return GetMod(this.CONTotal());};
    Char.Abilities.INTMod = function() {return GetMod(this.INTTotal());};
    Char.Abilities.WISMod = function() {return GetMod(this.WISTotal());};
    Char.Abilities.CHAMod = function() {return GetMod(this.CHATotal());};

    Char.Abilities.PointBuy = function() {return Calculator.GetPointBuy(this);};

    Char.ClassString = function() {return Calculator.GetClassString(this.Levels);};
    Char.HD = function() {return Calculator.GetHD(this.Levels);};
    Char.HP = function() {return Calculator.GetHP(this.Levels) + this.HD() * this.Abilities.CONMod();};
    Char.MaxHP = function() {return Calculator.GetMaxHP(this.Levels) + this.HD() * this.Abilities.CONMod();};
    Char.BAB = function() {return Calculator.GetBAB(this.Levels);};
    Char.Fort = function() {return Calculator.GetFort(this.Levels) + this.Abilities.CONMod();};
    Char.Ref = function() {return Calculator.GetRef(this.Levels) + this.Abilities.DEXMod();};
    Char.Will = function() {return Calculator.GetWill(this.Levels) + this.Abilities.WISMod();};

    // Get a collection of bonuses that apply to a given target property
    Char.GetBonuses = function(targetName, targetType) {return Calculator.GetBonuses(targetType,targetName,this.Bonuses);};
    Char.GetBonusValue = function(targetName, targetType) {return Calculator.GetBonusValue(this.GetBonuses(targetType,targetName));};

    Char.SkillPoints = function() {return Calculator.GetSkillPointTotal(this.Levels);};
    Char.UsedSkillPoints = function() {return Calculator.GetUsedSkillPoints(this.Skills);};

}
