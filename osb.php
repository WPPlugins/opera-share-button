<?php
/*
Plugin Name: Opera Share Button
Version: 0.1.5.1
Plugin URI: https://github.com/sergeyklay/OSB/
Description: Put Opera Buttons in to your post.
Author: Sergey Yakovlev
Author URI: http://klays.ru
License: GPL3
Text Domain: opera-share-button
Domain Path: /languages
*/

/*  Copyright 2012  Sergey Yakovlev  (email : sadhooklay@gmail.com)

    This file is part of Opera Share Button.

    Opera Share Button is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Opera Share Button. If not, see <http://www.gnu.org/licenses/>.
*/

if( ! class_exists( 'Osb' ) ) :

  /**
   * Basic class for Opera Share Button plugin
   *
   * @class Osb
   * @package Osb
   */
  class Osb {

    /**
     * Stores the plugin base filesystem directory
     *
     * @var string
     * @access private
     */
    private $plugin_dir;

    /**
     * Stores the URL to the plugin directory
     *
     * @var string
     * @access private
     */
    private $plugin_url;

    /** Stores the plugin version.
     *
     * @var string
     * @access private
     */
    private $version;

    /**
     * Stores the unique text domain used for I18n
     *
     * @var string
     * @access private
     */
    private $text_domain;

    /**
     * Stores plugin options.
     *
     * Options are automatically updated in the database when the destructor is called.
     *
     * @var array
     * @access private
     */
    private $options;

    /**
     * Stores the identifier of the plugin options page.
     *
     * @var string
     * @access private
     */
    private $pagehook;

    /**
     * Stores a list of buttons that are available.
     *
     * @var array
     * @access private
     */
    private $buttons;

    /**
     * Stores a list of positions of buttons
     *
     * @var array
     * @access private
     */
    private $buttons_position;

    /**
     * Stores messages that need to be displayed.
     *
     * @var array
     * @access private
     */
    private $messages = array();

    /**
     * Stores the target value for links
     *
     * @var array
     * @access private
     */
    private $links_target;

    function __construct() {
      $this->plugin_dir   = dirname( plugin_basename( __FILE__ ) );
      $this->text_domain  = 'opera-share-button';
      $this->plugin_url   = plugin_dir_url(__FILE__);
      $this->version      = '0.1.5.1';

      // Get options if they exist, else set default
      if( ! $this->options = get_option( 'osb_options' ) ) {
        $this->options = array(
          'button_list'     =>  array( 'share_button' ),
          'position'        =>  array( 'after' ),
          'user_name'       =>  '',
          'plugin_version'  =>  $this->version,
          'links_target'    =>  array( '_blank' )
        );
      }
      else
        if( ! isset( $this->options['plugin_version'] ) || $this->version > $this->options['plugin_version'] )
          add_action('init', array( &$this, 'upgrade' ));

      $this->buttons = array(
        'user_page_button'  =>  array( 'title' => __( 'User Page Button', $this->text_domain ), 'path' => '' ),
        'share_button'      =>  array( 'title' => __( 'Share Button',     $this->text_domain ), 'path' => '' )
      );

      $this->buttons_position = array(
        'before'          => __( 'Before content',          $this->text_domain ),
        'after'           => __( 'After content',           $this->text_domain ),
        'beforeandafter'  => __( 'Befor and after content', $this->text_domain ),
        'shortcode'       => __( 'Shortcode',               $this->text_domain )
      );

      $this->links_target = array(
        '_blank'    => __( 'In new window',   $this->text_domain ),
        '_self'     => __( 'In parent window', $this->text_domain )
      );

      // Enable i18n
      load_plugin_textdomain( $this->text_domain, false, $this->plugin_dir . '/languages' );

      register_activation_hook( __FILE__, array( &$this, 'activate' ) );
      register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );

      // Link to the settings page from the plugins page
      add_filter( 'plugin_action_links', array( &$this, 'action_links' ), 10, 2 );

      // Add 'Opera Share Button' to the Settings admin menu
      add_action( 'admin_menu', array( &$this, 'osb_menu' ) );

      // Add buttons to the content
      add_filter( 'the_content', array( &$this, 'display_buttons' ) );

      // Add shortcode
      add_shortcode( 'opera_buttons', array( &$this, 'osb_shortcode' ) );

      // Ability to load CSS and JS files
      add_action( 'wp_enqueue_scripts', array( &$this, 'load_scripts' ) );
    }

    /**
     * Opera Share Button activation.
     *
     * This is run when you activate the plugin, checking for compatibility, 
     * adding the default options to the database.
     *
     * @global  string  $wp_version Used to check against the required Wordpress version.
     */
    public function activate() {
      global $wp_version;
      // Check for compatibility
      try {
        // check Wordpress version
        if( version_compare( $wp_version, '3.0', '<' ) ) {
          throw new Exception( __( 'Opera Share Button requires Wordpress 3.0 or higher! Could not activate Plugin.', $this->text_domain ));
        }
      }
      catch( Exception $e ) {
        deactivate_plugins( $this->plugin_dir . '/osb.php', true );
        echo '<div id="message" class="error">' . $e->getMessage() . '</div>';
        trigger_error( 'Could not activate Opera Share Button.', E_USER_ERROR );
        return;
      }

      // Add the default options to the database, without letting WP autoload them 
      add_option( 'osb_options', $this->options, '', 'no' );
    }

    /**
     * Opera Share Button deactivation.
     *
     * This function is called whenever the plugin is being deactivated
     * and removes all options stored in the database, remove a CSS file
     * and removes scripts.
     *
     */
    public function deactivate() {
      // Delete options
      delete_option( 'osb_options' );

      // Remove a CSS file that was registered with wp_register_style()
      wp_deregister_style( 'osb' );
    }

    /**
     * Filter - Adds a 'Settings' action link on the plugin page.
     *
     * @param   array   $links  The list of links.
     * @param   string  $file   The plugin file to check.
     * @return  array           Returns the list of links with the custom link added.
     */
    public function action_links( $links, $file ) {
      if( $file != plugin_basename( __FILE__ ) )
        return $links;

      $settings_link = sprintf( '<a href="options-general.php?page=osb">%s</a>', __( 'Settings', $this->text_domain ) );

      array_unshift( $links, $settings_link );

      return $links;
    }

    /**
     * Action - This function handles upgrading the plugin to a new version.
     *
     * It gets triggered when the plugin version is different than the one stored in the database.
     *
     * @access public
     */
    public function upgrade() {
      if( ! isset( $this->options['plugin_version'] ) || $this->options['plugin_version'] < $this->version ) {
        $this->options['plugin_version']  = $this->version;
        $this->options['links_target']    = '_blank';
      }
      update_option( 'osb_options', $this->options );
    }

    /**
     * Action - Adds option page in the admin menu.
     */
    function osb_menu() {
      // We call this here just to get the page hook
      $this->pagehook = add_options_page(
        __( 'Opera Share Button Settings', $this->text_domain ),
        __( 'Opera Buttons', $this->text_domain ),
        'publish_pages',
        'osb',
        array( &$this, 'options_page' )
      );
      // Hook to update options
      add_action( 'load-' . $this->pagehook, array( &$this, 'option_update' ) );
    }

    /**
     * Display option page.
     */
    function options_page() {
      if( ! require_once( 'osb-options.php' ) ){
        return;
      }
    }

    /**
     * Validates and sanitizes user submitted options and saves them.
     */
    function option_update() {
      if( isset($_GET['action']) && 'update' == $_GET['action'] ) {
        check_admin_referer('osb_options');

        // If we have buttons that we haven't defined stop function execution.
        if( isset( $_POST['buttons'] ) ) {
          if( array_diff( $_POST['buttons'], array_keys( $this->buttons ) ) )
            wp_die( __( 'You were caught trying to do an illegal operation.', $this->text_domain ), __( 'Illegal operation', $this->text_domain ) );
          $this->options['button_list'] = $_POST['buttons'];
        }

        // If we have position thah haven't defined stop function execution.
        if( isset( $_POST['position'] ) ) {
          if( array_diff( $_POST['position'], array_keys( $this->buttons_position ) ) )
            wp_die( __( 'You were caught trying to do an illegal operation.', $this->text_domain ), __( 'Illegal operation', $this->text_domain ) );
          $this->options['position'] = $_POST['position'];
        }

         // Handle user name change.
        if( ! empty( $_POST['user_name'] ) ) {
          if( ! in_array( 'user_page_button', $_POST['buttons'] ) )
            $this->messages['error'][] = __( 'User Page Button must be enabled.', $this->text_domain );
          else
            $this->options['user_name'] = $_POST['user_name'];
        }
        else
          if( in_array( 'user_page_button', $_POST['buttons'] ) )
            $this->messages['error'][] = __( 'Please enter your user name.', $this->text_domain );

        // Where to open links
        if( isset( $_POST['targets'] ) ) {
          if( array_diff( $_POST['targets'], array_keys( $this->links_target ) ) )
            wp_die( __( 'You were caught trying to do an illegal operation.', $this->text_domain ), __( 'Illegal operation', $this->text_domain ) );
          $this->options['links_target'] = $_POST['targets'];
        }

        // If we have any error messages to display don't go any further with the function execution.
        if( empty( $this->messages['error'] ) )
          $this->messages['updated'][] = __( 'All changes were saved successfully.', $this->text_domain );
        else
          return;

        // Updating options in the database.
        update_option( 'osb_options', $this->options );
      }
    }

    /**
     * Displays Opera Buttons
     *
     * Function are using to add Opera Share Button to the content
     *
     * @access public
     * @param  string  $content   Is a string containing the enclosed content
     * @return string             Prepared string like HTML
     */
    public function display_buttons( $content ) {
      global $post;
      if( $this->options = get_option( 'osb_options' ) ) {

        $osb_html = '<div class="opera-buttons">';

        // User Page Button
        if( in_array( 'user_page_button', $this->options['button_list'] ) && $this->options['user_name'] ) {
          $osb_html .= '<a class="osb-page" href="http://my.opera.com/' . $this->options['user_name'] . '" target="' . reset( $this->options['links_target'] ) . '">';
          $osb_html .= '<img src="' . $this->plugin_url . 'img/myopera20-1.png" alt="' . __( 'Go to My Opera Page', $this->text_domain ) . '" />';
          $osb_html .= '</a>';
        }

        // Share Button
        if( in_array( 'share_button', $this->options['button_list'] ) ) {
          $osb_html .= '<a class="osb-share" href="http://my.opera.com/community/post/?url=' . urlencode( $post->guid ) . '&title=' . $post->post_title . '" target="' . reset( $this->options['links_target'] ) . '">';
          $osb_html .= '<img src="' . $this->plugin_url . 'img/operashare20-2.png" alt="' . __( 'Share this post', $this->text_domain ) . '" />';
          $osb_html .= '</a>';
        }

        $osb_html .= '</div>';

        // Indication where show Opera Buttons depending on selected item in admin page
        // Set the internal pointer of an array to its first element
        $pos_name = reset( $this->options['position'] );
        switch( $pos_name ) {
          case 'before' :
            return $osb_html . $content;
          case 'after' :
            return $content . $osb_html;
          case 'beforeandafter' :
            return $osb_html . $content . $osb_html;
          case 'shortcode' :
          default :
            return $content;
        }
      }
      else
        return $content;
    }

    /**
     * Action - Adds ability to load CSS
     *
     * @access public
     */
    public function load_scripts() {
      // Register a CSS style file for later use with wp_enqueue_style()
      wp_register_style(
        'osb',
        $this->plugin_url . 'css/opera-buttons.css',
        array(),
        $this->version,
        'screen'
      );

      // Add/enqueue a CSS style file to the wordpress generated page
      wp_enqueue_style( 'osb' );
    }

    /**
     * Action - Create shortcode
     *
     * Function are using to add Opera Share Button shortcode to the content
     *
     * @access  public
     * @param   string   $content   Is a string containing the enclosed content
     * @return  string              Prepared string like HTML
     */
    public function osb_shortcode( $content ) {
      global $post;
      if( $this->options = get_option( 'osb_options' ) ) {

        $osb_html = '<div class="opera-buttons">';

        // User Page Button
        if( in_array( 'user_page_button', $this->options['button_list'] ) && $this->options['user_name'] ) {
          $osb_html .= '&nbsp;<a href="http://my.opera.com/' . $this->options['user_name'] . '/">';
          $osb_html .= '<img src="' . $this->plugin_url . 'img/myopera20-1.png" alt="' . __( 'Go to My Opera Page', $this->text_domain ) . '" />';
          $osb_html .= '</a>';
        }

        // Share Button
        if( in_array( 'share_button', $this->options['button_list'] ) ) {
          $osb_html .= '&nbsp;<a class="osb-share">';
          $osb_html .= '<img src="' . $this->plugin_url . 'img/operashare20-2.png" alt="' . __( 'Share this post', $this->text_domain ) . '" />';
          $osb_html .= '<script type="text/javascript" src="' . $this->plugin_url . 'js/share.js"></script>';
          $osb_html .= '</a>';
        }

        $osb_html .= '</div>';

        return $osb_html;
      }
    }

    /**
     * Render messages.
     *
     * @access private
     * @return string Return current message prepared in html class
     */
    private function get_messages_html() {
      $html = '';
      foreach($this->messages as $type => $messages){
        $html .= '<div class="' . $type . '">';
        foreach( $messages as $message )
          $html .= '<p>' . $message . '</p>';
        $html .= '</div>';
      }
      return $html;
    }

  } // end class

endif;

$osb = new Osb();
