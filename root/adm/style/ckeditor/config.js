/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	    config.toolbar = 'MyToolbar';

    config.toolbar_MyToolbar =
    [
        ['Preview'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','SpellChecker', 'Scayt'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		'/',
        ['Format','Font','FontSize'],
        ['Bold','Italic','Strike'],
		['TextColor','BGColor'],
		['NumberedList','BulletedList'],
        ['Outdent','Indent','Blockquote'],
        ['Link','Unlink','Anchor'],
        ['Maximize']
    ];

	
};
