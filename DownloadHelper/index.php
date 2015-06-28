<?php
	function GetBlacklist()
	{
		$blacklist = file_get_contents('EDEblacklist.txt');
		$blacklist = explode("\n", $blacklist);
		return $blacklist;
	}	
	
	function GetDirs($dir)
	{
		$dirfiles1 = scandir($dir);
		
		//Add files and details to new array
		foreach($dirfiles1 as $dirfile)
		{
			if($dirfile != '.' && $dirfile != '..')
			{
				$dir1 = explode('./', $dir);
				if ($dir1[1] != null)
				{
					$dir1 = $dir1[1].'/';
				}
				else
				{
					$dir1 = '';
				}
				if(is_dir($dir.'/'.$dirfile))
				{
					foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir1.$dirfile)) as $file)
					{
						$filesize += $file->getSize();
					}
				}
				else
				{
					$filesize = filesize($dir1.$dirfile);
				}
				$filesize = round($filesize,2);
				$filedate = date('Y-m-d H:i:s', filectime($dir1.$dirfile));
				if(!in_array('/'.$dir1.$dirfile, $GLOBALS['blacklist']))
				{
					array_push($GLOBALS['dirfiles'], array($dirfile, $dir, $filesize, $filedate));
				}
				if(is_dir($dir.'/'.$dirfile)) 
				{
					GetDirs($dir.'/'.$dirfile);
				}
			}
		}
	}
	
	function SortDirs()
	{
		// Define the custom sort function
		function custom_sort($a,$b) 
		{
			switch($_GET['s'])
			{
				case 1:
					return $a[0]>$b[0];
					break;
				case 2:
					return $a[0]<$b[0];
					break;
				case 3:
					
					$a[0] = pathinfo($a[0], PATHINFO_EXTENSION);
					$b[0] = pathinfo($b[0], PATHINFO_EXTENSION);
					
					return $a[0]>$b[0];
					break;
				case 4:
					$a[0] = pathinfo($a[0], PATHINFO_EXTENSION);
					$b[0] = pathinfo($b[0], PATHINFO_EXTENSION);
					
					return $a[0]<$b[0];
					break;
				case 5:
					return $a[2]>$b[2];
					break;
				case 6:
					return $a[2]<$b[2];
					break;
				case 7:
					return $a[3]>$b[3];
					break;
				case 8:
					return $a[3]<$b[3];
					break;
				default:
					return $a[0]>$b[0];
			}
		}
		usort($GLOBALS['dirfiles'], "custom_sort");
	}
	
	function zipDirs($dir)
	{
		$a = $b;
	}
	
	function PrintDirs($dir, $isSub)
	{
		//$dirfiles = scandir($dir);
		
		
		//Echo all files in directory
		foreach($GLOBALS['dirfiles'] as $dirfile)
		{
			if ($dir == $dirfile[1])
			{
				$dir = $dirfile[1];
				$dirfilename = $dirfile[0];
				$dirfiledate = $dirfile[3];
				$dirfilesize = $dirfile[2];
				if($dirfilename != '.' && $dirfilename != '..')
				{
					if(!is_dir($dir.'/'.$dirfilename))
					{
						$dir1 = explode('./', $dir);
						if ($dir1[1] != null)
						{
							$dir1 = $dir1[1].'/';
						}
						else
						{
							$dir1 = '';
						}
						$subPrefix = $isSub*40+10;
						$sizeunit = " B";
						if ($dirfilesize >= 1024)
						{
							$dirfilesize = $dirfilesize/1024;
							$sizeunit = " KB";
							if ($dirfilesize >= 1024)
							{
								$dirfilesize = $dirfilesize/1024;
								$sizeunit = " MB";
								if ($dirfilesize >= 1024)
								{
									$dirfilesize = $dirfilesize/1024;
									$sizeunit = " GB";
									if ($dirfilesize >= 1024)
									{
										$dirfilesize = $dirfilesize/1024;
										$sizeunit = " TB";
									}
								}
							}
						}
						$filesize = round($dirfilesize, 2).$sizeunit;
						$filedate = date('d-m-y H:i:s', filectime($dir1.$dirfilename));
						$filedetails = $filedate.'&nbsp;&nbsp;<span style="font-size: 24px; position: absolute; margin-left: -3px; margin-top: -8px;">|</span>&nbsp;&nbsp;'.$filesize;
						echo '<div id="a-'.$dir1.$dirfilename.'" oncontextmenu="event.stopPropagation(); customContextMenu(this); return false;" class="file"><p style="margin-left: '.$subPrefix.'px;"><a href="'.$dir1.$dirfilename.'">/'.$dir1.$dirfilename.'</a></p><p class="fileDetails">'.$filedetails.'</p></div></div>';
		
					}
				}
			}
		}
		
		//Echo all subdirectories
		foreach($GLOBALS['dirfiles'] as $dirfile)
		{
			if ($dir == $dirfile[1])
			{
				$dir = $dirfile[1];
				$dirfilename = $dirfile[0];
				$dirfiledate = $dirfile[3];
				$dirfilesize = $dirfile[2];
				if($dirfilename != '.' && $dirfilename != '..')
				{
					if(is_dir($dir.'/'.$dirfilename)) 
					{
						$dir1 = explode('./', $dir);
						if ($dir1[1] != null)
						{
							$dir1 = $dir1[1].'/';
						}
						else
						{
							$dir1 = '';
						}
						
						$subPrefix = $isSub*40+10;
						$sizeunit = " B";
						switch(TRUE)
						{
							case ($dirfilesize >= 1024):
								$dirfilesize = $dirfilesize/1024;
								$sizeunit = " KB";
								break;
							case ($dirfilesize >= 1024):
								$dirfilesize = $dirfilesize/1024/1024;
								$sizeunit = " MB";
								break;
							case ($dirfilesize >= 1024):
								$dirfilesize = $dirfilesize/1024/1024/1024;
								$sizeunit = " GB";
								break;
							case ($dirfilesize >= 1024):
								$dirfilesize = $dirfilesize/1024/1024/1024/1024;
								$sizeunit = " TB";
								break;
							default:
								$sizeunit = " B";
						}
						
						$filesize = round($dirfilesize, 2).$sizeunit;
						$filedate = date('d-m-y H:i:s', filectime($dir1.$dirfilename));
						
						$filedetails = $filedate.'&nbsp;&nbsp;<span style="font-size: 24px; position: absolute; margin-left: -3px; margin-top: -8px;">|</span>&nbsp;&nbsp;'.$filesize;
						
						echo '<div id="a-'.$dir1.$dirfilename.'" oncontextmenu="event.stopPropagation(); customContextMenu(this); return false;" class="dir"><p style="margin-left: '.$subPrefix.'px;"><a href="'.$dir1.$dirfilename.'"><b>/'.$dir1.$dirfilename.'</b></a></p>';
						echo '<p class="fileDetails">'.$filedetails.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
						echo '<p class="anchorDir" onclick="location.href = \'#a-'.$dir1.$dirfilename.'\';">A</p>';
						echo '<p class="toggleDir" id="toggle'.$dir1.$dirfilename.'" onclick="toggleDir(\''.$dir1.$dirfilename.'\');">&#60;</p>';
						echo '</div><span id="'.$dir1.$dirfilename.'" style="display: none;">';
						PrintDirs($dir.'/'.$dirfilename, $isSub + 1);
						echo '</span>';
					}
				}
			}
		}
	}
	
	function CreateZipDirs($dir, $zip_file) 
	{
		$zip = new ZipArchive;
		if (true !== $zip->open($zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE))
		{
			return false;
		}
		ZipDir($dir, $zip);
		return $zip;
	}
	
	function ZipDir($dir, $zip, $relative_path = DIRECTORY_SEPARATOR)
	{
		//$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		if ($handle = opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (file === '.' || $file === '..')
				{
					continue;
				}
				if (is_file($dir . $file))
				{
					$zip->addFile($dir . $file, $file);
				}
				else if (is_dir($dir . $file))
				{
					ZipDir($dir . $file, $zip, $relative_path . $file);
				}
			}
		}
		closedir($handle);
	}

	if(isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'default';
	}
	
	switch($action)
	{
		case 'zipdir':
			if(isset($_GET['dir']))
			{
				CreateZipDirs('/home/armorwat/public_html/school/PHP31/week2', 'files.zip');
			}
			break;
		case 'zipfile':
			
			break;
		default:
			echo '	<!--
				PHP Easy File Explorer
				Author: Caspar Neervoort
				Creative Commons Attribution-NoDerivatives 4.0 International License.
				
				REQUIRES: PHP 5.3 or higher.
				You may use this anywhere and in any way you want, as long as you don`t alter the license information.
				If you want to adapt this script, please contact me for permission and further information.
				-->
				
				<html>
				
				<head>
					<title>Easy Directory Explorer</title>
					<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
					<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
					<link href="http://www.armorwatcher.com/PHPEasyDirectoryExplorer/Sources/style.css" rel="stylesheet" type="text/css">
					<script src="http://www.armorwatcher.com/PHPEasyDirectoryExplorer/Sources/clientside.js"></script>
				</head>
				
				<body onclick="hideContextMenu();" oncontextmenu="hideContextMenu();">
					<div id="header"><h1>PHP Easy Directory Explorer</h1>
					<p><b>Current relative path: '.$_SERVER['PHP_SELF'].'</b><br /><br /></p></div>';
			
			//Add a sticky menu
			echo '	<div class="menu">
					<p style="margin-left: 10px;"><a href="#">Go to top</a></p>
					<p class="menuSort" style="margin-left: 10px;">
						<a href="'.$_SERVER['PHP_SELF'].'?s=1">Name&nbsp;(Asc)</a>&nbsp;/&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?s=2">Name&nbsp;(Desc)</a>
						 - 
						<a href="'.$_SERVER['PHP_SELF'].'?s=3">Type&nbsp;(Asc)</a>&nbsp;/&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?s=4">Type&nbsp;(Desc)</a>
						 - 
						<a href="'.$_SERVER['PHP_SELF'].'?s=5">Size&nbsp;(Asc)</a>&nbsp;/&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?s=6">Size&nbsp;(Desc)</a>
						 - 
						<a href="'.$_SERVER['PHP_SELF'].'?s=7">Date&nbsp;(Asc)</a>&nbsp;/&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?s=8">Date&nbsp;(Desc)</a>
					</p>
				</div>
				
				<div id="fixPage">
				<p style="margin-left: 10px;">Sort by: &nbsp; <p class="menuSort" style="margin-left: 10px;">Sort&nbsp;by: &nbsp;&nbsp;&nbsp;Name&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;Name&nbsp;(Desc) &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;Size&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;Size&nbsp;(Desc) &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;Date&nbsp;Modified&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;Date&nbsp;Modified&nbsp;(Desc)</p></div></div>				
				<section class="explorer">';
			
			if(strpos($_SERVER['PHP_SELF'], 'dir.php') && file_exists('../dir.php') && file_exists('/dir.php'))
			{
				echo '<div class="dir"><p style="margin-left: 10px"><a href="../dir.php"><b>Go Up</b></a> &nbsp;&nbsp; <a href="/dir.php"><b>Go To Root</b></a></p></div>';
			}
			else if(strpos($_SERVER['PHP_SELF'], 'dir.php') && file_exists('../dir.php'))
			{
				echo '<div class="dir"><p style="margin-left: 10px"><a href="../dir.php"><b>Go Up</b></a> &nbsp;&nbsp; <a href="/"><b>Go To Root</b></a></p></div>';
			}
			else if(strpos($_SERVER['PHP_SELF'], 'dir.php') && file_exists('/dir.php'))
			{
				echo '<div class="dir"><p style="margin-left: 10px"><a href="../"><b>Go Up</b></a> &nbsp;&nbsp; <a href="/dir.php"><b>Go To Root</b></a></p></div>';
			}
			else
			{
				echo '<div class="dir"><p style="margin-left: 10px"><a href=".."><b>Go Up</b></a> &nbsp;&nbsp; <a href="/"><b>Go To Root</b></a></p></div>';
			}
			
			//Initiate the functions
			$dirfiles = array();
			$blacklist = GetBlacklist();
			GetDirs('.');
			SortDirs();
			PrintDirs('.', 0);
			if(isset($_GET['bl']))
			{
				echo '<div class="blacklist">a</div>';
			}
		
			echo '	</section>
				<br />
				<br />
				<div id="customMenu" onclick="event.stopPropagation();" oncontextmenu="return false;">
					<ul>
						<li>
							<span></span>
						</li>
						<li>
							<a id="menuOpen" href="#">Open&nbsp;link</a>
						</li>
						<li>
							<a id="menuOpenTab" href="#" target="_blank">Open&nbsp;link&nbsp;in&nbsp;new&nbsp;tab</a>
						</li>
						<li>
							<a id="menuOpenWindow" href="#" target="_new">Open&nbsp;link&nbsp;in&nbsp;new&nbsp;window</a>
						</li>
						<li>
							<span class="divider"></span>
							<span></span><span></span>
						</li>
						<li>
							<a id="menuSaveLink" href="#">Save&nbsp;link&nbsp;as...</a>
						</li>
						<li>
							<span class="divider"></span>
							<span></span><span></span>
						</li>
						<li>
							<a id="menuBlacklist" href="#">Blacklist <em style="display: none;">&#9658;</em></a>
						</li>
						<li>
							<span></span>
						</li>
					<ul>
				</div>
				
				<footer class="footerText">
					<a rel="license" href="http://creativecommons.org/licenses/by-nd/4.0/deed.en_US">
						<img alt="Creative Commons License" style="border-width:0;" src="http://i.creativecommons.org/l/by-nd/4.0/80x15.png" />
					</a>
					&nbsp;&nbsp;
					<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">
						PHP Easy Directory Explorer
					</span> 
					by 
					<a xmlns:cc="http://creativecommons.org/ns#" href="https://twitter.com/__Caspar__" property="cc:attributionName" rel="cc:attributionURL">
						Caspar Neervoort
					</a> 
					is licensed under a 
					<a rel="license" href="http://creativecommons.org/licenses/by-nd/4.0/deed.en_US">
						Creative Commons Attribution-NoDerivatives 4.0 International License
					</a>.
				</footer>
			</body>
			</html>';
	}
?>