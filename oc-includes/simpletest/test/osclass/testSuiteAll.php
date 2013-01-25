<?php
require_once(dirname(__FILE__).'/../../test_case.php');
class AllTests extends TestSuite {
    function AllTests() {
        global $test_str;
        global $php_sapi;
        $this->TestSuite('All tests');
        $tests = array();

        if(PHP_SAPI==='cli') {
            $php_sapi = 'cli';
            foreach($_SERVER['argv'] as $k => $v) {
                $tmp_arg = explode("=", $v);
                $k = str_replace("--", "", $tmp_arg[0]);
                if(count($tmp_arg)>1) {
                    $v = $tmp_arg[1];
                    if($k=='installer' || $k=='frontend' || $k=='admin') {
                        if($v=='' || $v==null) {
                            $tests[$k] = '';
                        } else {
                            $tmp = explode(",", $v);
                            foreach ($tmp as $t) {
                                $tests[$k][$t] = 1;
                            }
                        }
                    }
                } else {
                    $tests[$k] = '';
                }
            }
        } else {
            $php_sapi = 'web';
            foreach($_REQUEST as $k => $v) {
                if($k=='installer' || $k=='frontend' || $k=='admin') {
                    if($v=='' || $v==null) {
                        $tests[$k] = '';
                    } else {
                        $tmp = explode(",", $v);
                        foreach ($tmp as $t) {
                            $tests[$k][$t] = 1;
                        }
                    }
                }
            }
        }
        
        if(empty($tests)) {
            $tests['installer'] = '';
            $tests['frontend'] = '';
            $tests['admin'] = '';
        }

        $test_str = '';
        
        foreach($tests as $k => $v) {
            if($k=="installer" || $k=="frontend" || $k=="admin") {
                $test_str .= $k." {".(is_array($v)?implode(",", array_keys($v)):'all')."}    ";
            }
        }
        
        // INSTALLER
        if(isset($tests['installer'])) {
            $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Installer-installer.php');
        }


        // FRONTEND
        if(isset($tests['frontend'])) {
            require_once(dirname(__FILE__).'/../../../../oc-load.php');
            
            if(isset($tests['frontend']['contact']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-contactForm.php');
            }
            if(isset($tests['frontend']['login']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-login.php');
            }
            if(isset($tests['frontend']['register']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-register.php');
            }
            if(isset($tests['frontend']['search']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-search.php');
            }
            if(isset($tests['frontend']['users']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-users.php');
            }
            if(isset($tests['frontend']['items']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-items.php');
            }
            if(isset($tests['frontend']['page']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-page.php');      
            }
        }

        // FRONTEND WITH PERMALINKS
        if(isset($tests['frontend'])) {
            require_once(dirname(__FILE__).'/../../../../oc-load.php');
            
            // activate permalinks
            $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-onPermalinks.php');
            
            if(isset($tests['frontend']['contact']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-contactForm.php');
            }
            if(isset($tests['frontend']['login']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-login.php');
            }
            if(isset($tests['frontend']['register']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-register.php');
            }
            if(isset($tests['frontend']['search']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-search.php');
            }
            if(isset($tests['frontend']['users']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-users.php');
            }
            if(isset($tests['frontend']['items']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-items.php');
            }
            if(isset($tests['frontend']['page']) || $tests['frontend']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-page.php');      
            }
            // deactivate permalinks
            $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/Frontend-offPermalinks.php');
        }
        
        // ADMIN
        if(isset($tests['admin'])) {
            require_once(dirname(__FILE__).'/../../../../oc-load.php');
            
            if(isset($tests['admin']['categories']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-categories.php');    // OK
            }
            if(isset($tests['admin']['settings']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-generalSettings.php');    // OK
            }
            if(isset($tests['admin']['administrators']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-administrators.php');    // NEED DOC     
            }
            if(isset($tests['admin']['emailandalerts']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-emailsAndAlerts.php');    // NEED DOC
            }
            if(isset($tests['admin']['users']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-users.php');    // NEED DOC HAS BUGS
            }
            if(isset($tests['admin']['languages']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-languages.php'); // CURRENCY FORMAT HAS CHANGED, NEED TO UPDATE TEST, ALSO NEED TO UPDATE PACKAGE .ZIP
            }
            if(isset($tests['admin']['tools']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-tools.php');              // OK NEED TO TEST LOCATION STATS, MAINTENANCE AND UPGRADE?
            }
            if(isset($tests['admin']['pages']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-pages.php');              // OK MAYBE NEED TO TEST MULTI-LOCALE ...
            }
            if(isset($tests['admin']['plugins']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-plugins.php');            // OK - TAKE CARE OF FILES (oc-content/plugins should be writable)
            }
            if(isset($tests['admin']['appearance']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-appearance.php');         // OK
            }
            if(isset($tests['admin']['items']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-items.php');     // necesita limpiar código     
            }
            if(isset($tests['admin']['stats']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-stats.php');     // only test that the page load
            }
            if(isset($tests['admin']['moderator']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin_moderator.php');     // only test that the page load
            }
            if(isset($tests['admin']['reported']) || $tests['admin']=='') {
                $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/OCadmin-reported.php');     // only test that the page load
            }
        }


    }
}
?>
