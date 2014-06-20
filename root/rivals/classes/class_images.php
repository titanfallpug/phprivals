<?php
##############################################################
# FILENAME  : class_images.php
# COPYRIGHT : (c) 2011, Soshen <nipponart.org>
# http://opensource.org/licenses/gpl-license.php GNU Public License
##############################################################
if ( !defined ( 'IN_PHPBB' ) )
{
	exit;
}

/**
 * Function images
 */
 
function caricaimgresized ( $imgfield, $imgquality = 72, $finaldir, $finalwidth = 100 )
{
	 global	$user;
		/////////////////////
		$estenzionef = $_FILES["{$imgfield}"]["type"];
		$immaginename = $_FILES["{$imgfield}"]["name"];
		move_uploaded_file($_FILES["{$imgfield}"]["tmp_name"], $finaldir . $immaginename);
		$immagine = "{$finaldir}/{$immaginename}";
		chmod($immagine, 0777);
		$nomeimage   = str_replace(" ","_", substr($immaginename,0,5));
		$error	 = array();
		$ext         = substr(strrchr($immagine, '.'), 1);
		$randomizz   = mt_rand(1000, 9999);
		$finalname   = "{$nomeimage}_{$randomizz}";
		
		if (empty($estenzionef))
		{
		$error[] = $user->lang['XIMAGE'];
		}
		print_r($ext);
		
	  if (substr($estenzionef, 0, 5) == 'image')
	  {
		list($src_width, $src_height) = getimagesize($immagine);
		// Switch between type.
if ( $ext == 'gif' )
{
$base = imagecreatefromgif($immagine);
}
else if ( $ext == 'jpg' )
{
$base = imagecreatefromjpeg($immagine);
}
else if ( $ext == 'png' )
{
$base = imagecreatefrompng($immagine);
}
else if ( $ext == 'jpeg' )
{
$base = imagecreatefromjpeg($immagine);
}
else
{
$error[] = $user->lang['FILE_NO_IMAGE_SUPPORT'];
}
	
		        if ( ($src_width <= $finalwidth) && ($src_width >= $src_height) )
				{
				$big = $base;
				$sx = $src_width;
				$sy = $src_height;
				}
				else
				{
				$quoziente = $src_width / $finalwidth;
				$yok = ceil($src_height / $quoziente);
                $big = imagecreatetruecolor($finalwidth,$yok);
				imagecopyresized($big, $base, 0, 0, 0, 0, $finalwidth, $yok, $src_width, $src_height);
				$newbig = "{$finaldir}/{$finalname}.jpg";
				$sx = $finalwidth;
				$sy = $yok;
				}
		
		ImageJpeg ($big, $newbig, $imgquality);
		chmod($newbig, 0777);
		
		ImageDestroy ($big);
		ImageDestroy ($base);
		unlink ($immagine); 	
	  }
	  else
	  {
	  $error[] = trigger_error ( 'FILE_NO_IMG' );
	  }
	return array ($finalname, $ext, $sx, $sy, $error);
}

function upload_img ( $field, $finalpath, $kbmax = 102400, $smin = 100, $smax = 500, &$error )
{
		global $phpbb_root_path, $config, $db, $user, $phpEx;
		include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
		
		$upload = new fileupload('IMG_', array('jpg', 'jpeg', 'gif', 'png'), $kbmax, $smin, $smin, $smax, $smax, explode('|', $config['mime_triggers']));
		
		$foto = $upload->form_upload("{$field}");
		$foto->clean_filename('real', '', $user->data['user_id']);
		
		$destination = $finalpath;
		if (substr($destination, -1, 1) == '/' || substr($destination, -1, 1) == '\\')
			{
			$destination = substr($destination, 0, -1);
			}

		$destination = str_replace(array('../', '..\\', './', '.\\'), '', $destination);
		if ($destination && ($destination[0] == '/' || $destination[0] == "\\"))
			{
			$destination = '';
			}
		
		$foto->move_file($destination, true);
		$ext     = $foto->get('extension');
		$xname   = $foto->get('realname');
		$swidth  = $foto->get('width');
		$sheight = $foto->get('height');
		
		if (sizeof($foto->error))
			{
			$foto->remove();
			$error = array_merge($error, $foto->error);
			}
		
	return array($xname, $ext, $swidth, $sheight);	

}

?>
