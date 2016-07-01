<?php
$protocol = $_SERVER["SERVER_PROTOCOL"];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
	$protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Retry-After: 600' );
?>
<html>
<head>
	<title>Le CRIDON LYON | Site en cours de maintenance</title>
	<meta name="viewport" content="width=1024">
</head>
<body style="background: #e6e6e6; margin:0; padding:180px 0; position:relative;" >
	
	<div style="position:absolute; top:0px; width:100%;">
		<div style="width:100%; height:60px; background-color:#018383;"></div>
		<div style="width:100%; height:120px; background-color:#FFF;"></div>
	</div>


	<div style="width:500px; height:auto; margin:100px auto 0 auto; overflow: hidden;">
		
	
		<center><img src="/wp-content/themes/maestro/library/images/logo-cridon-new.svg" alt="" width="500" /></center>
		<p style="@import url('https://fonts.googleapis.com/css?family=Dosis'); font-size: 60px; line-height: 60px; margin:70px 0 0 0; color:#2e4867; font-weight:300; padding:0px; font-family: 'Dosis', Arial, Helvetica, sans-serif; text-align:center;">Site en cours de maintenance</p>

	</div>

	<div style="position:fixed; bottom:0px; width:100%;">
		<div style="width:100%; height:120px; background-color:#15283f;"></div>
		<div style="width:100%; height:60px; background-color:#0b1828;"></div>
	</div>

	
</body>
</html>
