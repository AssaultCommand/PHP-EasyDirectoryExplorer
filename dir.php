<!--
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
	<style>
		body
		{
			margin: 0;
		}
		h1
		{
			font-family: Arial, Verdana, sans-serif;
		}
		p
		{
			font-family: Arial, Verdana, sans-serif;
			font-size: 12px;
			margin-top: 10px;
			margin-bottom: 12px;
		}
		#header
		{
			z-index: 500;
			width: 100%; 
			padding-left: 5px; 
			padding-top: 5px; 
		}
		#menu
		{
			z-index: 500;
			width: 100%; 
			padding: 1px; 
			background: #adadad;
		}
		.menuSort
		{
			margin-top: -27px; 
			margin-right: 20px; 
			text-align: right;
		}
		#fixPage
		{
			z-index: 500;
			width: 100%; 
			padding: 1px; 
			visibility: hidden;
			display: none;
		}
		.dir
		{
			width: 100%; 
			padding: 1px; 
			background: #dedede;
		}
		.dir:hover
		{
			background: #cdcdcd;
		}
		.file
		{
			width: 100%; 
			padding: 1px; 
			background: #efefef;
		}
		.file:hover
		{
			background: #cdcdcd;
		}
		.toggleDir
		{
			margin-top: -27px; 
			margin-right: 20px; 
			text-align: right;
			cursor: pointer;
		}
		.anchorDir
		{
			margin-top: -27px; 
			margin-right: 45px; 
			text-align: right;
			cursor: pointer;
		}
		.fileDetails
		{
			margin-top: -27px; 
			margin-right: 20px; 
			text-align: right;
			color: #aaa;
			pointer-events: none;
		}
		.footerText
		{
			color: #999;
		}
	</style>
	<script>
		function toggleDir(dirID)
		{
			console.log(dirID);
			dirDiv = document.getElementById(dirID);
			dirToggle = document.getElementById("toggle" + dirID);
			if (dirDiv.style.display == "block")
			{
				dirDiv.style.display = "none";
				dirToggle.innerHTML = "<";
			} else
			{
				dirDiv.style.display = "block";
				dirToggle.innerHTML = "V";
			}
		}
		
		window.onscroll = function (event) 
		{	
			if(document.body.scrollTop > document.getElementById('header').offsetHeight)
			{
				document.getElementById('menu').style.position = "fixed";
				document.getElementById('menu').style.top = "0";
				document.getElementById('fixPage').style.display= "block";
			}
			else
			{
				document.getElementById('menu').style.position = "relative";
				document.getElementById('fixPage').style.display = "none";
			}
		}
	</script>
</head>
<body>
	<?php
function getDirs($dir)
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
			array_push($GLOBALS['dirfiles'], array($dirfile, $dir, $filesize, $filedate));
			if(is_dir($dir.'/'.$dirfile)) 
			{
				getDirs($dir.'/'.$dirfile);
			}
		}
	}
}
 

function sortDirs()
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

function printDirs($dir, $isSub)
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
					echo '<div id="a-'.$dir1.$dirfilename.'" class="file"><p style="margin-left: '.$subPrefix.'px;"><a href="'.$dir1.$dirfilename.'">/'.$dir1.$dirfilename.'</a></p></div>';
					echo '<p class="fileDetails">'.$filedetails.'</p></div>';
	
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
					echo '<div id="a-'.$dir1.$dirfilename.'" class="dir"><p style="margin-left: '.$subPrefix.'px;"><a href="'.$dir1.$dirfilename.'"><b>/'.$dir1.$dirfilename.'</b></a></p>';
					echo '<p class="fileDetails">'.$filedetails.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
					echo '<p class="anchorDir" onclick="location.href = \'#a-'.$dir1.$dirfilename.'\';">A</p>';
					if (isset($_GET['t']) && $_GET['t'] == 'c')
					{
						echo '<p class="toggleDir" id="toggle'.$dir1.$dirfilename.'"onclick="toggleDir(\''.$dir1.$dirfilename.'\');">&#60;</p>';
						echo '</div><span id="'.$dir1.$dirfilename.'" style="display: none;">';
					}
					else
					{
						echo '<p class="toggleDir" id="toggle'.$dir1.$dirfilename.'"onclick="toggleDir(\''.$dir1.$dirfilename.'\');">V</p>';
						echo '</div><span id="'.$dir1.$dirfilename.'" style="display: block;">';
					}
					printDirs($dir.'/'.$dirfilename, $isSub + 1);
					echo '</span>';
				}
			}
		}
	}
}

//Echo Header
echo '<div id="header"><h1>PHP Easy Directory Explorer</h1>';
echo '<p><b>Current relative path: '.$_SERVER['PHP_SELF'].'</b><br /><br /></p></div>';

//Add a sticky menu
echo '<div id="menu">';
echo '<p style="margin-left: 10px;"><a href="#">Go to top</a></p> ';
echo '<p class="menuSort" style="margin-left: 10px;">Sort&nbsp;by: &nbsp;&nbsp; ';
echo '<a href="'.$_SERVER['PHP_SELF'].'?s=1">Name&nbsp;(Asc)</a>&nbsp;&nbsp;/&nbsp;&nbsp;';
echo '<a href="'.$_SERVER['PHP_SELF'].'?s=2">Name&nbsp;(Desc)</a>';

echo ' &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;';

echo '<a href="'.$_SERVER['PHP_SELF'].'?s=3">Type&nbsp;(Asc)</a>&nbsp;&nbsp;/&nbsp;&nbsp;';
echo '<a href="'.$_SERVER['PHP_SELF'].'?s=4">Type&nbsp;(Desc)</a>';

echo ' &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;';

echo '<a href="'.$_SERVER['PHP_SELF'].'?s=5">Size&nbsp;(Asc)</a>&nbsp;&nbsp;/&nbsp;&nbsp;';
echo '<a href="'.$_SERVER['PHP_SELF'].'?s=6">Size&nbsp;(Desc)</a>';

echo ' &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;';

echo '<a href="'.$_SERVER['PHP_SELF'].'?s=7">Date&nbsp;(Asc)</a>&nbsp;&nbsp;/&nbsp;&nbsp;';
echo '<a href="'.$_SERVER['PHP_SELF'].'?s=8">Date&nbsp;(Desc)</a>';

echo '</p></div>';

//Add a fix for sticky menu
echo '<div id="fixPage"><p style="margin-left: 10px;">Sort by: &nbsp;&nbsp;&nbsp; <p class="menuSort" style="margin-left: 10px;">Sort&nbsp;by: &nbsp;&nbsp;&nbsp; <a href="1">Name&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;<a href="2">Name&nbsp;(Desc) &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;<a href="3">Size&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;<a href="4">Size&nbsp;(Desc) &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;Date&nbsp;Modified&nbsp;(Asc)&nbsp;&nbsp;/&nbsp;&nbsp;Date&nbsp;Modified&nbsp;(Desc)</p></div></div>';

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
getDirs('.');
sortDirs();
printDirs('.', 0);
	?>
	<br />
	<br />
	<p class="footerText">
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
	</p>
</body>
</html>