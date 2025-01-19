<?php

if (empty($_GET["url"])) { ?>
	thou shalt specify a <a href="?url=https://www.youtube.com/watch?v=dQw4w9WgXcQ">?url=</a>
	<?php exit();}

if (!empty($_GET["mime_type"])) {
	header("Content-Type: " . $_GET["mime_type"]);
}

$url = $_GET["url"];
$debug = isset($_GET["debug"]);

if ($debug);
?><pre><?php
$php_output = fopen("php://output", "w");
$php_stderr = fopen("php://stderr", "w");
$php_stdout = fopen("php://stdout", "w");
$command =
	"yt-dlp " .
	($debug ? "-v " : "") .
	"--ffmpeg-location /usr/local/bin/ffmpeg7 -S 'height:720,ext,+codec:avc:m4a' -f 'bv*+ba/b' -o - $url";
$env = ["TMPDIR" => "./tmp"];
// setting ffmpeg location and tmpdir because of synology dsm

$descriptorspec = [
	0 => ["pipe", "r"],
	1 => ["pipe", "w"],
	2 => ["pipe", "w"],
];
$process = proc_open($command, $descriptorspec, $pipes, null, $env);

$proc_stdout = $pipes[1];
$proc_stderr = $pipes[2];
$err = "";

if (!$debug) {
	while ($buf = fread($proc_stdout, 1024)) {
		fwrite($php_output, $buf);
	}
}

while (!feof($proc_stderr)) {
	$line = fgets($proc_stderr);
	fwrite($debug ? $php_output : $php_stderr, $line);
	$err .= $line;
}

$return_value = proc_close($process);

if ($return_value != 0) {
	header("Content-Type: text/html"); ?>
	<h2>error :(</h2>
	<pre><strong><?= $command ?></strong></pre>
	<pre><?= $err ?></pre>
<?php
}

