<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title><?php wp_title(''); ?></title>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
            <meta name="theme-color" content="#121212">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

	</head>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

		<div id="container">

			<header class="header" role="banner" itemscope itemtype="http://schema.org/WPHeader">

				<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
				<a href="http://localhost:8000"><img src="https://www.hs-heilbronn.de/assets/logo_color_de.1d2df2ddaca26921d4f3.png" alt="HS Heilbronn Logo" style="width:200px; padding:1%; display:inline-block;"></a>
				<ul style="text-transform:uppercase; display:inline-block; position:absolute; right:5%;">
					<li style="display:inline; padding-right:2%;"><a href="http://localhost:8000/ideas/">Ideas</a></li>
					<li style="display:inline; padding-right:2%;"><a href="#">Explore</a></li>
					<li style="display:inline; padding-right:2%;"><a href="#">About</a></li>
					<li style="display:inline; padding-right:2%;"><a href="#">Support</a></li>
					<li style="display:inline; padding-right:2%;">|</li>
					<li style="display:inline; padding-right:2%;">Search</li>
					<li style="display:inline; padding-right:2%;">Login</li>


				</ul>
						<?php wp_nav_menu(array(
    					         'container' => false,                           // remove nav container
    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
    					         'menu' => __( 'The Main Menu', 'bonestheme' ),  // nav name
    					         'menu_class' => 'nav top-nav cf',               // adding custom nav class
    					         'theme_location' => 'main-nav',                 // where it's located in the theme
    					         'before' => '',                                 // before the menu
        			               'after' => '',                                  // after the menu
        			               'link_before' => '',                            // before each link
        			               'link_after' => '',                             // after each link
        			               'depth' => 0,                                   // limit the depth of the nav
    					         'fallback_cb' => ''                             // fallback function (if there is one)
						)); ?>

					</nav>


				<?php if( is_front_page() ) { ?>
					<div class="homepagebox" style="padding:2%; background-color:#191970; color:#fff;">
						<aside style="display: inline-block; width:45%;">
							<img src="https://cdn.hs-heilbronn.de/08d09d4c9176fc90/cd2a9cc0e2ea/v/6f923a32d84a/08d09d4c9176fc90-f79bff7db7d7-08d09d4c9176fc90-36c8b51ab200-Logo_Weltoffene_Hochschulen_gegen_Fremdenfeindlichkeit.png" alt="">
						</aside>
						<aside style="display: inline-block; width:45%;">
						<h2>IdeaLize</h2>
						<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic quaerat rem, a ratione nesciunt pariatur, unde atque non debitis quo eos molestiae quisquam, nisi delectus ad optio fugit perferendis quas amet vel ea. Placeat repudiandae consectetur quaerat dignissimos eaque alias sunt quod rerum ratione optio eius, beatae reprehenderit quidem error, cumque omnis debitis quae autem laboriosam provident incidunt libero modi. Sint, aliquid. Numquam corrupti voluptatem ipsa voluptate, expedita, hic libero temporibus quidem nihil adipisci totam et unde perferendis error, commodi consequatur atque minima explicabo rerum ipsum? Fugiat architecto autem officiis iure porro perferendis quod, nihil nam? Iusto porro vitae unde non, est nihil accusamus cum veniam pariatur inventore vero obcaecati ea temporibus repellendus minus iste ut nobis laborum beatae doloremque. Obcaecati, exercitationem! Quidem, culpa quam perspiciatis dolorum aliquid atque odit, debitis illum reprehenderit placeat exercitationem ducimus animi eveniet similique sit numquam sint, ad rem quo aut quas neque ab eos!</p>
						</aside>
					</div>
				<?php } ?>
				<div id="inner-header" class="wrap cf">
					<?php // to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> ?>
					<!-- <p id="logo" class="h1" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></p> -->

					<?php // if you'd like to use the site description you can un-comment it below ?>
					<?php // bloginfo('description'); ?>


					

				</div>

			</header>
