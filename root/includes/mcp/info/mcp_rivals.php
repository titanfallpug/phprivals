<?php
/**
*
* @package mcp
* @version $Id$
* @copyright (c) 2011 Soshen <nipponart.org>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class mcp_rivals_info
{
	function module()
	{
		return array(
			'filename'	=> 'mcp_rivals',
			'title'		=> 'MCP_RIVALS',
			'version'	=> '2.0.0',
			'modes'		=> array(
				'main'			=> array('title' => 'MCP_RIVALS_MAIN', 'auth' => 'aclf_m_rivals', 'display' => true, 'cat' => array('MCP_RIVALS')),
				'edit_match'	=> array('title' => 'MCP_RIVALS_EDIT_MATCH', 'auth' => 'aclf_m_rivals', 'display' => false, 'cat' => array('MCP_RIVALS')),
				'ipwhois'		=> array('title' => 'MCP_RIVALS_IP_WHOIS', 'auth' => 'aclf_m_rivals', 'display' => false, 'cat' => array('MCP_RIVALS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>