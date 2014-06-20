<?php
##############################################################
# FILENAME  : funzioni_decerto.php
# COPYRIGHT : (c) 2010, Soshen <nipponart.org>
# http://opensource.org/licenses/gpl-license.php GNU Public License
##############################################################
if ( !defined ( 'IN_PHPBB' ) )
{
	exit;
}

	function decerto($nome_corto, $tipoladder){
	
	global	$db, $user, $template;
	global	$phpbb_root_path, $phpEx;
	// 1 decerto - 2 cpc su edit dec
	// 1 decerto - 2 cpc su ladder
	
		if ($tipoladder == 1) {

                // modi
				$sql2	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = $tipoladder AND nome_corto = '$nome_corto' ORDER BY RAND() LIMIT 0,1";
				$result2	= $db->sql_query ( $sql2 );
				$row2	= $db->sql_fetchrow ( $result2 );
				$inter1 = $row2['decerto_interid'];
				$mode1 = $row2['decerto_mode'];
				$db->sql_freeresult ( $result2 );
				
				$sql3	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = $tipoladder AND nome_corto = '$nome_corto' AND decerto_interid != $inter1 LIMIT 0,1";
				$result3	= $db->sql_query ( $sql3 );
				$row3	= $db->sql_fetchrow ( $result3 );
				$inter2 = $row3['decerto_interid'];
				$mode2 = $row3['decerto_mode'];
				$db->sql_freeresult ( $result3 );
				
				$sql4	= "SELECT * FROM " . DECERTO_CAT. " WHERE cpc = $tipoladder AND nome_corto = '$nome_corto' AND decerto_interid != $inter1 AND decerto_interid != $inter2 ORDER BY RAND() LIMIT 0,1";
				$result4	= $db->sql_query ( $sql4 );
				$row4	= $db->sql_fetchrow ( $result4 );
				$mode3 = $row4['decerto_mode'];
				$db->sql_freeresult ( $result4 );
				
				$ordine = "1) $mode1 2) $mode2 3) $mode3";
				
				// mappa 1
				$sql_a	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '$nome_corto' AND decerto_interid = 1 ORDER BY RAND() LIMIT 0,1";
				$result_a = $db->sql_query ( $sql_a );
				$row_a	= $db->sql_fetchrow ( $result_a );
				$mappa1 = $row_a['decerto_interid']; //////////////
				$db->sql_freeresult ( $result_a );
				// mappa 2
				$sql_b	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '$nome_corto' AND decerto_interid = 2 ORDER BY RAND() LIMIT 0,1";
				$result_b = $db->sql_query ( $sql_b );
				$row_b	= $db->sql_fetchrow ( $result_b );
				$mappa2 = $row_b['decerto_interid']; /////////////////
				$db->sql_freeresult ( $result_b );
				// mappa 3
				$sql_c	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 1 AND nome_corto = '$nome_corto' AND decerto_interid = 3 ORDER BY RAND() LIMIT 0,1";
				$result_c = $db->sql_query ( $sql_c );
				$row_c	= $db->sql_fetchrow ( $result_c );
				$mappa3 = $row_c['decerto_interid']; ///////////////
				$db->sql_freeresult ( $result_c );
			
		}
		else if ($tipoladder == 2) {
		// mappa 1
				$sql_a	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '$nome_corto' AND decerto_interid = 1 ORDER BY RAND() LIMIT 0,1";
				$result_a = $db->sql_query ( $sql_a );
				$row_a	= $db->sql_fetchrow ( $result2_a );
				$mappa1 = $row_a['decerto_interid']; //////////////
				$db->sql_freeresult ( $result_a );
				// mappa 2
				$sql_b	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '$nome_corto' AND decerto_interid = 1 ORDER BY RAND() LIMIT 0,1";
				$result_b = $db->sql_query ( $sql_b );
				$row_b	= $db->sql_fetchrow ( $result2_b );
				$mappa2 = $row_b['decerto_interid']; /////////////////
				$db->sql_freeresult ( $result_b );
				// mappa 3
				$sql_c	= "SELECT * FROM " . DECERTO_MAP . " WHERE decerto_cpc = 2 AND nome_corto = '$nome_corto' AND decerto_interid = 1 ORDER BY RAND() LIMIT 0,1";
				$result_c = $db->sql_query ( $sql_c );
				$row_c	= $db->sql_fetchrow ( $result2_c );
				$mappa3 = $row_c['decerto_interid']; ///////////////
				$db->sql_freeresult ( $result_c );
				
				$ordine = "-";
		} else {
		$mappa1 = "-";
		$mappa2 = "-";
		$mappa3 = "-";
		$ordine = "-";
		}
	}

?>