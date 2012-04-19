<?php
require_once dirname(__FILE__).'/../../../../oc-load.php';

//require_once('Frontendtest.php');

class OCadmin_generalSettings extends OCadmintest {
    
    /*
     * Login into oc-admin.
     * GeneralSettings->Cron system.
     * - switch inputs.
     * Logout.
     */

    function testCrontab()
    {
        $uSettings = new utilSettings();
        
        $this->loginWith();
        $this->assertTrue(!$this->selenium->isTextPresent('Log in'), "Login oc-admin.");
        
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_general']");
        $this->selenium->waitForPageToLoad("10000");
        
        $cron = $uSettings->findValueByName('auto_cron');
        if($cron == 1){ $cron = 'on';} else { $cron = 'off'; }
        
        $this->assertEqual($cron, $this->selenium->getValue("auto_cron"), "Cron tab, check values/ preference values.");

        $this->selenium->click("auto_cron");
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");
        
        $cron = $uSettings->findValueByName('auto_cron');
        if($cron == 1){ $cron = 'on';} else { $cron = 'off'; }
        
        $this->assertEqual($cron, $this->selenium->getValue("auto_cron"), "Cron tab, check values/ preference values.");
        
        unset($uSettings);
    }
    
    /*
     * Login oc-admin
     * Update all inputs and check if change has been saved, update old configuration and check again.
     * Logout
     */
    function testMediatab()
    {
        $uSettings = new utilSettings();
        
        $this->loginWith();
        
        $maxSizeKb      = $uSettings->findValueByName('maxSizeKb');
        $allowedExt     = $uSettings->findValueByName('allowedExt');
        $dimThumbnail   = $uSettings->findValueByName('dimThumbnail');
        $dimPreview     = $uSettings->findValueByName('dimPreview');
        $dimNormal      = $uSettings->findValueByName('dimNormal');
        $keep_original_image   = $uSettings->findValueByName('keep_original_image');
        if($keep_original_image == 1){ $keep_original_image = 'on';} else { $keep_original_image = 'off'; }

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_media']");
        $this->selenium->waitForPageToLoad("10000");

        // change values to sometest-defined ones
        $this->selenium->type('maxSizeKb'   , 'ads');
        $this->selenium->type('dimThumbnail', 'bsg');
        $this->selenium->type('dimPreview'  , 'cylon');
        $this->selenium->type('dimNormal'   , 'adama');
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        $this->assertTrue($this->selenium->isTextPresent("Maximun size: this field has to be numeric only"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Thumbnail size: is not in the correct format"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Preview size: is not in the correct format"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Normal size: is not in the correct format"), "Media tab JS, update.");

        $this->selenium->type('maxSizeKb'   , '');
        $this->selenium->type('dimThumbnail', '');
        $this->selenium->type('dimPreview'  , '');
        $this->selenium->type('dimNormal'   , '');
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        $this->assertTrue($this->selenium->isTextPresent("Maximun size: this field is required"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Thumbnail size: this field is required"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Preview size: this field is required"), "Media tab JS, update.");
        $this->assertTrue($this->selenium->isTextPresent("Normal size: this field is required"), "Media tab JS, update.");
        
        $this->selenium->type('maxSizeKb'   , '500');
        $this->selenium->type('allowedExt'  , 'ext,deg,osc');
        $this->selenium->type('dimThumbnail', '10x10');
        $this->selenium->type('dimPreview'  , '50x50');
        $this->selenium->type('dimNormal'   , '100x100');
        $this->selenium->click('keep_original_image');

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Media config has been updated"), "Media tab, update.");

        $this->assertEqual( $this->selenium->getValue("maxSizeKb")      , '500', 'Media tab, check maxSizeKb');
        $this->assertEqual( $this->selenium->getValue('allowedExt')     , 'ext,deg,osc', 'Media tab, check allowedExt ext,deg,osc');
        $this->assertEqual( $this->selenium->getValue('dimThumbnail')   , '10x10', 'Media tab, check dimThumnai 10x10');
        $this->assertEqual( $this->selenium->getValue('dimPreview')     , '50x50' , 'Media tab, check dimPreview 50x50');
        $this->assertEqual( $this->selenium->getValue('dimNormal')      , '100x100', 'Media tab, check dimNormal 100x100');
        $this->assertEqual( $this->selenium->getValue('keep_original_image'), $keep_original_image=='off'?'on':'off', 'Media tab, check keep_original_image');

        $this->selenium->type('maxSizeKb'   , $maxSizeKb);
        $this->selenium->type('allowedExt'  , $allowedExt);
        $this->selenium->type('dimThumbnail', $dimThumbnail);
        $this->selenium->type('dimPreview'  , $dimPreview);
        $this->selenium->type('dimNormal'   , $dimNormal);
        $this->selenium->click('keep_original_image');

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Media config has been updated"), "Media tab, update.");

        $this->assertEqual( $this->selenium->getValue("maxSizeKb")      , $maxSizeKb);
        $this->assertEqual( $this->selenium->getValue('allowedExt')     , $allowedExt);
        $this->assertEqual( $this->selenium->getValue('dimThumbnail')   , $dimThumbnail);
        $this->assertEqual( $this->selenium->getValue('dimPreview')     , $dimPreview);
        $this->assertEqual( $this->selenium->getValue('dimNormal')      , $dimNormal);
        $this->assertEqual( $this->selenium->getValue('keep_original_image'), $keep_original_image);
    }
    
    /*
     * Login oc-admin
     * General Settings -> Mail server
     * update configuration and check and set old configuration again and check.
     * Logout.
     */
    function testMailServer()
    {
        $uSettings = new utilSettings();
        
        $pref = array();
        $pref['mailserver_type']        = $uSettings->findValueByName('mailserver_type');
        $pref['mailserver_host']        = $uSettings->findValueByName('mailserver_host');
        $pref['mailserver_port']        = $uSettings->findValueByName('mailserver_port');
        $pref['mailserver_username']    = $uSettings->findValueByName('mailserver_username');
        $pref['mailserver_password']    = $uSettings->findValueByName('mailserver_password');
        $pref['mailserver_ssl']         = $uSettings->findValueByName('mailserver_ssl');
        $pref['mailserver_auth']        = $uSettings->findValueByName('mailserver_auth');
        if($pref['mailserver_auth'] == 1){ $pref['mailserver_auth'] = 'on';} else { $pref['mailserver_auth'] = 'off'; }
        
        $this->loginWith();

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_mailserver']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type('mailserver_type'     , 'custom');
        $this->selenium->type('mailserver_host'     , 'mailserver.test.net');
        $this->selenium->type('mailserver_port'     , '1234');
        $this->selenium->type('mailserver_username' , 'test');
        $this->selenium->type('mailserver_password' , 'test');
        $this->selenium->type('mailserver_ssl'      , 'ssltest');
        $this->selenium->click('mailserver_auth');
        
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent( 'Mail server configuration has changed') , "Mail server configuration.");

        $this->assertEqual( $this->selenium->getValue("mailserver_type")     , 'custom');
        $this->assertEqual( $this->selenium->getValue('mailserver_host')     , 'mailserver.test.net');
        $this->assertEqual( $this->selenium->getValue('mailserver_port')     , '1234');
        $this->assertEqual( $this->selenium->getValue('mailserver_username') , 'test');
        $this->assertEqual( $this->selenium->getValue('mailserver_password') , 'test');
        $this->assertEqual( $this->selenium->getValue('mailserver_ssl')      , 'ssltest');
        $this->assertEqual( $this->selenium->getValue('mailserver_auth')     , 'on');

        $this->selenium->type('mailserver_type'     , $pref['mailserver_type']);
        $this->selenium->type('mailserver_host'     , $pref['mailserver_host']);
        $this->selenium->type('mailserver_port'     , $pref['mailserver_port']);
        $this->selenium->type('mailserver_username' , $pref['mailserver_username']);
        $this->selenium->type('mailserver_password' , $pref['mailserver_password']);
        $this->selenium->type('mailserver_ssl'      , $pref['mailserver_ssl']);
        $this->selenium->click('mailserver_auth');

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent( 'Mail server configuration has changed') , "Mail server configuration.");

        $this->assertEqual( $this->selenium->getValue("mailserver_type")     , $pref['mailserver_type']);
        $this->assertEqual( $this->selenium->getValue('mailserver_host')     , $pref['mailserver_host']);
        $this->assertEqual( $this->selenium->getValue('mailserver_port')     , $pref['mailserver_port']);
        $this->assertEqual( $this->selenium->getValue('mailserver_username') , $pref['mailserver_username']);
        $this->assertEqual( $this->selenium->getValue('mailserver_password') , $pref['mailserver_password']);
        $this->assertEqual( $this->selenium->getValue('mailserver_ssl')      , $pref['mailserver_ssl']);
        $this->assertEqual( $this->selenium->getValue('mailserver_auth')     , $pref['mailserver_auth']);
        
        unset($pref);
        unset($uSettings);
    }
    
