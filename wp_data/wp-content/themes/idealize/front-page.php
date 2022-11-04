<?php
/*
 * CUSTOM POST TYPE ARCHIVE TEMPLATE
 *
 * This is the custom post type archive template. If you edit the custom post type name,
 * you've got to change the name of this template to reflect that name change.
 *
 * For Example, if your custom post type is called "register_post_type( 'bookmarks')",
 * then your template name should be archive-bookmarks.php
 *
 * For more info: http://codex.wordpress.org/Post_Type_Templates
*/
?>

<?php get_header(); ?>

<div id="content">

	<div id="inner-content" class="wrap cf">

		<main id="main" class="m-all t-all d-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

			<?php
			$args = array(
				'post_type' => 'idea',
				'posts_per_page' => 3
			);
			$the_query = new WP_Query($args); ?>
			<h2 class="archive-title h1">Featured Ideas</h2>

			<?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>

					<div class="d-1of3 t-1of2 cf" style="padding:0.5rem">
						<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

							<header class="article-header">
								<?php if (get_field('headerImage')) { ?>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php the_field('headerImage'); ?>" alt="<?php the_field('imgAlt'); ?>" style="width:100%; aspect-ratio: 1 / 1; object-fit: cover;"></a>
								<?php } else { ?>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="https://picsum.photos/seed/<?php the_ID(); ?>/300/300" alt="" style="width:100%; aspect-ratio: 1 / 1; object-fit: cover;"></a>
								<?php }  ?>
								<h3 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
								<hr>
								<!-- <p class="byline vcard"><?php
																printf(__('Posted <time class="updated" datetime="%1$s" itemprop="datePublished">%2$s</time> by <span class="author">%3$s</span>', 'bonestheme'), get_the_time('Y-m-j'), get_the_time(__('F jS, Y', 'bonestheme')), get_author_posts_url(get_the_author_meta('ID')));
																?></p> -->

							</header>

							<section class="entry-content cf">
								<?php the_field('shortDescription'); ?>
							</section>

							<footer class="article-footer">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><button class="blue-btn">View details</button></a>
							</footer>

						</article>
					</div>

				<?php endwhile; ?>

				<?php bones_page_navi(); ?>

			<?php else : ?>

				<article id="post-not-found" class="hentry cf">
					<header class="article-header">
						<h1><?php _e('Oops, Post Not Found!', 'bonestheme'); ?></h1>
					</header>
					<section class="entry-content">
						<p><?php _e('Uh Oh. Something is missing. Try double checking things.', 'bonestheme'); ?></p>
					</section>
					<footer class="article-footer">
						<p><?php _e('This is the error message in the custom posty type archive template.', 'bonestheme'); ?></p>
					</footer>
				</article>

			<?php endif; ?>

		</main>


		<?php get_sidebar(); ?>
		<?php include('toplist.php'); ?>

	</div>

</div>
<?php get_sidebar('homepagebox'); ?>
<?php get_footer(); ?>