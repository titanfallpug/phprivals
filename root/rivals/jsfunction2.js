/****************************************************
*	 phpRivalsMOD By Soshen javascript addon		*
****************************************************/

function getParentElement(starterElement, classPattern, testTagName) {
//
var currElement = starterElement;
var foundElement = null;
while(!foundElement && (currElement = currElement.parentNode)) {
if ((classPattern && (currElement.className.indexOf(classPattern) != -1)) || (testTagName && (testTagName.toLowerCase() == currElement.tagName.toLowerCase())))
{
foundElement = currElement;
}
}
return foundElement;
}

/* TABS MENU */
function ShowSection(anchor) {
    // got from a web site that i do not remember url, sorry ^^'
    
    var voiceclicked = null;

    var tabmenu = getParentElement(anchor,"tabs-nav");
    var anchorcluster = tabmenu.getElementsByTagName("a");

    var idOFtab = [];
    
    for (var i=0; (anchorSelected = anchorcluster[i]); i++) {
        var theAnchor = anchorSelected.href.substring(anchorSelected.href.indexOf("#") + 1, anchorSelected.href.length);
        var theParent = getParentElement(anchorSelected,null,"li");
        if (anchorSelected == anchor) {
            voiceclicked = theAnchor;
            theParent.className = "active";
        } else {
            theParent.className = "";
        }
        idOFtab.push(theAnchor);
    }
    
    for (var j=0; (currTabId = idOFtab[j]); j++) {
        var element = document.getElementById("view-" + currTabId);
        if (!element) {continue;}
        if (currTabId == voiceclicked) {
            element.className="tabs-panel panel-active";
        } else {
            element.className="tabs-panel";
        }
    }

    return false;
}

// http://bontragerconnection.com/ and http://willmaster.com/
// Version: July 28, 2007
var cX = 0; var cY = 0; var rX = 0; var rY = 0;
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}
if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
else { document.onmousemove = UpdateCursorPosition; }
function AssignPosition(d) {
if(self.pageYOffset) {
rX = self.pageXOffset;
rY = self.pageYOffset;
}
else if(document.documentElement && document.documentElement.scrollTop) {
rX = document.documentElement.scrollLeft;
rY = document.documentElement.scrollTop;
}
else if(document.body) {
rX = document.body.scrollLeft;
rY = document.body.scrollTop;
}
if(document.all) {
cX += rX;
cY += rY;
}
d.style.left = (cX+10) + "px";
d.style.top = (cY+10) + "px";
}
function HideContent(d) {
if(d.length < 1) { return; }
document.getElementById(d).style.display = "none";
}
function ShowContent(d) {
if(d.length < 1) { return; }
var dd = document.getElementById(d);
AssignPosition(dd);
dd.style.display = "block";
}
function ReverseContentDisplay(d) {
if(d.length < 1) { return; }
var dd = document.getElementById(d);
AssignPosition(dd);
if(dd.style.display == "none") { dd.style.display = "block"; }
else { dd.style.display = "none"; }
}
//-->

// RANDOM GUID E UAC
function randomGUID() {
	var chars = "0123456789abcdefghiklmnopqrstuvwxyz";
	var string_length = 8;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	document.forms['editclan'].guid.value = randomstring;
}

function randomUAC() {
	var chars = "0123456789";
	var string_length = 6;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	document.forms['editclan'].uac.value = randomstring;
}