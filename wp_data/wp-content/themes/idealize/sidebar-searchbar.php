				 <div id="searchbar" class="m-all t-all d-all cf">

					<?php if ( is_active_sidebar( 'searchbar' ) ) : ?>
						
						<div>
						<?php dynamic_sidebar( 'searchbar' ); ?>
						</div>

					<?php else : ?>

						<?php
							/*
							 * This content shows up if there are no widgets defined in the backend.
							*/
						?>

						<div class="no-widgets">
							<p><?php _e( 'This is a widget ready area. Add some and they will appear here.', 'bonestheme' );  ?></p>
						</div>

					<?php endif; ?>

				</div> 
