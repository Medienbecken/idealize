 <div class="menu">
     <div class="title">ADD NEW</div>
     <nav role="navigation">
         <?php wp_nav_menu(array(
                'container' => 'div',                           // enter '' to remove nav container (just make sure .footer-links in _base.scss isn't wrapping)
                'container_class' => 'nav',         // class of container (should you choose to use it)
                'menu' => __('Idea Menu', 'bonestheme'),   // nav name
                'menu_class' => '',                      // adding custom nav class
                'theme_location' => 'idea-nav',             // where it's located in the theme
                'before' => '',                                 // before the menu
                'after' => '',                                  // after the menu
                'link_before' => '',                            // before each link
                'link_after' => '',                             // after each link
                'depth' => 0,                                   // limit the depth of the nav
                'fallback_cb' => 'bones_footer_links_fallback'  // fallback function
            )); ?>
     </nav>
   
 </div>