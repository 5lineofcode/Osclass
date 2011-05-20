<?php

require_once('../../../../oc-load.php');
require_once('../../autorun.php');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests');
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_generalSettings.php');    // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_pages.php');              // OK
//        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_category.php');           // TODO 
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_users.php');              // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_language.php');           // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_emailsAndAlerts.php');    // Ok
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_administrators.php');     // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_tools.php');              // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_items.php');              // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_appearance.php');         // OK
        $this->addFile(ABS_PATH . 'oc-includes/simpletest/test/osclass/test_admin_plugins.php');            // OK
    }
}
?>
