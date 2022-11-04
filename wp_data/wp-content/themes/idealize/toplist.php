<aside class="m-all d-all cf">
	<header>
		<h2 class="h1">Top Of The Week</h2>
	</header>
	<section class="totw">
		<ol>
			<?php
			for ($i = 1; $i <= 10; $i++) {
			?>
				<li class="totw-item d-1of10 t-1of10 m-1of10  cf">
					<header>
						<img src="https://picsum.photos/200?random=<?php echo ($i); ?>" alt="Bild Nummer: <?php echo ($i); ?>" style="width:100%">
						<h2>&Uuml;berschrift <?php echo ($i); ?></h2>
					</header>
					<article>
					<i class="fa fa-facebook"></i>
					<p style="font-family:'FontAwesome';">&#xf19c;</p>
						<p>
							Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni similique saepe, libero nam molestias optio quia illum blanditiis eos fugit beatae atque est doloremque, modi aperiam ipsam, error sed culpa.
						</p>
					</article>
					<footer>
						<p>
							<i>Top of the week <?php echo ($i); ?></i>
						</p>
					</footer>

				</li>
			<?php
			}
			?>
		</ol>
	</section>

</aside>