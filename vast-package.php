<?php
/*
 * Plugin Name: 		Vast Package
 * Plugin URI:			https://vastthemes.com
 * Description:			Add-On for Vast Theme
 * Version:				1.1.2.2
 * Author:				deTheme
 * Author URI:			https://detheme.com
 * Requires at least:	4.0.0
 * Tested up to:		5.2.2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'VAST_PACKAGE_PATH' ) ) {
    define( 'VAST_PACKAGE_PATH', plugin_dir_url( __FILE__ ) );
}

if ( ! function_exists( 'vast_package_activation_redirect' ) ) :

    /**
     * Redirect to "Vast Dashboard" page on activation
     */
    function vast_package_activation_redirect( $plugin ) {
        if( $plugin == plugin_basename( __FILE__ ) ) {
            wp_redirect( admin_url( 'admin.php?page=vast-package-options' ) );
            exit;
        }
    }
endif;

add_action( 'activated_plugin', 'vast_package_activation_redirect' );

if ( ! function_exists( 'vast_plugin_enqueue_scripts' ) ) :

	/**
	 * Enqueue plugin styles.
	 */
	function vast_plugin_enqueue_scripts() {
		wp_register_style( 'vast-package-style', VAST_PACKAGE_PATH . 'assets/css/vast_package.css' );
		wp_enqueue_style( 'vast-package-style' );
	}

	add_action( 'wp_enqueue_scripts', 'vast_plugin_enqueue_scripts' );
endif;

if ( ! function_exists( 'vast_admin_script' ) ) :

    /**
     * Load admin script
     */
    function vast_admin_script() {
		// Load helpscout script if vast_theme.
		$vast_theme = get_template();
		if ( $vast_theme=="vast" ){
        	wp_register_script( 'vast_helpscout' , 'http://demoimporter.vastthemes.com/beaconJS/repo/helpscout.js', array('jquery'),'1.1', true);
			wp_enqueue_script('vast_helpscout');
		}
		wp_enqueue_style( 'vast_package', VAST_PACKAGE_PATH . '/assets/css/vast_package.css' );

    }
    add_action( 'admin_enqueue_scripts', 'vast_admin_script' );




endif;

if ( ! function_exists( 'vast_package_custom_menu_page' ) ) :

    /**
     * Create Admin Menu
     */
    function vast_package_custom_menu_page() {

    add_menu_page( 'Vast Package', 'Vast Package', 'manage_options', 'vast-package-options', 'vast_package_plugin_options', 'dashicons-welcome-widgets-menus', 90 );
    }
    add_action( 'admin_menu', 'vast_package_custom_menu_page' );

endif;

if ( ! function_exists( 'vast_package_plugin_options' ) ) :

    /**
     * Create Vast Package Layout
     */
    function vast_package_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $theme_info = wp_get_theme();
        ?>
        <div class="wrap vp-page vp-about">
			<div class="vp-about-wrapper">
				<div class="vp-about-header">
					<div class="vp-about-header__title">
						<h3>Getting Started</h3>
					</div>
				</div>
				<div class="vp-about-container">
					<div class="vp-about-content">
						<img src="<?php echo VAST_PACKAGE_PATH . '/assets/images/vast_logo.png';?>" alt="">
						<p>Thank you for installing Vast Theme! Follow our Facebook page for more updates on Vast Theme <a target="_blank" href="http://fb.com/vast.themes">http://fb.com/vast.themes</a>  and join our Facebook group to get involved with a huge community of Vast Users <a target="_blank" href="https://www.facebook.com/groups/235578843768042/">https://www.facebook.com/groups/235578843768042</a></p>
					</div>
					<div class="vp-about-video-wrapper">
					<div class="vast_videoWrapper">
						<iframe width="709" height="399" src="https://www.youtube.com/embed/ueph4gloDq0?rel=0&amp;&amp;showinfo=0&autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					</div>
					</div>
					<div class="vp-about-buttons">
						<a class="vp_button" href="https://vastthemes.com/ultimate-package/" target="_blank">Supercharge your Vast Themes today!</a>
					</div>
				</div>
			</div>
		</div>
        <?php
    }
endif;

if ( ! function_exists( 'vast_comment_rating_rating_field' ) ) :

	/**
	 * Create the rating interface.
	 */
	function vast_comment_rating_rating_field() {
		?>
		<div class="vp-comments-rating">
			<label for="rating">Rating<span class="required">*</span></label>
			<fieldset class="comments-rating">
				<span class="rating-container">
					<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
						<input type="radio" id="rating-<?php echo esc_attr( $i ); ?>" name="rating" value="<?php echo esc_attr( $i ); ?>" /><label for="rating-<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></label>
					<?php endfor; ?>
					<input type="radio" id="rating-0" class="star-cb-clear" name="rating" value="0" /><label for="rating-0">0</label>
				</span>
			</fieldset>
		</div>
		<?php
	}

	add_action( 'comment_form_logged_in_after', 'vast_comment_rating_rating_field' );
	add_action( 'comment_form_after_fields', 'vast_comment_rating_rating_field' );

endif;

if ( ! function_exists( 'vast_body_class' ) ) :

	/**
	 * vast hide title body class.
	 *
	 * @param array $classes Body Class.
	 */
	function vast_body_class( $classes ) {
		global $post;

		if ( $post ) {
			$is_hide = get_post_meta( $post->ID, '_hide_title', true );
			if ( $is_hide ) {
				$classes[] = 'hide-title';
			}
		}

		return $classes;
	}

	add_filter( 'body_class', 'vast_body_class' );
endif;


if ( ! function_exists( 'vast_metabox_hide_title' ) ) :

	/**
	 * Vast Hide Title Metabox
	 */
	function vast_metabox_hide_title() {
		add_meta_box(
			'vast-meta-hide-title-id', __( 'Vast Options', 'vast-plugins' ), 'vast_metabox_hide_title_control', 'page', 'side', 'high', array(
				'__block_editor_compatible_meta_box' => true,
				'__back_compat_meta_box'             => false,
			)
		);
	}

endif;

if ( is_admin() ) {
	add_action( 'add_meta_boxes', 'vast_metabox_hide_title' );
}

if ( ! function_exists( 'vast_metabox_hide_title_control' ) ) :

	/**
	 * Vast hide title checkbox
	 */
	function vast_metabox_hide_title_control() {
		global $post;
		wp_nonce_field( 'vast_nonce', 'vast_hide_title_nonce' );
		$is_hide = get_post_meta( $post->ID, '_hide_title', true );
		?>
		<p><input type="checkbox" name="hide_title" id="hide_title" value="1" <?php echo ($is_hide) ? 'checked="checked"' : ''; ?>/> <?php esc_html_e( 'Hide Title','vast' ); ?></strong></p>

		<?php
	}

endif;

if ( ! function_exists( 'vast_metabox_hide_title_save' ) ) :

	/**
	 * Vast hide title checkbox.
	 *
	 * @param string $post_id Post_id.
	 */
	function vast_metabox_hide_title_save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! empty( $_REQUEST['vast_hide_title_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['vast_hide_title_nonce'], 'vast_nonce' ) ) {
				return;
			}
		}

		$old = get_post_meta( $post_id, '_hide_title', true );
		$new = (isset( $_POST['hide_title'] )) ? sanitize_text_field( $_POST['hide_title'] ) : '';

		update_post_meta( $post_id, '_hide_title', $new,$old );
	}
	add_action( 'save_post', 'vast_metabox_hide_title_save' );
endif;

