<?php

/* Start the session for each page this is included on */

session_start();

/* Deliver Page Layout in Alterable & Divided Segments */

function deliverTop($pagetitle) {

    $top = '<!DOCTYPE HTML>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>'.$pagetitle.'</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

';

    return $top;
}

function deliverMiddle($toptext, $bottomtext, $buttons) {

    $middle = '<!-- Wrapper -->
			<div id="wrapper">

				<!-- Main -->
					<section id="main">
						<header>
							<span class="avatar"><a href="index.php"><img src="images/main.png" width="122px" height="122px" alt="" /></a></span>
							<h1>'.$toptext.'</h1>
							<p>'.$bottomtext.'</p>
						</header>

						<footer>
							<ul class="icons">
'.$buttons.'
							</ul>
						</footer>
					</section>

				';
				
	return $middle;
}

function deliverBottom() {
    
    $bottom = '<!-- Footer -->
					<footer id="footer">
					</footer>

			</div>

		<!-- Scripts -->
			<script>
				if (\'addEventListener\' in window) {
					window.addEventListener(\'load\', function() { document.body.className = document.body.className.replace(/\bis-preload\b/, \'\'); });
					document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? \' is-ie\' : \'\');
				}
			</script>

	</body>
</html>';

    return $bottom;
}

?>
