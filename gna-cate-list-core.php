<?php
if (!class_exists('GNA_CateList')) {
	class GNA_CateList {
		var $cate_list_default_settings = null;
		var $plugin_url;

		public function init() {
			$class = __CLASS__;
			new $class;
		}

		public function __construct() {
			$this->define_constants();
			$this->define_variables();
			$this->setup_shortcodes();

			add_action('init', array(&$this, 'plugin_init'), 0);
			add_action('wp_print_styles', array(&$this, 'add_front_styles'));
			add_filter('plugin_row_meta', array(&$this, 'filter_plugin_meta'), 10, 2);
		}

		public function define_constants() {
			define('GNA_CATE_LIST_VERSION', '0.9.8');

			define('GNA_CATE_LIST_BASENAME', plugin_basename(__FILE__));
			define('GNA_CATE_LIST_URL', $this->plugin_url());
		}

		public function plugin_url() { 
			if ($this->plugin_url) return $this->plugin_url;
			return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
		}

		public function filter_plugin_meta($links, $file) {
			if( strpos( GNA_CATE_LIST_BASENAME, str_replace('.php', '', $file) ) !== false ) { /* After other links */
				$links[] = '<a target="_blank" href="https://profiles.wordpress.org/chris_dev/" rel="external">' . __('Developer\'s Profile', 'gna-cate-list') . '</a>';
			}

			return $links;
		}

		public function define_variables() {
			$this->cate_list_default_settings = array(
				'show_option_all'    => '',
				'orderby'            => 'name',
				'order'              => 'ASC',
				'style'              => 'list',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'use_desc_for_title' => 1,
				'child_of'           => 0,
				'feed'               => '',
				'feed_type'          => '',
				'feed_image'         => '',
				'exclude'            => '',
				'exclude_tree'       => '',
				'include'            => '',
				'hierarchical'       => 1,
				'title_li'           => __( 'Categories' ),
				'show_option_none'   => __( '' ),
				'number'             => null,
				'echo'               => 0,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => 'category',
				'walker'             => null,
				
				'class'				 => ''
			);
		}

		public function install() {
		}

		public function uninstall() {
		}

		public function activate_handler() {
		}

		public function deactivate_handler() {
		}

		public function setup_shortcodes() {
			add_filter('widget_text', 'do_shortcode');

			add_shortcode( 'gna_catelist', array($this, 'catelist_shortcode') );
			add_shortcode( 'catelist', array($this, 'catelist_shortcode') );
		}

		public function plugin_init() {
			load_plugin_textdomain('gna-cate-list', false, dirname(plugin_basename(__FILE__ )) . '/languages/');
		}

		public function add_front_styles() {
			wp_enqueue_style('gna-cate-list-front-css', GNA_CATE_LIST_URL. '/assets/css/gna-cate-list-front-styles.css');
		}

		public function catelist_shortcode($atts) {
			global $post;

			$return = '';
			extract(shortcode_atts($this->cate_list_default_settings, $atts));

			$cate_list_args = array(
				'show_option_all'    => $show_option_all,
				'orderby'            => $orderby,
				'order'              => $order,
				'style'              => $style,
				'show_count'         => $show_count,
				'hide_empty'         => $hide_empty,
				'use_desc_for_title' => $use_desc_for_title,
				'child_of'           => $this->change_str2cateid($child_of),
				'feed'               => $feed,
				'feed_type'          => $feed_type,
				'feed_image'         => $feed_image,
				'exclude'            => $this->change_str2cateid($exclude),
				'exclude_tree'       => $exclude_tree,
				'include'            => $include,
				'hierarchical'       => $hierarchical,
				'title_li'           => $title_li,
				'show_option_none'   => $show_option_none,
				'number'             => $number,
				'echo'               => 0,
				'depth'              => $depth,
				'current_category'   => $current_category,
				'pad_counts'         => $pad_counts,
				'taxonomy'           => $taxonomy,
				'walker'             => null
			);

			$list_cates = '';
			if ( $style == 'list' ) {
				$list_cates = wp_list_categories( $cate_list_args );
			} else if ( $style == 'dropdown' ) {
				$list_cates = wp_dropdown_categories( $cate_list_args );
			}
			
			if(strpos($list_cates, 'current-cat') == false) { // check if the class exists
				// add the class to the All item if it doesn't exist
				$list_cates = str_replace('cat-item-all', 'cat-item-all current-cat', $list_cates);
			}
			if ($list_cates) {
				if ( $style == 'list' ) {
					$return .= '<ul class="cate_list '.$class.'">'."\n".$list_cates."\n".'</ul>';
				} else {
					$return .= '<div class="cate_list dropdown '.$class.'">'."\n".$list_cates."\n".'</div>';
				}
			} else {
				$return .= '<!-- no categories to show -->';
			}

			return $return;
		}

		public function change_str2cateid( $str ) {
			global $post;

			$new_str_id = $str;
			$new_str_id = str_replace('this', $post->ID, $new_str_id);				// exclude this category
			$new_str_id = str_replace('current', $post->ID, $new_str_id);			// exclude current category
			$new_str_id = str_replace('parent', $post->post_parent, $new_str_id);	// exclude parent category

			return $new_str_id;
		}
	}
}
$GLOBALS['g_catelist'] = new GNA_CateList();