    /*
     * Login oc-admin
     * General settings -> Spam and bots
     * Set akismet, recaptcha, check modifications
     * Logout
     */
    function testSpamAndBots()
    {
        $uSettings = new utilSettings();
        
        $pref = array();
        $pref['akismet_key']        = $uSettings->findValueByName('akismet_key');
        $pref['recaptchaPrivKey']   = $uSettings->findValueByName('recaptchaPrivKey');
        $pref['recaptchaPubKey']    = $uSettings->findValueByName('recaptchaPubKey');

        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_spambots']");
        $this->selenium->waitForPageToLoad("10000");
        
        // AKISMET

        $this->selenium->type('akismetKey'          , '9f18f856aa3c');
        $this->selenium->click("//input[@id='submit_akismet']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Your Akismet key has been updated") ,"Can't update the Akismet Key. ERROR");
        $this->assertEqual( $this->selenium->getValue('akismetKey')         , '9f18f856aa3c', 'Spam&Bots, akismet key');

        $this->selenium->type('akismetKey'          , $pref['akismet_key']);
        $this->selenium->click("//input[@id='submit_akismet']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Your Akismet key has been cleared") ,"Can't update the Akismet Key. ERROR");
        $this->assertEqual( $this->selenium->getValue('akismetKey')         , $pref['akismet_key'] , 'Spam&Bots, akismet key');
        
        // RECAPTCHA
        
        $this->selenium->type('recaptchaPrivKey'    , '1234567890');
        $this->selenium->type('recaptchaPubKey'     , '1234567890');
        $this->selenium->click("//input[@id='submit_recaptcha']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Your reCAPTCHA key has been updated") ,"Can't update the reCAPTCHA Key. ERROR");
        $this->assertEqual( $this->selenium->getValue('recaptchaPrivKey')   , '1234567890', 'Spam&Bots, recaptcha private key');
        $this->assertEqual( $this->selenium->getValue('recaptchaPubKey')    , '1234567890', 'Spam&Bots, recaptcha public key');

        $this->selenium->type('recaptchaPrivKey'    , $pref['recaptchaPrivKey']);
        $this->selenium->type('recaptchaPubKey'     , $pref['recaptchaPubKey']);
        $this->selenium->click("//input[@id='submit_recaptcha']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Your reCAPTCHA key has been cleared") ,"Can't update the reCAPTCHA Key. ERROR");
        $this->assertEqual( $this->selenium->getValue('recaptchaPrivKey')   , $pref['recaptchaPrivKey'] , 'Spam&Bots, recaptcha private key');
        $this->assertEqual( $this->selenium->getValue('recaptchaPubKey')    , $pref['recaptchaPubKey'] , 'Spam&Bots, recaptcha public key');
        
        unset($pref);
        unset($uSettings);
    }
    
    /*
     * Login oc-admin
     * GeneralSettings -> Comments
     * update settings, and check
     * Logout
     * 
     */
    function testComments()
    {
        $uSettings = new utilSettings();
        $pref = array();
        
        $pref['enabled_comments']   = $uSettings->findValueByName('enabled_comments') ;
        $pref['moderate_comments']  = $uSettings->findValueByName('moderate_comments') ;
        $pref['notify_new_comment'] = $uSettings->findValueByName('notify_new_comment') ;
        $pref['reg_user_post_comments'] = $uSettings->findValueByName('reg_user_post_comments') ;
        $pref['num_moderate_comments'] = $uSettings->findValueByName('moderate_comments');
        $pref['comments_per_page']     = $uSettings->findValueByName('comments_per_page');
        
        if($pref['enabled_comments'] == 1){ $pref['enabled_comments'] = 'on';} else { $pref['enabled_comments'] = 'off'; }
        if($pref['moderate_comments'] < 0){ $pref['moderate_comments'] = 'off';} else { $pref['moderate_comments'] = 'on'; }
        if($pref['notify_new_comment'] == 1){ $pref['notify_new_comment'] = 'on';} else { $pref['notify_new_comment'] = 'off'; }
        if($pref['reg_user_post_comments'] == 1){ $pref['reg_user_post_comments'] = 'on';} else { $pref['reg_user_post_comments'] = 'off'; }

        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_comments']");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->selenium->type("num_moderate_comments","wrong");
        $this->selenium->type("comments_per_page","test");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        
        $this->assertTrue( $this->selenium->isTextPresent("Moderated comments: this field has to be numeric only") , "Comments settings JS validator.");
        $this->assertTrue( $this->selenium->isTextPresent("Comments per page: this field has to be numeric only") , "Comments settings JS validator.");
        
        
        $this->selenium->click("enabled_comments");
        $this->selenium->click("reg_user_post_comments");
        if( !$pref['moderate_comments'] == 'on' ) {
            $this->selenium->click("moderate_comments");
        }
        $this->selenium->click("notify_new_comment");
        $this->selenium->type("num_moderate_comments",10);
        $this->selenium->type("comments_per_page",0);

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Comments' settings have been updated") , "Comments settings, check.");
        if( $pref['enabled_comments'] == 'on' ){
            $this->assertEqual( $this->selenium->getValue('enabled_comments'), 'off' , "Comments settings, check." ) ;
        } else {
            $this->assertEqual( $this->selenium->getValue('enabled_comments'), 'on' , "Comments settings, check." ) ;
        }

        if( $pref['reg_user_post_comments'] == 'on' ){
            $this->assertEqual( $this->selenium->getValue('reg_user_post_comments'), 'off' , "Comments settings, check." ) ;
        } else {
            $this->assertEqual( $this->selenium->getValue('reg_user_post_comments'), 'on' , "Comments settings, check." ) ;
        }
        
        if(! $pref['moderate_comments'] == 'on' ){
            $this->assertEqual( $this->selenium->getValue('moderate_comments'), 'on' , "Comments settings, check." ) ;
        }

        if( $pref['notify_new_comment'] == 'on' ){
            $this->assertEqual( $this->selenium->getValue('notify_new_comment'), 'off' , "Comments settings, check." ) ;
        } else {
            $this->assertEqual( $this->selenium->getValue('notify_new_comment'), 'on' , "Comments settings, check." ) ;
        }
        
        $this->assertTrue($this->selenium->getValue("num_moderate_comments") == 10 , "Comments settings, check. Not saved ok, num comments are 10." );
        $this->assertTrue($this->selenium->getValue("num_moderate_comments") == 10 , "Comments settings, check. Not saved ok, num comments are 10." );

        $this->selenium->click("enabled_comments");
        $this->selenium->click("reg_user_post_comments");
        $this->selenium->click("notify_new_comment");
        $this->selenium->type("num_moderate_comments",$pref['num_moderate_comments'] );
        $this->selenium->type("comments_per_page",$pref['comments_per_page'] );

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Comments' settings have been updated") , "Update comments settings. ERROR");
        
        $this->assertEqual( $this->selenium->getValue('enabled_comments')       ,  $pref['enabled_comments']         , "Comments settings, check.") ;
        $this->assertEqual( $this->selenium->getValue('reg_user_post_comments') ,  $pref['reg_user_post_comments']   , "Comments settings, check.") ;
        $this->assertEqual( $this->selenium->getValue('notify_new_comment')     ,  $pref['notify_new_comment']       , "Comments settings, check.") ;
        $this->assertEqual( $this->selenium->getValue('num_moderate_comments')  ,  $pref['num_moderate_comments']    , "Comments settings, check.") ;
        $this->assertEqual( $this->selenium->getValue('comments_per_page')      ,  $pref['comments_per_page']        , "Comments settings, check.") ;
        
        unset($pref);
        unset($uSettings);
    }
    
    private function getPreferencesGeneralSettings()
    {
        $uSettings = new utilSettings();
        $pref = array();
        $pref['pageTitle']      = Preference::newInstance()->findValueByName('pageTitle') ;
        $pref['contactEmail']   = Preference::newInstance()->findValueByName('contactEmail') ;
        $pref['df']             = Preference::newInstance()->findValueByName('dateFormat') ;
        $pref['pageDesc']       = Preference::newInstance()->findValueByName('pageDesc') ;
        $pref['language']       = Preference::newInstance()->findValueByName('language') ;
        $pref['currency']       = Preference::newInstance()->findValueByName('currency') ;
        $pref['weekStart']      = Preference::newInstance()->findValueByName('weekStart') ;
        $pref['num_rss_items']  = Preference::newInstance()->findValueByName('num_rss_items') ;
        $pref['tf']             = Preference::newInstance()->findValueByName('timeFormat') ;
        $pref['default_results_per_page']  = Preference::newInstance()->findValueByName('defaultResultsPerPage@search') ;
        $pref['max_latest_items_at_home']  = Preference::newInstance()->findValueByName('maxLatestItems@home') ;
        $pref['contact_attachment'] = Preference::newInstance()->findValueByName('contact_attachment') ;
        if($pref['contact_attachment'] == 1){ $pref['contact_attachment'] = 'on';} else { $pref['contact_attachment'] = 'off'; }
        unset($uSettings);  
        return $pref;
    }
    
    /*
     * Login oc-admin
     * GeneralSettings->GeneralSettings
     * update settings, and check
     * Logout
     */
    function testGeneralSettings()
    {
        $pref = $this->getPreferencesGeneralSettings();
        
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_general']");
        $this->selenium->waitForPageToLoad("10000");


        $this->selenium->type("pageTitle"   ,"");
        $this->selenium->type("contactEmail","");
        $this->selenium->type("num_rss_items" , "");
        $this->selenium->type("max_latest_items_at_home" , "");
        $this->selenium->type("default_results_per_page" , "");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        
        $this->assertTrue( $this->selenium->isTextPresent("Page title: this field is required") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("RSS shows: this field is required") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("The latest items shows: this field is required") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("The search page shows: this field is required") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("Email: this field is required") , 'JS Validation');

                
        $this->selenium->type("pageTitle"   ,"test title");
        $this->selenium->type("contactEmail","test email@.");
        $this->selenium->type("num_rss_items" , "a");
        $this->selenium->type("max_latest_items_at_home" , "b");
        $this->selenium->type("default_results_per_page" , "c");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        
        $this->assertTrue( $this->selenium->isTextPresent("RSS shows: this field has to be numeric only") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("The latest items shows: this field has to be numeric only") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("The search page shows: this field has to be numeric only") , 'JS Validation');
        $this->assertTrue( $this->selenium->isTextPresent("Invalid email address") , 'JS Validation');
        
        $this->selenium->type("pageTitle"   ,"New title web");
        $this->selenium->type("contactEmail","foo@bar.com");
        $this->selenium->type("pageDesc"    ,"Description web");
        $this->selenium->select("currency_admin", "label=EUR");
        $this->selenium->select("weekStart"     , "label=Saturday");
        $this->selenium->type("num_rss_items" , "61");
        $this->selenium->type("max_latest_items_at_home" , "21");
        $this->selenium->type("default_results_per_page" , "23");
        $this->selenium->click("m/d/Y");
        $this->selenium->click("H:i");
        $this->assertEqual( $this->selenium->getValue('enabled_attachment'), $pref['contact_attachment'] , 'Contact, check.') ;
        $this->selenium->click("enabled_attachment");
        
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertEqual( $this->selenium->getValue('pageTitle')     , "New title web" , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('contactEmail')  , "foo@bar.com"   , 'GeneralSettings, check.' ) ;
        $this->assertEqual( $this->selenium->getValue('dateFormat')    , "m/d/Y"         , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('pageDesc')      , "Description web"  , 'GeneralSettings, check.') ;
//        $this->assertEqual( $this->selenium->getValue('language')      , 'en_US' ) ;
        $this->assertEqual( $this->selenium->getValue('currency')      , 'EUR'          , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('weekStart')     , '6'            , 'GeneralSettings, INT.') ;
        $this->assertEqual( $this->selenium->getValue('num_rss_items') , '61'           , 'GeneralSettings, INT.') ;
        $this->assertEqual( $this->selenium->getValue('max_latest_items_at_home')       , '21'  , 'GeneralSettings, INT.' ) ;
        $this->assertEqual( $this->selenium->getValue('default_results_per_page')       , '23'  , 'GeneralSettings, INT.' ) ;
        $this->assertEqual( $this->selenium->getValue('timeFormat')    , "H:i"          , 'GeneralSettings, check.') ;

        if( $pref['contact_attachment'] == 'on' ) {
            $this->assertEqual( $this->selenium->getValue('enabled_attachment'), 'off', 'Contact, check.' ) ;
        } else {
            $this->assertEqual( $this->selenium->getValue('enabled_attachment'), 'on', 'Contact, check.' ) ;
        }

        $this->selenium->click("//a[@id='settings_general']");
        $this->selenium->waitForPageToLoad("10000");
        $this->selenium->type("pageTitle"   , $pref['pageTitle']);
        $this->selenium->type("contactEmail", $pref['contactEmail']);
        $this->selenium->type("pageDesc"    , $pref['pageDesc']);
        $this->selenium->select("currency_admin", "label=" . $pref['currency'] ) ;
        $this->selenium->select("weekStart"     , "value=" . $pref['weekStart'] ) ;
        $this->selenium->type("num_rss_items" , $pref['num_rss_items'] ) ;
        $this->selenium->type("max_latest_items_at_home" , $pref['max_latest_items_at_home'] ) ;
        $this->selenium->type("default_results_per_page" , $pref['default_results_per_page'] ) ;
        $this->selenium->click($pref['df']);
        $this->selenium->click($pref['tf']);
        $this->selenium->click("enabled_attachment");
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertEqual( $this->selenium->getValue('pageTitle')     , $pref['pageTitle']      , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('contactEmail')  , $pref['contactEmail']   , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('dateFormat')    , $pref['df']             , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('pageDesc')      , $pref['pageDesc']       , 'GeneralSettings, check.') ;
//        $this->assertEqual( $this->selenium->getValue('language')      , $pref['language']       , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('currency')      , $pref['currency']       , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('weekStart')     , $pref['weekStart']      , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('num_rss_items') , $pref['num_rss_items']  , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('default_results_per_page') , $pref['default_results_per_page']  , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('timeFormat')    , $pref['tf']             , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('max_latest_items_at_home') , $pref['max_latest_items_at_home']  , 'GeneralSettings, check.') ;
        $this->assertEqual( $this->selenium->getValue('enabled_attachment'), $pref['contact_attachment'], 'Contact, check.' ) ;
    }

    /*
     * Login oc-admin
     * GeneralSettings -> locations 
     * Add & edit & delete locations 
     * Logout
     */
    function testLocationsGEO()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("xpath=//a[@id='b_new_country']");

        $this->selenium->type("country"     , "Andorra" ) ;
        $this->selenium->type("c_country"   , "AN" ) ;
        $this->selenium->type('c_manual'    , '0') ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new country") , "Can't add new country" );

        // edit country
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div[1]/div/a[@class='edit']");
        $this->selenium->type("e_country"     , "Andorra_" ) ;

        $this->selenium->click("xpath=//button/span[text()='Edit']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been edited") , "Can't edit country name" );
        
        // delete country
        $this->selenium->click("xpath=//a[@id='country_delete']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been deleted") , "Can't delete Country" ) ;

    }



    /*
     * Login oc-admin
     * GeneralSettings -> locations 
     * add country/region/city twice
     * edit country/region/citytest location already exist
     * Logout
     */
    function testLocationsNEWForceError()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->waitForPageToLoad("4000");
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add Country
        $this->selenium->click("xpath=//a[@id='b_new_country']");

        $this->selenium->type("country", "ikea") ;
        $this->selenium->type("c_country", "IK") ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new country") , "Add new country" ) ;

        // add country again

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add Country
        $this->selenium->click("xpath=//a[@id='b_new_country']");

        $this->selenium->type("country", "ikea") ;
        $this->selenium->type("c_country", "IK") ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:already was in the database") , "Add country twice" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add Region
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_region']") ;

        $this->selenium->type("region", "Republica") ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000") ;

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new region") , "Add new region" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add Region again
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_region']") ;

        $this->selenium->type("region", "Republica") ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000") ;

        $this->assertTrue( $this->selenium->isTextPresent("regexp:already was in the database") , "Add region twice" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add City
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_regions']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_city']") ;

        $this->selenium->type("city", "Mi casa") ;
        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new city") , "Add new city" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add City again
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_regions']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_city']") ;

        $this->selenium->type("city", "Mi casa") ;
        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:already was in the database") , "Add city twice" ) ;

        //test errors when edit countries, regions, cities
        
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add another City
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_regions']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_city']") ;

        $this->selenium->type("city", "Mi casa_") ;
        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new city") , "Add new city" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // edit the city and change the name to existing one
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_regions']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_cities']/div/div/a[text()='Mi casa_']") ; 

        $this->selenium->type("e_city", "Mi casa") ;
        $this->selenium->click("xpath=//button/span[text()='Edit']") ;
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertTrue( $this->selenium->isTextPresent("regexp:already was in the database") , "Change city name to existing one" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // add another Region
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//a[@id='b_new_region']") ;

        $this->selenium->type("region", "Republica_") ;

        $this->selenium->click("xpath=//button/span[text()='Add']") ;
        $this->selenium->waitForPageToLoad("10000") ;

        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been added as a new region") , "Add new region" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // edit the region and change the name to existing one
        $this->selenium->click("xpath=//div[@id='l_countries']/div[1]/div/a[text()='View more »']") ;
        $this->selenium->click("xpath=//div[@id='i_regions']/div/div/a[text()='Republica_']") ; 
        $this->selenium->type("e_region", "Republica") ;

        $this->selenium->click("xpath=//button/span[text()='Edit']") ;
        $this->selenium->waitForPageToLoad("10000") ;

        $this->assertTrue( $this->selenium->isTextPresent("regexp:already was in the database") , "Change region name to existing one" ) ;

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_locations']");
        $this->selenium->waitForPageToLoad("10000");
        // DELETE THE LOCATION
        $this->selenium->click("xpath=//a[@id='country_delete'][1]");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue( $this->selenium->isTextPresent("regexp:has been deleted") , "Delete Country" ) ;

    }
        
    /*
     * Login oc-admin
     * add new currency
     * edit & delete the currency
     * Logout
     */
    function testCurrency()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("link=Add");

        $this->selenium->waitForPageToLoad("30000");

        $this->selenium->type("pk_c_code", "");
        $this->selenium->type("s_name", "");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        $this->assertTrue( $this->selenium->isTextPresent("Currency code: this field is required") , "Add currency" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Name: this field is required") , "Add currency" ) ;
        
        $this->selenium->type("pk_c_code", "INR");
        $this->selenium->type("s_name", "Indian Rupee");
        $this->selenium->type("s_description", "Indian Rupee र");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Currency added") , "Add currency" ) ;

        // edit
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("//table/tbody/tr[contains(.,'INR')]/td/small/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name", "");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        $this->assertTrue( $this->selenium->isTextPresent("Name: this field is required") , "Add currency" ) ;
        

        $this->selenium->type("s_name", "Indian_Rupee");
        $this->selenium->type("s_description", "Indian_Rupee र");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Currency updated") , "Edit currency" ) ;
        // delete
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("//table/tbody/tr[contains(.,'INR')]/td/small/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("One currency has been deleted") , "Delete currency" ) ;
        $this->assertTrue( !$this->selenium->isTextPresent("Indian_Rupee") , "Delete currency" ) ;
    }

    /*
     * Login oc-admin
     * Add new currency twice
     * Delete 
     * Logout
     */
    function testAddCurrencyTwice()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("link=Add");

        $this->selenium->waitForPageToLoad("30000");

        $this->selenium->type("pk_c_code", "INR");
        $this->selenium->type("s_name", "Indian Rupee");
        $this->selenium->type("s_description", "Indian Rupee र");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Currency added") , "Add currency" ) ;

        // add the same currency again
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->waitForPageToLoad("4000");
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("link=Add");

        $this->selenium->waitForPageToLoad("30000");

        $this->selenium->type("pk_c_code", "INR");
        $this->selenium->type("s_name", "Indian Rupee");
        $this->selenium->type("s_description", "Indian Rupee र");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Currency couldn't be added") , "Add currency twice. ERROR" ) ;

         // delete
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_currencies']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("//table/tbody/tr[contains(.,'INR')]/td/small/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("One currency has been deleted") , "Delete currency" ) ;
        $this->assertTrue( !$this->selenium->isTextPresent("Indian_Rupee") , "Delete currency" ) ;
    }
    
    function testPermalinks()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("//a[@id='settings_permalinks']");
        $this->selenium->waitForPageToLoad("10000");
        $value = $this->selenium->getValue('rewrite_enabled');
        
        // If they were off, enable it
        if($value=='off') {
            $this->selenium->click("rewrite_enabled");
            $this->selenium->click("//input[@type='submit']");
            $this->selenium->waitForPageToLoad("10000");
            $this->assertTrue( $this->selenium->isTextPresent("Permalinks structure updated") , "Disable permalinks" ) ;
        }
        
        $prefs = Preference::newInstance()->toArray();
        $this->selenium->type("rewrite_item_url", "");
        $this->selenium->type("rewrite_page_url", "");
        $this->selenium->type("rewrite_cat_url", "");
        $this->selenium->type("rewrite_search_url", "");
        $this->selenium->type("rewrite_search_country", "");
        $this->selenium->type("rewrite_search_region", "");
        $this->selenium->type("rewrite_search_city", "");
        $this->selenium->type("rewrite_search_city_area", "");
        $this->selenium->type("rewrite_search_pattern", "");
        $this->selenium->type("rewrite_search_category", "");
        $this->selenium->type("rewrite_search_user", "");
        $this->selenium->type("rewrite_contact", "");
        $this->selenium->type("rewrite_feed", "");
        $this->selenium->type("rewrite_language", "");
        $this->selenium->type("rewrite_item_mark", "");
        $this->selenium->type("rewrite_item_send_friend", "");
        $this->selenium->type("rewrite_item_contact", "");
        $this->selenium->type("rewrite_item_activate", "");
        $this->selenium->type("rewrite_item_edit", "");
        $this->selenium->type("rewrite_item_delete", "");
        $this->selenium->type("rewrite_item_resource_delete", "");
        $this->selenium->type("rewrite_user_login", "");
        $this->selenium->type("rewrite_user_dashboard", "");
        $this->selenium->type("rewrite_user_logout", "");
        $this->selenium->type("rewrite_user_register", "");
        $this->selenium->type("rewrite_user_activate", "");
        $this->selenium->type("rewrite_user_activate_alert", "");
        $this->selenium->type("rewrite_user_profile", "");
        $this->selenium->type("rewrite_user_items", "");
        $this->selenium->type("rewrite_user_alerts", "");
        $this->selenium->type("rewrite_user_recover", "");
        $this->selenium->type("rewrite_user_change_password", "");
        $this->selenium->type("rewrite_user_change_email", "");
        $this->selenium->type("rewrite_user_change_email_confirm", "");
        $this->selenium->click("//input[@type='submit']");
        sleep(4);
        
        
        $this->assertTrue( $this->selenium->isTextPresent("Item url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Page url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Categories url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search country: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search region: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search city: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search city area: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search category: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search pattern: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Search user: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Contact url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Feed url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Language url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Item mark url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Item send friend url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Item contact url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Activate item url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Edit item url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Delete item url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Delete item resource url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Login url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("User dashboard url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Logout url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("User register url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Activate user url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Activate alert url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("User profile url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("User items url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("User alerts url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Recover user url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Change password url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Change email url: this field is required") , "Empty permalink" ) ;
        $this->assertTrue( $this->selenium->isTextPresent("Change email confirm url: this field is required") , "Empty permalink" ) ;

        
        $this->selenium->type("rewrite_item_url", "item/{ITEM_ID}/{ITEM_TITLE}");
        $this->selenium->type("rewrite_page_url", "page/{PAGE_SLUG}");
        $this->selenium->type("rewrite_cat_url", "{CATEGORIES}");
        $this->selenium->type("rewrite_search_url", "search");
        $this->selenium->type("rewrite_search_country", "country");
        $this->selenium->type("rewrite_search_region", "region");
        $this->selenium->type("rewrite_search_city", "city");
        $this->selenium->type("rewrite_search_city_area", "cityarea");
        $this->selenium->type("rewrite_search_pattern", "pattern");
        $this->selenium->type("rewrite_search_category", "category");
        $this->selenium->type("rewrite_search_user", "user");
        $this->selenium->type("rewrite_contact", "contact");
        $this->selenium->type("rewrite_feed", "feed");
        $this->selenium->type("rewrite_language", "language");
        $this->selenium->type("rewrite_item_mark", "item/mark");
        $this->selenium->type("rewrite_item_send_friend", "item/send/friend");
        $this->selenium->type("rewrite_item_contact", "item/contact");
        $this->selenium->type("rewrite_item_activate", "item/new");
        $this->selenium->type("rewrite_item_edit", "item/edit");
        $this->selenium->type("rewrite_item_delete", "item/delete");
        $this->selenium->type("rewrite_item_resource_delete", "item/resource/delete");
        $this->selenium->type("rewrite_user_login", "user/login");
        $this->selenium->type("rewrite_user_dashboard", "user/dashboard");
        $this->selenium->type("rewrite_user_logout", "user/logout");
        $this->selenium->type("rewrite_user_register", "user/register");
        $this->selenium->type("rewrite_user_activate", "user/activate");
        $this->selenium->type("rewrite_user_activate_alert", "user/activate/alert");
        $this->selenium->type("rewrite_user_profile", "user/profile");
        $this->selenium->type("rewrite_user_items", "user/items");
        $this->selenium->type("rewrite_user_alerts", "user/alerts");
        $this->selenium->type("rewrite_user_recover", "user/recover");
        $this->selenium->type("rewrite_user_forgot", "user/forgot");
        $this->selenium->type("rewrite_user_change_password", "user/change/pasword");
        $this->selenium->type("rewrite_user_change_email", "user/change/email");
        $this->selenium->type("rewrite_user_change_email_confirm", "user/change/email/confirm");
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue( $this->selenium->isTextPresent("Permalinks structure updated") , "Disable permalinks" ) ;
        
        // Disable at the end of the tests
        $this->selenium->click("rewrite_enabled");
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue( $this->selenium->isTextPresent("Friendly urls successfully deactivated") , "Disable permalinks" ) ;
        
        // return to previous state (before starting the tests)
        if($value=='on') {
            $this->selenium->click("rewrite_enabled");
            $this->selenium->click("//input[@type='submit']");
            $this->selenium->waitForPageToLoad("10000");
            $this->assertTrue( $this->selenium->isTextPresent("Permalinks structure updated") , "Disable permalinks" ) ;
        }   
    }
    
}
?>
