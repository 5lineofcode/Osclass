<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    function customPageHeader() { ?>
        <h1><?php _e('Manage Plugins') ; ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
            <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&amp;action=add" class="btn btn-green ico ico-32 ico-add-white float-right"><?php _e('Add plugin') ; ?></a>
	   </h1>
<?php
    }
    osc_add_hook('admin_page_header','customPageHeader');

    function customPageTitle($string) {
        return sprintf(__('Plugins &raquo; %s'), $string);
    }
    osc_add_filter('admin_title', 'customPageTitle');

    //customize Head
    function customHead() { ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('input:hidden[name="installed"]').each(function() {
                    $(this).parent().parent().children().css('background', 'none') ;
                    if( $(this).val() == '1' ) {
                        if( $(this).attr("enabled") == 1 ) {
                            $(this).parent().parent().css('background-color', '#EDFFDF') ;
                        } else {
                            $(this).parent().parent().css('background-color', '#FFFFDF') ;
                        }
                    } else {
                        $(this).parent().parent().css('background-color', '#FFF0DF') ;
                    }
                }) ;
            });
            
        </script>
        <?php
    }
    osc_add_hook('admin_header','customHead');
   
    $iDisplayLength = __get('iDisplayLength');
    $aData          = __get('aPlugins'); 
?>
<?php osc_current_admin_theme_path( 'parts/header.php' ) ; ?>
<div id="tabs" class="ui-osc-tabs ui-tabs-right">
    <ul>
        <li><a href="#market"><?php _e('Market'); ?></a></li>
        <li><a href="#upload-plugins"><?php _e('Upload plugin') ; ?></a></li>
    </ul>
    <div id="upload-plugins">
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('Name') ; ?></th>
                    <th colspan=""><?php _e('Description') ; ?></th>
                    <th> &nbsp; </th>
                    <th> &nbsp; </th>
                    <th> &nbsp; </th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($aData['aaData'])>0) : ?>
            <?php foreach( $aData['aaData'] as $array) : ?>
                <tr>
                <?php foreach($array as $key => $value) : ?>
                    <td>
                    <?php echo $value; ?>
                    </td>
                <?php endforeach; ?>
                </tr>
            <?php endforeach;?>
            <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">
                <p><?php _e('No data available in table') ; ?></p>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>

       <?php 
            osc_show_pagination_admin($aData);
        ?>
    </div>
    
        
        
    <div id="market">
        <h2 class="render-title"><?php _e('Latest plugins on market') ; ?></h2>
        <div id="market_plugins" class="available-theme">
        </div>
    </div>

    <div id="market_installer" class="has-form-actions hide">
        <form action="" method="post">
            <input type="hidden" name="market_code" id="market_code" value="" />
            <div class="osc-modal-content-market">
                <img src="" id="market_thumb" class="float-left"/>
                <table class="table" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr class="table-first-row">
                            <td><?php _e('Name') ; ?></td>
                            <td><span id="market_name"><?php _e("Loading data"); ?></span></td>
                        </tr>
                        <tr class="even">
                            <td><?php _e('Version') ; ?></td>
                            <td><span id="market_version"><?php _e("Loading data"); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Author') ; ?></td>
                            <td><span id="market_author"><?php _e("Loading data"); ?></span></td>
                        </tr>
                        <tr class="even">
                            <td><?php _e('URL') ; ?></td>
                            <td><a id="market_url" href="#"><?php _e("Download manually"); ?></span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
            <div class="form-actions">
                <div class="wrapper">
                    <button id="market_cancel" class="btn btn-red" ><?php echo osc_esc_html( __('Cancel') ) ; ?></button>
                    <button id="market_install" class="btn btn-submit" ><?php echo osc_esc_html( __('Continue install') ) ; ?></button>
                </div>
            </div>
        </form>
    </div>        

</div>
        
        
        
<script>
    $(function() {
        $( "#tabs" ).tabs({ selected: 1 });

        $("#market_cancel").on("click", function(){
            $(".ui-dialog-content").dialog("close");
            return false;
        });

        $("#market_install").on("click", function(){
            $(".ui-dialog-content").dialog("close");
            //$(".ui-dialog-content").dialog({title:'Downloading...'}).html('Please wait until the download is completed');
            $('<div id="downloading"><div class="osc-modal-content">Please wait until the download is completed</div></div>').dialog({title:'Installing...',modal:true});
            $.getJSON(
            "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=market",
            {"code" : $("#market_code").attr("value")},
            function(data){
                $("#downloading .osc-modal-content").html(data.message);
                setTimeout(function(){
                  $(".ui-dialog-content").dialog("close");  
              },1000);
            });
            return false;
        });

        $.getJSON(
            "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=local_market",
            {"section" : "plugins"},
            function(data){
                $("#market_plugins").html(" ");
                if(data!=null && data.plugins!=null) {
                    for(var i=0;i<data.plugins.length;i++) {
                        var description = $(data.plugins[i].s_description).text();
                        dots = '';
                        if(description.length > 80){
                            dots = '...';
                        }
                        var imgsrc = '<?php echo osc_current_admin_theme("img/marketblank.jpg"); ?>';
                        if(data.plugins[i].s_image!=null) {
                            imgsrc = data.plugins[i].s_image;
                        }
                        $("#market_plugins").append('<div class="theme">'
                            +'<div class="plugin-stage">'
                                +'<img src="'+imgsrc+'" title="'+data.plugins[i].s_title+'" alt="'+data.plugins[i].s_title+'" />'
                                +'<div class="plugin-actions">'
                                    +'<a href="#'+data.plugins[i].s_slug+'" class="btn btn-mini btn-green market-popup"><?php _e('Install') ; ?></a>'
                                +'</div>'
                            +'</div>'
                            +'<div class="plugin-info">'
                                +'<h3>'+data.plugins[i].s_title+' '+data.plugins[i].s_version+' <?php _e('by') ; ?> <a target="_blank" href="">'+data.plugins[i].s_contact_name+'</a></h3>'
                            +'</div>'
                            +'<div class="plugin-description">'
                                +description.substring(0,80)+dots
                            +'</div>'
                        +'</div>');
                    }
                }
                $("#market_plugins").append('<div class="clear"></div>');
            }
        );

    });
    $('.market-popup').live('click',function(){
        $.getJSON(
            "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=check_market",
            {"code" : $(this).attr('href').replace('#','')},
            function(data){
                if(data!=null) {
                    $("#market_thumb").attr('src',data.s_thumbnail);
                    $("#market_code").attr("value", data.s_slug);
                    $("#market_name").html(data.s_title);
                    $("#market_version").html(data.s_version);
                    $("#market_author").html(data.s_contact_name);
                    $("#market_url").attr('href',data.s_source_file);

                    $('#market_installer').dialog({
                        modal:true,
                        title: '<?php echo osc_esc_js( __('OSClass Market') ) ; ?>',
                        class: 'osc-class-test',
                        width:485
                    });
                }
            }
        );

        return false;
    });        
</script>
        
<?php osc_current_admin_theme_path( 'parts/footer.php' ) ; ?>