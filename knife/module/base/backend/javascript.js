if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the subname module
 *
 * @author	authorname
 */
jsBackend.subname =
{
	// init, something like a constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.subname.init);