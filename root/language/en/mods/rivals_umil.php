<?php
/**
*
* phpRivalsMOD [English]
*
* @package language
* @version $Id: rivals_umil.php 4933 2011-08-09 14:40:11Z Soshen $
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_PHPRIVALS_MOD'                     => 'phpRivals MOD',
	'INSTALL_ACP_PHPRIVALS_MOD'             => 'Install phpRivals MOD',
	'INSTALL_ACP_PHPRIVALS_MOD_CONFIRM'     => 'Are you ready to install the phpRivals MOD?',
	'UNINSTALL_ACP_PHPRIVALS_MOD'			=> 'Uninstall phpRivals MOD',
	'UNINSTALL_ACP_PHPRIVALS_MOD_CONFIRM'	=> 'Are you ready to uninstall the phpRivals MOD?  All settings and data saved by this mod will be removed!',
	'UPDATE_ACP_PHPRIVALS_MOD'				=> 'Update phpRivals MOD',
	'UPDATE_ACP_PHPRIVALS_MOD_CONFIRM'		=> 'Are you ready to update the phpRivals MOD?',
));

?>