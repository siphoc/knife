<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */

// Redefine the exception handler if we are not running in the command line.
set_exception_handler('knifeExceptionHandler');

/**
 * Prints out the thrown exception in a more readable manner for a person using
 * a web browser.
 *
 * @param	KnifeException $exception
 */
function knifeExceptionHandler(Exception $exception)
{
	// specific name
	$name = (is_callable(array($exception, 'getName'))) ? $exception->getName() : get_class($exception);

	/*
	 * Print the exception in a readable way
	 */
	echo $exception->getMessage();
	echo "\n";

	// stop the script
	exit;
}
