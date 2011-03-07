<?php

require dirname(__FILE__) . '/libs/texy.min.php';
require dirname(__FILE__) . '/libs/fshl/fshl.php';
require dirname(__FILE__) . '/libs/MyTexy.php';

header('Content-Type: text/html; charset=utf-8');

ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 10);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 10);

session_start();

$texy = new MyTexy;

if (isset($_POST['text'])) { // vystup pro AJAX
	$text = $_POST['text'];
	if (get_magic_quotes_gpc()) {
		$text = stripslashes($text);
	}
	if (strlen($text) > 20000) {
		die('Too long text');
	}
	$text = Texy::normalize($text);
	$_SESSION['text'] = $text;

	echo $texy->process($text);
	exit;

} else {
	$text = @$_SESSION['text'];
}


?><html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="copyright" content="&copy; 2008 David Grudl; http://davidgrudl.com">

	<title>Texy AJAX Editor</title>

	<link rel="stylesheet" type="text/css" media="all" href="css/screen.css">
	<link rel="stylesheet" type="text/css" media="print" href="css/print.css">

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/htmltooltip.js"></script>
	<script type="text/javascript">
	<!--

	$(function(){
		var timeoutId;

		$.ajaxSetup({
			timeout: 4000
		});

		$('textarea').keydown(function(e) {
			clearTimeout(timeoutId);
			timeoutId = setTimeout(function() {
				var mask = new RegExp("[^a-zA-Z0-9_\\u00A1-\\uFFFF]", "g");

				$.post(window.location.href, { text: $('textarea').val() }, function(data) {
					$('#output').html(data);
					$('#counter').text($('#output').text().replace(mask, '').length + ' chars');
				});
			}, 400);

			if (e.which == 9 /* TAB */ && !e.ctrlKey && !e.altKey) {
				if (e.target.setSelectionRange) { // non-IE
					var start = e.target.selectionStart, end = e.target.selectionEnd;
					var top = e.target.scrollTop;
					if (start !== end) {
						start = e.target.value.lastIndexOf("\n", start) + 1;
					}
					var sel = e.target.value.substring(start, end);
					if (e.shiftKey) {
						sel = sel.replace(/^\t/gm, '');
					} else {
						sel = sel.replace(/^/gm, "\t");
					}
					e.target.value = e.target.value.substring(0, start) + sel + e.target.value.substr(end);
					e.target.setSelectionRange(start === end ? start + 1 : start, start + sel.length);
					e.target.focus();
					e.target.scrollTop = top; // Firefox

				} else if (e.target.createTextRange) { // ie
					document.selection.createRange().text = "\t";
				}
				if ($.browser.opera) {
					$(this).one('keypress', function(e) { return false; });
				}
				return false;

			} else if (e.which == 27 /* ESC */ && !e.shiftKey && !e.ctrlKey && !e.altKey) {
				var inputs = $(':input');
            	inputs.eq(inputs.index(e.target) - inputs.length + 1).focus();
			}
		}).focus();
	});

	-->
	</script>
	</head>

	<body>
		<h1>Texy AJAX Editor <small id="counter"></small></h1>

		<textarea name="text"><?= htmlSpecialChars($text) ?></textarea>

		<div id="output"><?= $texy->process($text) ?></div>

		<p id="help" rel="htmltooltip">?</p>

		<div class="htmltooltip">
		<ul>
			<li>jednoduchý editor s podporou Texy syntaxe
			<li>živý náhled
			<li>automatické ukládání (kdykoliv lze vypnout prohlížeč)
			<li>počítadlo znaků
		</ul>
		</div>
	</body>
</html>