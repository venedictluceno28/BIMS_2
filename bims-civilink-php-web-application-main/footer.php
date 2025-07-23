<html>
<style>
	body {
		background-color: #003C00;
		padding-top: 4px;
		font-family: calibri;
		color: white;
		margin: 0;
	}
	hr {
		height: 3px;
	}
	.footer-container {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 10px 30px;
	}
	.footer-left {
		text-align: left;
	}
	.footer-center {
		text-align: center;
		flex: 1;
	}
	a {
		color: #fff;
		text-decoration: underline;
	}
</style>
<body>
	<div class="footer-container">
		<div class="footer-left">
			Contact Number: <a href="tel:+639154154584">+63 915 415 4584</a><br>
			Facebook: <a href="https://www.facebook.com/pasongcamachile2/" target="_blank">facebook.com/pasongcamachile2</a>
		</div>
		<div class="footer-center">
			<div id="clockbox"></div>
		</div>
	</div>
	<script type="text/javascript">
		tday = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		tmonth = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		
		function GetClock() {
			var d = new Date();
			var nday = d.getDay(), nmonth = d.getMonth(), ndate = d.getDate(), nyear = d.getFullYear();
			var nhour = d.getHours(), nmin = d.getMinutes(), nsec = d.getSeconds(), ap;

			if (nhour == 0) { ap = " AM"; nhour = 12; }
			else if (nhour < 12) { ap = " AM"; }
			else if (nhour == 12) { ap = " PM"; }
			else if (nhour > 12) { ap = " PM"; nhour -= 12; }

			if (nmin <= 9) nmin = "0" + nmin;
			if (nsec <= 9) nsec = "0" + nsec;

			document.getElementById('clockbox').innerHTML = tmonth[nmonth] + " " + ndate + ", " + nyear + " " + tday[nday] + ", " + nhour + ":" + nmin + ":" + nsec + ap;
		}
		window.onload = function () {
			GetClock();
			setInterval(GetClock, 1000);
		}
	</script>
</body>
</html>