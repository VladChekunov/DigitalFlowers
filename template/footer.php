</div>
<footer>
	<div class="footer_container">
		<div class="col-3">
			<img class="footer_logo" src="/img/logo-white-xs.png">
			<p>DigitalFlowers - компания, занимающаяся выращиванием и продажей цифровых цветов.</p>
		</div>
		<div class="col-3 footer_menu">
			<?php
				global $api;
				$api->getMenu();
			?>
			<!--
			<a href="/">HOME</a>
			<a href="/about">ABOUT US</a>
			<a href="/contact">CONTACT US</a>
			-->
		</div>
		<div class="col-3 footer_phone">
			(123) 456 78 90
		</div>
		<div class="col-3">
			<div class="footer_btns">
				<a href="#"><i class="fab fa-twitter"></i></a>
				<a href="#"><i class="fab fa-vk"></i></a>
				<a href="#"><i class="fab fa-instagram"></i></a>
				<a href="#"><i class="fab fa-skype"></i></a>
			</div>
		</div>
	</div>
</footer>
</body>
</html>
