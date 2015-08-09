<?php
mb_language('japanese');
mb_internal_encoding('UTF-8');

define('IMAGE_PATH', '');

$error_flag = false;

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	$current_directory = htmlspecialchars(filter_input(INPUT_GET, 'dir'), ENT_QUOTES);
	$dir_name;

	$dir_name = explode('..', $current_directory);

	$dir_length = count($dir_name);
	$prev_dir = $dir_name;
	
	array_pop($prev_dir);
	array_push($prev_dir, '');

	if(count($prev_dir) > 1) {
		$prev_dir_name = (count($prev_dir) > 1) ? implode('..', $prev_dir) : $prev_dir;
		$prev_dir_name = substr($prev_dir_name, 0, mb_strlen($prev_dir_name)-2);
	} else {
		$prev_dir_name = $_SERVER['SCRIPT_NAME'];
	}

	$dir_name = implode('/', $dir_name);

	$check_dir_name = substr($dir_name, 0,  mb_strlen($dir_name));

	if(is_dir(IMAGE_PATH . $check_dir_name))
		$dirs = scandir(IMAGE_PATH . $check_dir_name);
	else {
		$dirs = false;
		$error_flag = true;
	}

} else {
	$dirs = scandir(IMAGE_PATH);
}

if($error_flag) {
	echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">RootDirectory</a><br>';
	exit('ディレクトリが存在しません。');
}

$images_dir = $dirs_dir = array();

foreach($dirs as $dir_k => $dir_v) :
	if($dir_v != '.' && $dir_v != '..') {
		if(preg_match('/[A-Za-z0-9*?].(png|jpeg|jpg|gif)/', $dir_v)) {
			array_push($images_dir, $dir_v);
		} else if(is_dir(IMAGE_PATH . $check_dir_name . '/' . $dir_v)){
			array_push($dirs_dir, $dir_v);
		}
	}
endforeach;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>yes view <?= IMAGE_PATH ?> easily</title>
</head>
<body>
	<h1 style="text-align:center;font-weight:lighter;margin:2em;">Current directory is 「<?= IMAGE_PATH . $check_dir_name ?>」</h1>
	<?php if(count($prev_dir) > 1) { ?>
		<p style="text-align:center;"><a style="display:inline;" href="?dir=<?= $prev_dir_name ?>">Parent directory</a></p>
	<?php } else { ?>
		<p style="text-align:center;"><a style="display:inline;" href="<?= $prev_dir_name ?>">Parent directory</a></p>
	<?php } ?>
	<h2 style="text-align:center;">SubDirectory</h2>
	<ul>
		<?php foreach($dirs_dir as $dir_v) { ?>
			<li>
				<?php if(isset($current_directory) && mb_strlen($current_directory) > 0) { ?>
					<a href="<?= $_SERVER['SCRIPT_NAME'] . '?dir=' . $current_directory . '..' . $dir_v ?>"><?= $dir_v ?></a>
				<?php } else { ?>
					<a href="<?= $_SERVER['SCRIPT_NAME'] . '?dir=' . $dir_v ?>"><?= $dir_v ?></a>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<h2 style="text-align:center;">Images at current directory</h2>
	<?php
	if(count($images_dir) > 0) {
	foreach($images_dir as $dir_v) : ?>
		<div class="image-block">
			<a href="<?= IMAGE_PATH . $check_dir_name . '/' .   $dir_v ?>" class="image-link" target="_blank">
				<img src="<?= IMAGE_PATH . $check_dir_name . '/' .   $dir_v ?>" alt="<?= $dir_v ?>">
			</a>
			<div class="hover-overlay">
				<span class="image-caption"><?= IMAGE_PATH . $check_dir_name . '/' . $dir_v ?></span>
			</div>
		</div>
	<?php endforeach;
	} else {
		echo '<p style="text-align:center;">Image is empty.</p>';
	}
	?>
	<style>
		a {
			display: block;
		}
		a.image-link {
			background-color: #fff;
			border:1px solid #5f5f5f;
			padding: 2em;
			margin: 1em;
			text-align: center;
			clear: both;
			max-width: 100%;
		}
		a.image-link:hover {
			background-color:#e0e0e0;
		}
		ul,
		.image-block {
			width:80%;
		}
		.image-block {
			display: block;
			float: left;
			padding-top: 2em;
			position: relative;
			width: 50%;
		}
		img {
			display: block;
			 max-height: 250px;
			margin:0 auto;
			width:auto;
		}
			img:hover {
				opacity:.8;
			}
		.hover-overlay {
			color:#000;
			font-size:1.1em;
			padding:0 0 1em;
			text-align: center;
			transition:font-size .2s;
		}
			.image-block:hover .hover-overlay {
				color:#2f2f2f;
			}
		.image-caption {
			height:2em;
			margin-top:-1em;
			top:50%;
			text-align: center
		}
		ul {
			list-style-type: none;
			margin:0 auto 4em;
			text-align: center;
			padding:0;
		}
		ul > li {
			display: inline-block;
			line-height:2;
			overflow: hidden;
		}
		ul > li > a {
			background-color:#000;
			border-radius:5px;
			color:#fff;
			display:block;
			font-size:1.5em;
			padding:.5em;
		}
		ul > li > a:hover {
			background-color:#353535;
		}
	</style>
</body>
</html>