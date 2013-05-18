<?php defined( 'ABSPATH' ) OR die( 'No direct access.' );

/**
 * Class Admin_External_Links
 * @category WordPress Plugins
 */
final class Admin_External_Links {

	/**
	 * Used as text domain (for translations)
	 * @var string
	 */
	protected $domain = 'wp_external_links';

	/**
	 * Options to be saved and their default values
	 * @var array
	 */
	protected $save_options = array(
		'general' => array(
			'target' => '_none',
			'use_js' => 1,
			'external' => 1,
			'nofollow' => 1,
			'title' => '%title%',
			'filter_page' => 1,
			'filter_posts' => 1,
			'filter_comments' => 1,
			'filter_widgets' => 1,
			'class_name' => 'ext-link',
			'filter_excl_sel' => '.excl-ext-link',
		),
		'style' => array(
			'icon' => 0,
			'no_icon_class' => 'no-ext-icon',
			'no_icon_same_window' => 0,
		),
		'screen' => array(
			'menu_position' => NULL,
		),
	);

	/**
	 * Meta box page object
	 * @var WP_Meta_Box_Page
	 */
	public $meta_box_page = NULL;

	/**
	 * Ajax form object
	 * @var WP_Ajax_Option_Form
	 */
	public $form = NULL;

	/**
	 * Location of the plugin file
	 * @var string
	 */
	protected $plugin_file = NULL;


	/**
	 * Constructor
	 * @param string $file  Location of the plugin file
	 */
	public function __construct( $plugin_file = NULL ) {
		// set location of plugin file
		$this->plugin_file = ( $plugin_file === NULL ) ? __FILE__ : $plugin_file;

		// set meta box page
		$this->meta_box_page = new WP_Meta_Box_Page_01();

		// set ajax forms (also used by front-end)
		$this->form = new WP_Option_Forms_01( $this->domain, $this->save_options );

		// init admin
		if ( is_admin() )
			$this->init();
	}

	/**
	 * Initialize Admin
	 */
	public function init() {
		// load text domain for translations
		load_plugin_textdomain( $this->domain, FALSE, dirname( plugin_basename( $this->plugin_file ) ) . '/lang/' );

		// set activation hook
		register_activation_hook( $this->plugin_file, array( $this, 'call_activation' ) );

		// set deactivation hook
		register_deactivation_hook( $this->plugin_file, array( $this, 'call_deactivation' ) );

		// set options for add_page_method
		$menu_pos = $this->form->set_current_option( 'screen' )->value( 'menu_position' );

		// init meta box page
		$this->meta_box_page->init(
			// settings
			array(
				'menu_title' => $this->__( 'External Links' ),
				'page_slug' => strtolower( $this->domain ),
				'add_page_method' => ( ! empty( $menu_pos ) AND $menu_pos != 'admin.php' ) ? 'add_submenu_page' : 'add_menu_page',
				'parent_slug' => ( ! empty( $menu_pos ) AND $menu_pos != 'admin.php' ) ? $menu_pos : NULL,
				'column_widths' => array(
					1 => array( 99 ),
					2 => array( 69, 29 ),
				),
				'icon_url' => plugins_url( 'images/icon-wp-external-links-16.png', $this->plugin_file ),
			),
			// load callback
			array( $this, 'call_load_meta_box' )
		);
	}

	/**
	 * Translate text in current domain
	 * @param string $text
	 * @return string
	 */
	public function __( $text ) {
		return translate( $text, $this->domain );
	}

	/**
	 * Translate text in current domain
	 * @param string $text
	 * @return string
	 */
	public function _e( $text ) {
		echo translate( $text, $this->domain );
	}

