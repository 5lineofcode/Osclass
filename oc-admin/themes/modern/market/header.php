<?php

    // check requirements
    if( !is_writable( ABS_PATH . 'oc-content/downloads/' ) ) {
        osc_add_flash_error_message( sprintf(_m('<code>downloads</code> folder has to be writable, i.e.: <code>chmod a+w %soc-content/downloads/</code>'), ABS_PATH), 'admin');
    }

    osc_enqueue_style('market', osc_current_admin_theme_styles_url('compile.css'));
    osc_register_script('market-js', osc_current_admin_theme_js_url('market.js'));
    osc_enqueue_script('market-js');

    osc_add_hook('admin_header','add_market_jsvariables');
    function add_market_jsvariables(){
        $marketPage = Params::getParam("mPage");
        $version_length = strlen(osc_version());
        $main_version = substr(osc_version(),0, $version_length-2).".".substr(osc_version(),$version_length-2, 1);


        if($marketPage>=1) $marketPage-- ;
        $action = Params::getParam("action");

        $js_lang = array(
                'by'                 => __('by'),
                'ok'                 => __('Ok'),
                'wait_download'      => __('Please wait until the download is completed'),
                'downloading'        => __('Downloading'),
                'close'              => __('Close'),
                'download'           => __('Download'),
                'update'             => __('Update'),
                'downloads'          => __('Downloads'),
                'requieres_version'  => __('Requires at least'),
                'compatible_with'    => __('Compatible up to'),
                'screenshots'        => __('Screenshots'),
                'download_manually'  => __('Download manually'),
                'proceed_anyway'     => sprintf(__('Warning! This package is not compatible with your current version of Osclass (%s)'), $main_version),
                'srue'               => __('Are you sure?'),
                'proceed_anyway_btn' => __('Ok, proceed anyway'),
                'not_compatible'     => sprintf(__('Warning! This theme is not compatible with your current version of Osclass (%s)'), $main_version),
                'themes' => array(
                                'download_ok' => __('The theme has been downloaded correctly, proceed to activate or preview it.')
                            ),
                'plugins' => array(
                                'download_ok' => __('The plugin has been downloaded correctly, proceed to install and configure.')
                            )

            );
        ?>
        <script type="text/javascript">
            var theme = window.theme || {};
            theme.adminBaseUrl = "<?php echo osc_admin_base_url(true); ?>";
            theme.themUrl = "<?php echo osc_current_admin_theme_url(); ?>";
            theme.langs = <?php echo json_encode($js_lang); ?>;

            var osc_market = {};
            osc_market.main_version = <?php echo $main_version; ?>;
        </script>
        <?php
        $sections = array();
        $featured = '';
        switch (Params::getParam("action")) {
            case 'plugins':
                $sections[] = 'plugins';
                break;

            case 'themes':
                $sections[] = 'themes';
                break;
            case 'languages':
                $sections[] = 'languages';
                break;

            default:
                $sections = array('plugins','themes','languages');
                $featured = 'true';
                break;
        }
        foreach($sections as $section){
            echo '<script src="'.osc_admin_base_url(true).'?page=ajax&amp;action=market_data&amp;section='.$section.'&amp;featured='.$featured.'&amp;mPage='.Params::getParam('mPage').'" type="text/javascript"></script>';
        }
        ?>
        <?php
    }

    function gradienColors(){
        $letters = str_split('abgi');
        shuffle($letters);
        return $letters;
    }

    function drawMarketItem($item,$color = false){
        //constants
        $updateClass      = '';
        $updateData       = '';
        $thumbnail        = false;
        $featuredClass    = '';
        $style            = '';
        $letterDraw       = '';
        $type             = strtolower($item['e_type']);
        $items_to_update  = json_decode(getPreference($type.'s_to_update'),true);


        if($item['s_thumbnail']){
            $thumbnail = $item['s_thumbnail'];
        }
        if($item['s_banner']){
            $thumbnail = 'http://market.osclass.org/oc-content/uploads/market/'.$item['s_banner'];
        }
        if ($item['b_featured']) {
            $featuredClass = ' is-featured';
        }
        if($type != 'language'){
            if (in_array($item['s_update_url'],$items_to_update)) {
                $updateClass = ' has-update';
                $updateData  = ' data-update="true"';
            }
        }
        if(!$thumbnail && $color){

            $thumbnail = osc_current_admin_theme_url('images/gr-'.$color.'.png');
            $letterDraw = $item['s_update_url'][0];
            if($type == 'language'){
                $letterDraw = $item['s_update_url'];
            }
        }
        $style = 'background-image:url('.$thumbnail.');';
        $item['total_downloads'] = 335;
        echo '<a href="#'.$item['s_update_url'].'" class="mk-item-parent'.$updateClass.$featuredClass.'" data-type="'.$type.'"'.$updateData.' data-gr="'.$color.'" data-letter="'.$item['s_update_url'][0].'">';
        echo '<div class="mk-item mk-item-'.$type.'">';
        echo '    <div class="banner" style="'.$style.'">'.$letterDraw.'</div>';
        echo '    <div class="mk-info"><i class="flag"></i>';
        echo '        <h3>'.$item['s_title'].'</h3>';
        echo '        <i>by '.$item['s_contact_name'].'</i>';
        echo '        <div>';
        echo '            <span class="more">'.__('View more').'</span>';
        echo '            <span class="downloads"><strong>'.$item['i_total_downloads'].'</strong>'.__('downloads').'</span>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
        echo '</a>';
    }
    if(!function_exists('addBodyClass')){
        function addBodyClass($array){
                   $array[] = 'market';
            return $array;
        }
    }
    osc_add_filter('admin_body_class','addBodyClass');


    function customPageHeader() {
        $action = Params::getParam("action"); ?>
        <h1><?php _e('Discover how to improve your Osclass!') ; ?></h1>
        <h2>Osclass offers many templates and plugins.<br/>Turn your Osclass installation into a classifieds site in a minute!</h2>
        <ul class="tabs">
            <li <?php if($action == ''){ echo 'class="active"';} ?>><a href="<?php echo osc_admin_base_url(true).'?page=market'; ?>"><?php _e('Market'); ?></a></li>
            <li <?php if($action == 'plugins'){ echo 'class="active"';} ?>><a href="<?php echo osc_admin_base_url(true).'?page=market&action=plugins'; ?>"><?php _e('Plugins'); ?></a></li>
            <li <?php if($action == 'themes'){ echo 'class="active"';} ?>><a href="<?php echo osc_admin_base_url(true).'?page=market&action=themes'; ?>"><?php _e('Themes'); ?></a></li>
            <li <?php if($action == 'languages'){ echo 'class="active"';} ?>><a href="<?php echo osc_admin_base_url(true).'?page=market&action=languages'; ?>"><?php _e('Languages'); ?></a></li>
        </ul>
<?php
    }
    osc_add_hook('admin_page_header','customPageHeader');

    function customPageTitle($string) {
        return __('Market');
    }
    osc_add_filter('admin_title', 'customPageTitle');
    osc_current_admin_theme_path( 'parts/header.php' ) ;
?>
