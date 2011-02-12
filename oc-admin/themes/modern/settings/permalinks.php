<?php
/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$htaccess_status = $this->_get('htaccess');
$file_status     = $this->_get('file');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
        <script type="text/javascript">
            var base_url    = '<?php echo osc_base_url() ; ?>';
            var s_close     = '<?php _e('Close'); ?>';
            var s_view_more = '<?php _e('View more'); ?>';
        </script>
        <?php $this->osc_print_head() ; ?>
    </head>
    <body>
        <?php $this->osc_print_header() ; ?>
        <div id="update_version" style="display:none;"></div>
        <div class="Header"><?php _e('Dashboard'); ?></div>

        <script type="text/javascript">
            $.extend({
                initDashboard: function(args) {
                    $.isArray(args) ? true : false;
                    $.each(args, function(i, val) {
                        $("#" + val.substr(3)).show();
                        $("#" + val).attr('checked', 'checked');
                    });
                },
                setCookie: function(args) {
                    $.isArray(args) ? true : false;
                    $.cookie.set("osc_admin_main", args, {json: true});
                }
            });

            $(function() {
                if ($.cookie.get("osc_admin_main") == '' || $.cookie.get("osc_admin_main") == null) {
                    // create cookies if admin is a first timer...
                    var sections = ['cb_last_items', 'cb_statistics', 'cb_last_comments', 'cb_last_news'];
                    $.initDashboard(sections);
                    $.setCookie(sections);

                } else { // else read it and apply it!
                    var enabled_sections = $.cookie.get("osc_admin_main", true);
                    $.initDashboard(enabled_sections);
                    $.setCookie(enabled_sections);
                }

                // save settings
                $("#button_save").click(function() {
                    var sections = [];
                    $('#checkboxes input:checkbox:checked').each(function() {
                        sections.push($(this).attr('id'));
                    });

                    $.setCookie(sections);
                    $('#main_div').hide();
                });

                $('#button_open').click(function() {
                    $('#main_div').toggle();
                });

                $("#checkboxes input[type='checkbox']").click(function() {
                    var val = $(this).attr('id');
                    $("#" + val.substr(3)).toggle();
                });
            });
        </script>
		<div id="content">
            <div id="separator"></div>

			<?php include_once osc_current_admin_theme_path() . 'include/backoffice_menu.php'; ?>

		    <div id="right_column">
				<div id="content_header" class="content_header">
					<div style="float: left;">
                        <img src="<?php echo  osc_current_admin_theme_url() ; ?>images/back_office/settings-icon.png" alt="" title=""/>
                    </div>
					<div id="content_header_arrow">&raquo; <?php _e('Permalinks'); ?></div>
					<div style="clear: both;"></div>
				</div>
				
				<div id="content_separator"></div>
				<?php osc_show_flash_message('admin'); ?>
				<!-- settings form -->
				<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
					<div style="padding: 20px;">

                        <form action="<?php echo osc_admin_base_url(true); ?>" method="post">
                            <input type="hidden" name="page" value="settings" />
                            <input type="hidden" name="action" value="permalinks_post" />
						
                            <div style="float: left; width: 100%;">
                                <fieldset>
                                    <legend><?php _e('Nice URLs') ; ?></legend>
                                    <div><?php _e('By default OSClass uses web URLs which have question marks and lots of numbers in them, however OSClass offers you the ability to create a custom URL structure for your permalinks and archives. This can improve the aesthetics, usability, and forward-compatibility of your links. A number of tags are available, and here are some examples to get you started'); ?>.</div>
                                    <br />
                                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_rewrite_enabled() ? 'checked="true"' : ''); ?> name="rewrite_enabled" id="rewrite_enabled" value="1" />
                                    <label for="rewrite_enabled"><?php _e('Enable nice URLs') ; ?></label>
                                </fieldset>
                            </div>

                            <?php if(osc_rewrite_enabled()) { ?>
                            <div style="float: left; width: 100%;">
                                <fieldset>
                                    <legend><?php _e('.htaccess file'); ?></legend>
                                    <?php switch($htaccess_status) {
                                            case 1:     _e('Module <em>mod_rewrite</em> was found on the server.');
                                            break;
                                            case 2:     _e('Warning! Rewrite module wasn\'t found on the server. This means you don\'t have it enabled or you\'re running PHP as CGI (or fastCGI). In the case you don\'t have mod_rewrite you could still use nice urls if AllowPathInfo option is On in your Apache configuration (we can not know if it\'s enabled or not, but usually it is). With restricted nice url "index.php" will appear as a part of your URL (ie. http://www.yourdomain.com/index.php/nice/url).');
                                            break;
                                          }
                                    ?>
                                         <br/>
                                    <?php switch ($file_status) {
                                            case 3:     _e('Error. We could not write the .htaccess file on your server. Please create a file called .htaccess on the root of your OSClass installation with the following content.');
                                            break;
                                            case 1:     _e('File .htaccess already exists. Please, check that the .htaccess file has the following content.');
                                            break;
                                            case 2:     _e('We create a .htaccess file on the root of your OSClass installation.');
                                            break;
                                          }
                                    ?>
                                    <div style="margin-top: 10px; clear: both;"></div>
                                    <div style="float: left; width: 50%;">
                                        <?php _e('Content of .htaccess file should look like this:'); ?>
                                        <textarea rows="8" style="width: 90%;">
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase <?php echo REL_WEB_URL; ?>

    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . <?php echo REL_WEB_URL; ?>index.php [L]
</IfModule>
                                        </textarea>
                                    </div>
                                    <div style="float: right; width: 50%;">
                                        <?php
                                            if(file_exists(ABS_PATH.'.htaccess')) {
                                                $htaccess_content = file_get_contents(ABS_PATH . '.htaccess');
                                                if($htaccess_content) {
                                                    _e('Current content of your .htaccess file:');
                                        ?>
                                        <br />
                                        <textarea rows="8" style="width: 90%;"><?php echo $htaccess_content ; ?></textarea>
                                        <?php }
                                        } ?>
                                    </div>
                                </fieldset>
                            </div>
                            <?php } ?>
                            <div style="clear: both;"></div>
                            <input id="button_save" type="submit" value="<?php _e('Update') ; ?>" />
                        </form>
					</div>
				</div>
			</div> <!-- end of right column -->
        </div>
        <?php $this->osc_print_footer() ; ?>
    </body>
</html>