	/**
	 * Load meta box action
	 */
	public function call_load_meta_box( $meta_box ) {
		// add filters
		$meta_box->add_title_filter( array( $this, 'call_page_title' ) )
							->add_screen_settings_filter( array( $this, 'call_screen_settings' ) )
							->add_contextual_help_filter( array( $this, 'call_contextual_help' ) );

		// add meta boxes
		// add_meta_box( $title, $callback, $context = 'normal', $id = NULL, $priority = 'default', $callback_args = NULL )
		$meta_box->add_meta_box( $this->__( 'General Settings' ), array( $this, 'call_box_general_settings' ), 1 )
							->add_meta_box( $this->__( 'Style Settings' ), array( $this, 'call_box_style_settings' ), 1 )
							->add_meta_box( $this->__( 'About this Plugin' ), array( $this, 'call_box_about' ), 2 )
							->add_meta_box( $this->__( 'Other Plugins' ), array( $this, 'call_box_other_plugins' ), 2 );

		// stylesheets
		wp_enqueue_style( 'jquery-tipsy', plugins_url( 'css/tipsy.css', $this->plugin_file ), FALSE, WP_EXTERNAL_LINKS_VERSION );

		// scripts
		wp_enqueue_script( 'jquery-tipsy', plugins_url( '/js/jquery.tipsy.js', $this->plugin_file ), array( 'jquery' ), WP_EXTERNAL_LINKS_VERSION );
		wp_enqueue_script( 'admin-external-links', plugins_url( '/js/admin-external-links.js', $this->plugin_file ), array( 'postbox', 'option-forms' ), WP_EXTERNAL_LINKS_VERSION );
	}

	/**
	 * Screen settings
	 * @param string $content
	 * @return string
	 */
	public function call_screen_settings( $content ) {
		$content .= '<h5>'. $this->__( 'Menu Setting' ) .'</h5>' . "\n";
		$content .= '<div class="extra-prfs">' . "\n";
		$content .= $this->__( 'Admin menu position' ) . ': ' . "\n";
		$content .= $this->form->open_screen_option( 'screen', 'menu_position' );
		$content .= $this->form->select( 'menu_position', array(
			'admin.php' => 'Main menu',
			'index.php' => $this->__( 'Subitem of Dashboard' ),
			'edit.php' => $this->__( 'Subitem of Posts' ),
			'upload.php' => $this->__( 'Subitem of Media' ),
			'link-manager.php' => $this->__( 'Subitem of Links' ),
			'edit.php?post_type=page' => $this->__( 'Subitem of Pages' ),
			'edit-comments.php' => $this->__( 'Subitem of Comments' ),
			'themes.php' => $this->__( 'Subitem of Appearance' ),
			'plugins.php' => $this->__( 'Subitem of Plugins' ),
			'users.php' => $this->__( 'Subitem of Users' ),
			'tools.php' => $this->__( 'Subitem of Tools' ),
			'options-general.php' => $this->__( 'Subitem of Settings' ),
		)) . "\n";
		$content .= '</div>' . "\n";

		return $content;
	}

	/**
	 * Contextual_help (callback)
	 * @param string $content
	 * @return string
	 */
	public function call_contextual_help( $content ) {
		$help = '';
		$help .= $this->meta_box_page->get_ob_callback( array( $this, 'call_box_about' ) );
		$help .= $this->hr();
		$help .= '<h4><img src="'. plugins_url( 'images/icon-wp-16.gif', $this->plugin_file ) .'" width="16" height="16" /> '
				. $this->__( 'WordPress' ) .'</h4>';
		return $help . $content;
	}

	/**
	 * Add icon to page title
	 * @return string
	 */
	public function call_page_title( $title ) {
		// when updated set update message
		if ( $_GET[ 'settings-updated' ] == 'true' ) {
			$title .= '<div class="updated settings-error" id="setting-error-settings_updated" style="display:none">'
				. '<p><strong>' . __( 'Settings saved.' ) .'</strong></p>'
				. '</div>';
		}

		$title = '<div class="icon32" id="icon-options-custom" style="background:url( '. plugins_url( 'images/icon-wp-external-links-32.png', $this->plugin_file ) .' ) no-repeat 50% 50%"><br></div>'
				. $title;

		return $title;
	}

