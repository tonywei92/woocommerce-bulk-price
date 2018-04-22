<?php

namespace tonysong\bulk_price\settings;
class Admin_Menu {
	private $parent_slug = 'woocommerce';

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_bulk_menu' ) );
	}

	function register_settings() {
		register_setting( 'bulk_price', 'bulk_price_style' );
		register_setting( 'bulk_price', 'bulk_price_color' );
	}

	function add_bulk_menu() {
		$page_title = "Bulk Price Settings";
		$menu_title = "Bulk Price";
		$capability = "manage_options";
		$function   = array( $this, 'my_plugin_options' );
		add_submenu_page( $this->parent_slug, $page_title, $menu_title, $capability, $this->parent_slug . '-bulkprice', $function );
	}

	function my_plugin_options() {
		?>
        <div class="wrap">
            <h1>Bulk Price Settings</h1>
            <form method="post" action="options.php">
				<?php settings_fields( 'bulk_price' ); ?>
				<?php do_settings_sections( 'bulk_price' ); ?>
				<?php settings_errors();
				$style = get_option( 'bulk_price_style', 0 );
				$color = get_option( 'bulk_price_color', '#FF0000' );
				?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Style</th>
                        <td><select name="bulk_price_style">
                                <option value="0" <?php selected( $style == 0 ) ?>>Default</option>
                                <option value="1" <?php selected( $style == 1 ) ?>>Compact</option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Discounted price color</th>
                        <td><input type="text" name="bulk_price_color"
                                   value="<?php echo $color; ?>"/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Preview</th>
                        <td>
                            <div class="price-previews">
                                <div class="preview-item preview-0">
									<?php
									echo '<span style="text-decoration: line-through">' . wc_price( 5400 ) . '</span> <span class="discounted" style="color:' . $color . '"><strong>' . wc_price( 4860 ) . '&nbsp;(10% off)</strong></span>'
									?>
                                </div>
                                <div class="preview-item preview-1">
	                                <?php
	                                echo '<span class="discounted" style="color:' . $color . '"><strong>' . wc_price( 4860 ) . '</strong></span>'
	                                ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <script>
                    jQuery(function($){
                        var $select_style = $('select[name=bulk_price_style]');
                        var $input_color = $('input[name=bulk_price_color]');
                        redisplay();
                        $input_color.iris({
                            hide:false,
                            change: function(event, ui) {
                                $(".discounted").css( 'color', ui.color.toString());
                            }
                        });

                        $select_style.on('change', function(){
                            redisplay();
                        });

                        $input_color.on('change', function(){
                            $input_color.iris('color', $(this).val());
                        });

                        function redisplay(){
                            $('.preview-item').hide();
                            $('.preview-' + $select_style.val()).show();
                        }
                    })
                </script>
				<?php submit_button(); ?>

            </form>
        </div>
		<?php
	}
}

return new Admin_Menu();