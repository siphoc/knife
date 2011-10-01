<?php
/**
 * This source file is a part of Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */
asdf
/*
 * Developer mode
 */
define('DEV_MODE', true);

// define the CLI path
define('CLIPATH', dirname(__FILE__) . '/');

/*
 * Get the base class
 */
require_once 'knife/knife.php';

/*
 * Start the tool
 */
new Knife($argv);
