				 <div id="homepagebox" class="m-all t-all d-all cf">

					<?php if ( is_active_sidebar( 'homepageBox' ) ) : ?>
						
						<aside class="homebox">
							<?php dynamic_sidebar( 'homepageBox' ); ?>
						</aside>

					<?php else : ?>

						<?php
							/*
							 * This content shows up if there are no widgets defined in the backend.
							*/
						?>

				<div class="homepagebox" style="padding:2%; background-color:#191970; color:#fff;">
					
					<aside style="display: inline-block; width:45%;">
						<img src="https://cdn.hs-heilbronn.de/08d09d4c9176fc90/cd2a9cc0e2ea/v/6f923a32d84a/08d09d4c9176fc90-f79bff7db7d7-08d09d4c9176fc90-36c8b51ab200-Logo_Weltoffene_Hochschulen_gegen_Fremdenfeindlichkeit.png" alt="">
					</aside>
					<aside style="display: inline-block; width:35%;">
					<h2>IdeaLize</h2>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic quaerat rem, a ratione nesciunt pariatur, unde atque non debitis quo eos molestiae quisquam, nisi delectus ad optio fugit perferendis quas amet vel ea. Placeat repudiandae consectetur quaerat dignissimos eaque alias sunt quod rerum ratione optio eius, beatae reprehenderit quidem error, cumque omnis debitis quae autem laboriosam provident incidunt libero modi. Sint, aliquid. Numquam corrupti voluptatem ipsa voluptate, expedita, hic libero temporibus quidem nihil adipisci totam et unde perferendis error, commodi consequatur atque minima explicabo rerum ipsum? Fugiat architecto autem officiis iure porro perferendis quod, nihil nam? Iusto porro vitae unde non, est nihil accusamus cum veniam pariatur inventore vero obcaecati ea temporibus repellendus minus iste ut nobis laborum beatae doloremque. Obcaecati, exercitationem! Quidem, culpa quam perspiciatis dolorum aliquid atque odit, debitis illum reprehenderit placeat exercitationem ducimus animi eveniet similique sit numquam sint, ad rem quo aut quas neque ab eos!</p>
					</aside>
				</div>

					<?php endif; ?>

				</div> 
