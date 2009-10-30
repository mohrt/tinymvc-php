<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">
<html>
	<head>
		<title>Welcome to TinyMVC!</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
		<style type="text/css">
			body {
			  background:#9dbde1 url(http://www.tinymvc.com/images/bg-gradient.gif) top repeat-x;
				color:							#666666;
				font-family:				arial, sans;
				font-size:					100%;
				line-height:				1.7em;
				margin:							0 auto;
				text-align:         center;
				width:              500px;
			}

			h1 {
				font-size: 					2.18em;
				letter-spacing:			-0.01em;
			}			
			
			a:link {
				color:							#134c8c;
			}

			a:visited {
				color:							#666666;
			}

      .code {
        text-align:         left;
      	margin:             0 0 1.5em 0;
      	font-size:          1.0em;
      	border:             1px solid #134c8c;
      	background-color:   #cae3ff;
      	color:              #c44242;
      	padding:            .2em 1em .4em;
      }
			
			#bottom {
				border-top:					1px solid #134c8c;
				margin-top:					1em;
				padding-top:				1em;
				font-size:          0.8em;
			}
		</style>
	</head>
	<body>
		
	  <h1>Welcome to TinyMVC!</h1>
		
		<p>This is TinyMVC version <?=TMVC_VERSION?>.</p>
		<p>The view file for this page is here:</p>

		<div class="code">tinymvc/myapp/views/index_view.php</div>

		<p>The controller for this page is here:</p>

		<div class="code">tinymvc/myapp/controller/index.php</div>
		
		Let's get started, head to the <a href="http://www.tinymvc.com/wiki/index.php/Documentation">documentation</a>!
		
		<div id="bottom	">
			<a href="http://www.tinymvc.com/">TinyMVC</a> is licensed under the GNU <a rel="license" href="http://www.gnu.org/licenses/lgpl.html">LGPL</a> license.
		<br />
		<span style="font-size: 0.8em">This page was rendered in {TMVC_TIMER} seconds.</span>
		</div>
	</body>
</html>