	/**
	 * Meta Box: General Settings
	 */
	public function call_box_general_settings() {
		echo $this->form->set_current_option( 'general' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:300px;"><?php $this->_e( 'Set <code>target</code> for external links' ) ?>
						<?php echo $this->tooltip_help( 'Specify the target (window or tab) for opening external links.' ) ?></th>
				<td class="target_external_links">
					<label><?php echo $this->form->radio( 'target', '_blank', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( '<code>_blank</code>, new window' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open every external link in a new window or tab.' ) ?>
					<br/>
					<label><?php echo $this->form->radio( 'target', '_top', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( '<code>_top</code>, topmost frame' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open in current window or tab, when framed in the topmost frame.' ) ?>
					<br/>
					<label><?php echo $this->form->radio( 'target', '_new', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( '<code>_new</code>, seperate window' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open new window the first time and use this window for each external link.' ) ?>
					<br/>
					<label><?php echo $this->form->radio( 'target', '_none', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( '<code>_none</code>, current window' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open in current window or tab, when framed in the same frame.' ) ?>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Add to <code>rel</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Set values for the "rel"-atribute of external links.' ) ?></th>
				<td><label><?php echo $this->form->checkbox( 'external', 1 ); ?>
						<span><?php $this->_e( 'Add <code>"external"</code>' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Add "external" to the "rel"-attribute of external links.' ) ?>
					<br/><label><?php echo $this->form->checkbox( 'nofollow', 1 ); ?>
						<span><?php $this->_e( 'Add <code>"nofollow"</code>' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Add "nofollow" to the "rel"-attribute of external links (unless link already has "follow").' ) ?>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Add to <code>class</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Add one or more extra classes to the external links, seperated by a space. It is optional, else just leave field blank.' ) ?></th>
				<td><label><?php echo $this->form->text( 'class_name' ); ?></label></td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Set <code>title</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Set title attribute for external links. Use %title% for the original title value.' ) ?></th>
				<td><label><?php echo $this->form->text( 'title' ); ?>
					<span class="description"><?php _e( 'Use <code>%title%</code> for the original title value.' ) ?></span></label></td>
			</tr>
			</table>

			<?php echo $this->hr(); ?>

			<table class="form-table">
			<tr>
				<th style="width:300px;"><?php $this->_e( 'Valid XHTML Strict' ) ?>
						<?php echo $this->tooltip_help( 'The "target"-attribute is not valid XHTML strict code. Enable this option to remove the target from external links and use the JavaScript method (built-in this plugin) for opening links.' ) ?></th>
				<td>
					<label><?php echo $this->form->checkbox( 'use_js', 1, array( 'class' => 'field_use_js' ) ); ?>
					<span><?php $this->_e( 'Use JavaScript for opening links in given target, instead of setting <code>target</code>-attribute <em>(recommended)</em>' ) ?></span></label>
				</td>
			</tr>
			</table>

			<?php echo $this->hr(); ?>

			<table class="form-table">
			<tr>
				<th style="width:300px;"><?php $this->_e( 'Apply settings to external links on...' ) ?>
						<?php echo $this->tooltip_help( 'Choose contents for applying settings to external links.' ) ?></th>
				<td>
					<label><?php echo $this->form->checkbox( 'filter_page', 1 ); ?>
					<span><?php $this->_e( 'All contents' ) ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_posts', 1 ); ?>
							<span><?php $this->_e( 'Post contents' ) ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_comments', 1 ); ?>
							<span><?php $this->_e( 'Comments' ) ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_widgets', 1 ); ?>
							<span><?php 
								if ( self::check_widget_content_filter() ):
									$this->_e( 'All widgets' );
									echo $this->tooltip_help( 'Applied to all widgets by using the "widget_content" filter of the Widget Logic plugin' );
								else:
									$this->_e( 'All text widgets' );
									echo $this->tooltip_help( 'Only the text widget will be applied. To apply to all widget you should select "All contents" option.' );
								endif;
							?></span></label>
				</td>
			</tr>
			<tr class="filter_excl_sel">
				<th><?php $this->_e( 'Do NOT apply settings on...' ) ?>
					<?php echo $this->tooltip_help( 'The external links of these selection will be excluded for the settings of this plugin. Define the selection by using CSS selectors.' ) ?></th>
				<td><label><?php echo $this->form->textarea( 'filter_excl_sel' ); ?>
						<span class="description"><?php _e( 'Define selection by using CSS selectors, f.e.: <code>.excl-ext-link, .entry-title, #comments-title</code> (look <a href="http://code.google.com/p/phpquery/wiki/Selectors" target="_blank">here</a> for available selectors).' ) ?></span></label>
				</td>
			</tr>
			</table>
		</fieldset>

		<p style="position:absolute;"><a id="admin_menu_position" href="#"><?php _e( 'Change menu position in "Screen Options"' ) ?></a></p>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: Style Settings
	 */
	public function call_box_style_settings() {
		echo $this->form->set_current_option( 'style' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:300px;"><?php $this->_e( 'Set icon for external link' ) ?>
						<?php echo $this->tooltip_help( 'Set an icon that wll be shown for external links. See example on the right side.' ) ?></th>
				<td>
					<div>
						<div style="width:15%;float:left">
							<label><?php echo $this->form->radio( 'icon', 0 ); ?>
							<span><?php $this->_e( 'No icon' ) ?></span></label>
						<?php for ( $x = 1; $x <= 20; $x++ ): ?>
							<br/>
							<label title="<?php echo sprintf( $this->__( 'Icon %1$s: choose this icon to show for all external links or add the class \'ext-icon-%1$s\' to a specific link.' ), $x ) ?>">
							<?php echo $this->form->radio( 'icon', $x ); ?>
							<img src="<?php echo plugins_url( 'images/external-'. $x .'.png', $this->plugin_file ) ?>" /></label>
							<?php if ( $x % 5 == 0 ): ?>
						</div>
						<div style="width:15%;float:left">
							<?php endif; ?>
						<?php endfor; ?>
						</div>
						<div style="width:29%;float:left;"><span class="description"><?php $this->_e( 'Example:' ) ?></span>
							<br/><img src="<?php echo plugins_url( 'images/link-icon-example.png', $this->plugin_file ) ?>"	/>
						</div>
						<br style="clear:both" />
					</div>
				</td>
			</tr>
			<tr>
				<th style="width:300px;"><?php $this->_e( 'Set no-icon class' ) ?>
						<?php echo $this->tooltip_help( 'Set this class for links, that should not have the external link icon.' ) ?></th>
				<td><label><?php echo $this->form->text( 'no_icon_class', array( 'class' => '' ) ); ?></label>
					<label><?php echo $this->form->checkbox( 'no_icon_same_window', 1 ); ?>
					<span><?php $this->_e( 'Always open links with no-icon class in same window or tab' ) ?></span></label>
						<?php echo $this->tooltip_help( 'When enabled external links containing the no-icon class will always be opened in the current window or tab. No matter which target is set.' ) ?>
				</td>
			</tr>
			</table>
		</fieldset>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: About...
	 */
	public function call_box_about() {
?>
		<h4><img src="<?php echo plugins_url( 'images/icon-wp-external-links-16.png', $this->plugin_file ) ?>" width="16" height="16" /> <?php $this->_e( 'WP External Links' ) ?></h4>
		<div>
			<p><?php printf( $this->__( 'Current version: <strong>%1$s</strong>' ), WP_EXTERNAL_LINKS_VERSION ) ?></p>
			<p><?php $this->_e( 'Manage external links on your site: open in new window/tab, set link icon, add "external", add "nofollow" and more.' ) ?></p>
			<p><a href="http://www.freelancephp.net/contact/" target="_blank"><?php $this->_e( 'Questions or suggestions?' ) ?></a></p>
			<p><?php $this->_e( 'If you like this plugin please send your rating at WordPress.org.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/wp-external-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-external-links-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>
<?php
	}

	/**
	 * Meta Box: Other Plugins
	 */
	public function call_box_other_plugins() {
?>
		<h4><img src="<?php echo plugins_url( 'images/icon-email-encoder-bundle-16.png', $this->plugin_file ); ?>" width="16" height="16" /> Email Encoder Bundle</h4>
		<div>
			<?php if ( is_plugin_active( 'email-encoder-bundle/email-encoder-bundle.php' ) ): ?>
				<p><?php $this->_e( 'This plugin is already activated.' ) ?> <a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/options-general.php?page=email-encoder-bundle/email-encoder-bundle.php"><?php $this->_e( 'Settings' ) ?></a></p>
			<?php elseif( file_exists( WP_PLUGIN_DIR . '/email-encoder-bundle/email-encoder-bundle.php' ) ): ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugins.php?plugin_status=inactive"><?php $this->_e( 'Activate this plugin.' ) ?></a></p>
			<?php else: ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugin-install.php?tab=search&type=term&s=Email+Encoder+Bundle+freelancephp&plugin-search-input=Search+Plugins"><?php $this->_e( 'Get this plugin now' ) ?></a></p>
			<?php endif; ?>

			<p><?php $this->_e( 'Protect email addresses on your site from spambots and being used for spamming by using one of the encoding methods.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/email-encoder-bundle/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/email-encoder-php-class-wp-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>

		<?php echo $this->hr(); ?>

		<h4><img src="<?php echo plugins_url( 'images/icon-wp-mailto-links-16.png', $this->plugin_file ); ?>" width="16" height="16" /> WP Mailto Links</h4>
		<div>
			<?php if ( is_plugin_active( 'wp-mailto-links/wp-mailto-links.php' ) ): ?>
				<p><?php $this->_e( 'This plugin is already activated.' ) ?> <a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/options-general.php?page=wp-mailto-links/wp-mailto-links.php"><?php $this->_e( 'Settings' ) ?></a></p>
			<?php elseif( file_exists( WP_PLUGIN_DIR . '/wp-mailto-links/wp-mailto-links.php' ) ): ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugins.php?plugin_status=inactive"><?php $this->_e( 'Activate this plugin.' ) ?></a></p>
			<?php else: ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+Mailto+Links+freelancephp&plugin-search-input=Search+Plugins"><?php $this->_e( 'Get this plugin now' ) ?></a></p>
			<?php endif; ?>

			<p><?php $this->_e( 'Manage mailto links on your site and protect emails from spambots, set mail icon and more.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/wp-mailto-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-mailto-links-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>
<?php
	}

	/**
	 * Activation plugin callback
	 */
	public function call_activation() {
		// check for upgrading saved options to v1.00
		$old_options = get_option( 'WP_External_Links_options' );

		if ( ! empty( $old_options ) ) {
			$new_options = $this->save_options;

			foreach ( $old_options AS $option ) {
				$new_options[ 'general' ][ 'target' ] = $old_options[ 'target' ];
				$new_options[ 'general' ][ 'use_js' ] = $old_options[ 'use_js' ];
				$new_options[ 'general' ][ 'external' ] = $old_options[ 'external' ];
				$new_options[ 'general' ][ 'nofollow' ] = $old_options[ 'nofollow' ];
				$new_options[ 'general' ][ 'filter_page' ] = $old_options[ 'filter_whole_page' ];
				$new_options[ 'general' ][ 'filter_posts' ] = $old_options[ 'filter_posts' ];
				$new_options[ 'general' ][ 'filter_comments' ] = $old_options[ 'filter_comments' ];
				$new_options[ 'general' ][ 'filter_widgets' ] = $old_options[ 'filter_widgets' ];
				$new_options[ 'general' ][ 'class_name' ] = $old_options[ 'class_name' ];
				$new_options[ 'style' ][ 'icon' ] = $old_options[ 'icon' ];
				$new_options[ 'style' ][ 'no_icon_class' ] = $old_options[ 'no_icon_class' ];
				$new_options[ 'style' ][ 'no_icon_same_window' ] = $old_options[ 'no_icon_same_window' ];
			}

			// save new format option values
			update_option( 'wp_external_links-general', $new_options[ 'general' ] );
			update_option( 'wp_external_links-style', $new_options[ 'style' ] );

			// delete old format option values
			delete_option( 'WP_External_Links_options' );
			unregister_setting( 'WP_External_Links', 'WP_External_Links_options' );
		}
	}

	/**
	 * Deactivation plugin callback
	 */
	public function call_deactivation() {
		$this->form->delete_options();
	}

	/**
	 * Set tooltip help
	 * @param string $text
	 * @return string
	 */
	public function tooltip_help( $text ) {
		$text = $this->__( $text );
		$text = htmlentities( $text );

		$html = '';
		$html .= '<a href="#" class="tooltip-help" title="'. $text .'">';
		$html .= '<img alt="" title="" src="'. plugins_url( '/images/help-icon.png', $this->plugin_file ) .'" />';
		$html .= '</a>';
		return $html;
	}

	/**
	 * Get html seperator
	 * @return string
	 */
	protected function hr() {
		return '<hr style="border:1px solid #FFF; border-top:1px solid #EEE;" />';
	}


	/**
	 * Check if widget_content filter is available (Widget Logic Plugin)
	 * @return boolean
	 * @static
	 */
	public static function check_widget_content_filter() {
		// set widget_content filter of Widget Logic plugin
		$widget_logic_opts = get_option( 'widget_logic' );

		if ( function_exists( 'widget_logic_expand_control' ) AND is_array( $widget_logic_opts ) AND key_exists( 'widget_logic-options-filter', $widget_logic_opts ) )
			return ( $widget_logic_opts[ 'widget_logic-options-filter' ] == 'checked' );

		return FALSE;
	}

} // End Admin_External_Links Class
