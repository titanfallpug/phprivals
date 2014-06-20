<?php
/**
*
* @package Rivals
* @version $Id$
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

	function round_up($value, $places=0)
	{
		if ($places < 0)
		{
			$places = 0;
		}
		$mult = pow(10, $places);
    
	return ceil($value * $mult) / $mult;
    }
	
	function round_out($value, $places=0)
	{
		if ($places < 0)
		{
			$places = 0;
		}
		$mult = pow(10, $places);
		
    return ($value >= 0 ? ceil($value * $mult):floor($value * $mult)) / $mult;
    }
	
	function TagliaStringa($stringa, $max_char)
	{
		if(strlen($stringa)>$max_char)
		{
			$stringa_tagliata=substr($stringa, 0,$max_char);
			$last_space=strrpos($stringa_tagliata," ");
			$stringa_ok=substr($stringa_tagliata, 0,$last_space);
			return $stringa_ok."...";
		}
		else
		{
			return $stringa;
		}
	}

?>