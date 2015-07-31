<?php 
class trm {

	public static function carrega(){
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<title>lliure Terminal</title>
				<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
				<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'/>
				<link href="usr/terminal/core/terminal.css" rel="stylesheet"/>
				<meta name="charset" content="UTF8"/>
				<meta name="robots" content="index, nofollow">
				<meta name="author" content="Carlos Alberto Bertholdo Carucce"/>
			</head>
			<body>
				<div class="wrap color1">
					<div class="terminal-width">
						<div class="log"></div>
	
						<table class="command-box incomming">
							<tr>
								<td class="cmd-arrow color2" style="width: 20px;">></td>
								<td class="cmd-inner-box"><div><input class="color1" id="command" type="text" placeholder=""/></div></td>
							</tr>
						</table>
					</div>
				</div>
				
				<script src="usr/terminal/core/jquery-1.9.1.min.js"></script>
				<script src="usr/terminal/core/terminal.js"></script>
			</body>
		</html>		
		<?php
	}
	
	
}

trm::carrega();
?>