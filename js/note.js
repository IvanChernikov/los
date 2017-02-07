function noteNode(type, id, text) {
    this.type = type;
    this.id = id;
    this.text = text;

    this.children = [];

    this.render = function (indent) {
        for (var i in this.children) {
            this.children[i].render(indent + 16);
        }
    }
}

function init() {
    console.log("INIT!!!!!");
}

$(document).ready(init);
