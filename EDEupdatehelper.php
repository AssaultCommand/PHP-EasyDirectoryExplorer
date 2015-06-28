<!--
PHP Easy File Explorer
Author: Caspar Neervoort
Creative Commons Attribution-NoDerivatives 4.0 International License.

REQUIRES: PHP 5.3 or higher.
You may use this anywhere and in any way you want, as long as you don`t alter the license information.
If you want to adapt this script, please contact me for permission and further information.

INSTRUCTIONS:
Do not use this file on it`s own, it updates the installer to the latest version.
-->

<html>
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>
	<?php
	if($installKey == $_GET['upd'])
	{
		try
		{
			ob_start();
			include $_SERVER['DOCUMENT_ROOT'].'/PHPEasyDirectoryExplorer.php';
			$source = file_get_contents('http://www.armorwatcher.com/PHPEasyDirectoryExplorer/Sources/PHPEasyDirectoryExplorer.txt');
			$source = str_replace('#k#e#y#', $installKey, $source);
			file_put_contents('./PHPEasyDirectoryExplorer.php', $source);
			ob_clean();
			echo 'DONE';
			echo '<script>location = \'./PHPEasyDirectoryExplorer.php\'</script>';
		}
		catch(Exception $e)
		{
			echo 'ERROR, please contact the author.<br />'.$e;
		}
	}
	else
	{
		echo '<script>alert(\'That key was not valid!\nCould not update.\');</script>';
		echo '<script>location = \'./PHPEasyDirectoryExplorer.php\'</script>';
	}
	?>
</body>
</html>