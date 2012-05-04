<?php

/**
 * Global configuration options and constants of the FORK CMS
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */

/**
 * Site configuration
 */
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PROTOCOL']) ? (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https') : 'http');
define('SITE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '<projectname>.dev');
define('SITE_DEFAULT_TITLE', 'Fork CMS');
define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);
define('SITE_MULTILANGUAGE', true);
define('ACTION_GROUP_TAG', '@actiongroup');
define('ACTION_RIGHTS_LEVEL', '7');

/**
 * Spoon configuration
 */
define('SPOON_DEBUG', enableDebug());
define('SPOON_DEBUG_EMAIL', !isProductionSite() ? '' : '<projectname>-bugs@fork-cms.be');
define('SPOON_DEBUG_MESSAGE', 'Internal error.');
define('SPOON_CHARSET', 'utf-8');

/**
 * Fork configuration
 */
define('FORK_VERSION', '<version>');

/**
 * Database configuration
 */
if(isProductionSite())
{
	require_once dirname(__FILE__) . '/globals_production.php';
}
elseif(file_exists(dirname(__FILE__) . '/globals_staging.php'))
{
	require_once dirname(__FILE__) . '/globals_staging.php';
}
else
{
	define('DB_TYPE', 'mysql');
	define('DB_PORT', '3306');
	define('DB_DATABASE', '<projectname>');
	define('DB_HOSTNAME', '');
	define('DB_USERNAME', '');
	define('DB_PASSWORD', '');
}

/**
 * Check to see if we should enable debug or not.
 */
function enableDebug()
{
	/*
 	 * In non-production sites, debug mode should always be enabled.
	 */
	if(!isProductionSite()) return true;

	/*
	 * For production environments, we sometimes want to use the site with
	 * debug disabled. This enables us to do so.
	 */
	if(isset($_GET['enable_debug'])) return (bool) $_GET['enable_debug'];
	else return false;
}

/**
 * @return bool Whether or not we're running the site in production.
 */
function isProductionSite()
{
	return (file_exists(dirname(__FILE__) . '/globals_production.php'));
}

/**
 * Path configuration
 */
define('PATH_WWW', dirname(__FILE__) . '/..');
define('PATH_LIBRARY', dirname(__FILE__));
