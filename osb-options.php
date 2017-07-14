<?php
// Display messages and errors
echo $this->get_messages_html();
?>
<div class="wrap">
  <?php screen_icon( 'options-general' ); ?>
  <h2><?php _e( 'Opera Share Button Settings', $this->text_domain ); ?></h2>
  <form action="<?php echo admin_url( "options-general.php?page=osb&action=update" ); ?>" method="post">
    <?php wp_nonce_field( 'osb_options' ); ?>
    <input type="hidden" name="action" value="save_osb_options" />
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><?php _e( 'What will be displayed:', $this->text_domain ); ?></th>
          <td>
            <div class="feature-filter">
              <ol class="feature-group">
<?php
foreach( $this->buttons as $button => $name )
  echo '<li><label for="' . $button . '"><input id="' . $button . '" name="buttons[]" type="checkbox" value="' . $button . '" ' . checked( $button, in_array( $button, $this->options['button_list']) ? $button : false, false ) . ' /> ' . __( $name['title'], $this->text_domain ) . '</label></li>';
?>
              </ol>
              <div class="clear">&nbsp;</div>
            </div>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php _e( 'Your Opera user name:', $this->text_domain );?></th>
          <td>
            <input id="user_name" name="user_name" type="text" class="regular-text code" placeholder="<?php _e( 'Your login in My Opera Social Network', $this->text_domain ); ?>" value="<?php echo esc_html( $this->options['user_name'] ); ?>" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php _e( 'Buttons position:', $this->text_domain ); ?></th>
          <td>
            <select name="position[]" onchange="if( this.value == 'shortcode' ) { getElementById( 'shortcode' ) . style.display = 'inline'; } else { getElementById( 'shortcode' ) . style.display = 'none'; }" style="width:200px;">
<?php
  foreach( $this->buttons_position as $position => $title )
    echo '<option value="' . $position . '"' . selected( true, in_array( $position, $this->options['position'] ), false ) . '>' . __( $title, $this->text_domain ) . '</option>';
?>
            <select>
            <span id="shortcode" style="color: rgb(136, 136, 136); <?php if( $this->options['position'] == 'shortcode' ) { echo( 'display:inline' ); } else { echo( 'display:none' ); }?>"><?php echo __( "If you would like to add a Opera Buttons to your website, just copy and put this shortcode onto your post or page:", $this->text_domain ); ?> [opera_buttons].</span>
          </td>
        </tr>
        <tr>
          <th scope="row"><?php _e( 'Where to open links:', $this->text_domain ); ?></th>
          <td>
            <div class="feature-filter">
              <ol class="feature-group">
<?php
  foreach( $this->links_target as $target => $title )
    echo '<li><label for="link' . $target . '"><input id="link' . $target . '" name="targets[]" type="radio" value="' . $target . '"' . checked( $target, in_array( $target, $this->options['links_target'] ) ? $target : false, false ) . ' /> ' . __( $title, $this->text_domain ) . '</label></li>';
?>
              </ol>
              <div class="clear">&nbsp;</div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input name="submit" type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->text_domain ) ?>" />
    </p>
  </form>
</div>
