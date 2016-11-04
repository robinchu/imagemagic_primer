<?php session_start();
?>
<html>
	<head>
		<title>imagemagick</title>
	</head>

	<body>
		<form action="index.php" method="post">
<?php
$arr = ['resize', 'crop'];
echo 'handle:	<select name="handle">';
foreach ($arr as $val) {
	if (isset($_REQUEST['handle']) && $val == $_REQUEST['handle']) {
		echo '<option value="'. $val . '" selected="selected">' . $val . '</option>';
	} else {
		echo '<option value="'. $val . '" >' . $val . '</option>';
	}
}
echo '</select>';
echo '模式:	<select name="mode">';
$arr = ['equal' => '等比例', 'fold' => '伸展'];
foreach ($arr as $key => $val) {
	if (isset($_REQUEST['mode']) && $key == $_REQUEST['mode']) {
		echo '<option value="'. $key . '" selected="selected">' . $val . '</option>';
	} else {
		echo '<option value="'. $key . '" >' . $val . '</option>';
	}
}

echo '</select>';
			?>

			width:<input type="text" name="width" value="<?=isset($_REQUEST['width']) ? $_REQUEST['width']: 100; ?>">
			heigt:<input type="text" name="height" value="<?=isset($_REQUEST['height']) ? $_REQUEST['height']: 100; ?>">
			img url:<input type="text" name="img" value="<?=isset($_REQUEST['img']) ? $_REQUEST['img']: ''; ?>">
			<input type="submit" value="submit">
		</form>

		<?php
			$img = 	isset($_REQUEST['img']) ? $_REQUEST['img'] : '';
			$handle = isset($_REQUEST['handle']) ? $_REQUEST['handle'] : 'resize';
			$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 100;
			$width = isset($_REQUEST['width']) ? $_REQUEST['width'] : 100;
			$height = isset($_REQUEST['height']) ? $_REQUEST['height'] : 100;

			//$_SESSION['img'] = $img;
			//$_SESSION['handle'] = $handle;
			//$_SESSION['height'] = $height;
			//$_SESSION['width'] = $width;
			if ($img) {
				$filestream = file_get_contents($img);
				if (!file_exists('../tmp_img') && is_dir('../tmp_img')) {
					exec('mkdir ../tmp_img');
				}
				$filename = 'tmp_img/'. substr(md5(time()), -6) . ".jpg";
				file_put_contents($filename, $filestream);
				$image_size = getimagesize($filename);
				var_dump($image_size);
				echo '<br>';
				if ($handle == 'resize') {
					if ($mode == 'equal') {
						$sCmd = 'convert -' . "$handle $width" . 'X' . "$height  $filename $filename";
						exec($sCmd);
						$image_size = getimagesize($filename);
						var_dump($image_size);
						echo '<br>';
						$width_diff = $width - $image_size[0];
						$height_diff = $height - $image_size[1];
						exec('rm -r remain.jpg');
						if ($width_diff > 2) {
							$sCmd = 'convert -size '. $width_diff/2 . 'X' . $height . ' xc:green remain.jpg';
							exec($sCmd);
							$sCmd = 'convert +append remain.jpg ' . $filename . ' ' . $filename;
							exec($sCmd);
							$sCmd = 'convert +append ' . $filename . ' remain.jpg ' . $filename;
							exec($sCmd);

						} else if ($height_diff > 2){
							$sCmd = 'convert -size '. $width . 'X' . $height_diff/2 . ' xc:green remain.jpg';
							exec($sCmd);
							$sCmd = 'convert -append remain.jpg ' . $filename . ' ' . $filename;
							exec($sCmd);
							$sCmd = 'convert -append ' . $filename . ' remain.jpg ' . $filename;
							exec($sCmd);
						}

					} else if ($mode == 'fold') {
						$sCmd = 'convert -' . "$handle $width" . 'X' . "$height! $filename $filename";
						exec($sCmd);
						$image_size = getimagesize($filename);
						var_dump($image_size);
						echo '<br>';
					}

				}
				echo '<img src="' . $filename . '">';
			}
		?>
	</body>
</html>
