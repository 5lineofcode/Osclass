<?php
require_once('../../autorun.php');
require_once('../../web_tester.php');
require_once('../../reporter.php');

// LOAD OSCLASS
require_once '../../../../oc-load.php';
require_once LIB_PATH . 'Selenium.php';

class TestOfAdminTools extends WebTestCase {

    private $selenium;

    function setUp()
    {
        $conn = getConnection();
        $conn->osc_dbExec(sprintf("INSERT INTO `%st_admin` (`s_name` ,`s_username` ,`s_password` ,`s_secret` ,`s_email`) VALUES ('Test Admin','testadmin','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','mvqdnrpt','testadmin@test.net')", DB_TABLE_PREFIX));

        echo "<br><div style='background-color: Wheat; color: black;'>init test</div>";

        $this->selenium = new Testing_Selenium("*firefox", "http://localhost/");
        $this->selenium->start();
        $this->selenium->setSpeed("150");
    }

    function tearDown()
    {
        $this->selenium->stop();
        $admin = Admin::newInstance()->findByEmail('testadmin@test.net');
        Admin::newInstance()->delete(array('pk_i_id' =>$admin['pk_i_id']));
        echo "<div style='background-color: Wheat; color: black;'>end test</div>";
        flush();
    }
    /*           TESTS          */

    /**
     * switch ($status) {
                        case(0):   $msg = _m('The theme folder is not writable');
                        break;
                        case(1):   $msg = _m('The theme has been installed correctly');
                        break;
                        case(2):   $msg = _m('The zip file is not valid');
                        break;
                        case(3):   $msg = _m('The zip file is empty');
                        break;
                        case(-1):
                        default:   $msg = _m('There was a problem adding the theme');
                        break;
                    }
     */
    function testAddTheme()
    {
        echo "<div style='background-color: green; color: white;'><h2>testAddTheme</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testAddTheme - LOGIN </div>";
        flush();
        $this->loginCorrect();
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testAddTheme - ADD THEME</div>";

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Appearance");
        $this->selenium->click("link=» Add a new theme");
        $this->selenium->waitForPageToLoad("10000");

        if($this->selenium->isTextPresent("chmod a+w ") ){
            $this->assertTrue(FALSE, "You need give permissions to the folder");
        } else {
            $this->selenium->type("package", LIB_PATH."simpletest/test/osclass/newcorp.zip");
            $this->selenium->click("button_save");
            $this->selenium->waitForPageToLoad("30000");

            $this->assertTrue($this->selenium->isTextPresent("The theme has been installed correctly"), "Can't upload themes");
        }
    }

    function testActivateTheme()
    {
        echo "<div style='background-color: green; color: white;'><h2>testActivateTheme</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testActivateTheme - LOGIN </div>";
        $this->loginCorrect();
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testActivateTheme - ACTIVATE THEME</div>";
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Appearance");
        $this->selenium->click("link=» Manage themes");
        $this->selenium->waitForPageToLoad("10000");

        if($this->selenium->isTextPresent("chmod a+w ") ){
            $this->assertTrue(FALSE, "You need give permissions to the folder");
        } else {

            $this->selenium->click("link=Activate");
            $this->selenium->waitForPageToLoad("30000");

            $text_element = $this->selenium->getText("xpath=//div[@id='current_theme_info']" );
            if(preg_match('/NewCorp Theme/', $text_element) ) {
                $this->assertTrue(TRUE);
            } else {
                $this->assertTrue(FALSE, "Can't activate the theme");
            }
        }
    }

    function testWidgets()
    {
        echo "<div style='background-color: green; color: white;'><h2>testWidgets</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testWidgets - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testWidgets - WIDGETS</div>";
        $this->widgets() ;
        flush();
    }

    function testDeleteTheme()
    {
        echo "<div style='background-color: green; color: white;'><h2>testDeleteTheme</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testDeleteTheme - YOU NEED TO DELETE THE PLUGIN DIRECTORY MANUALY ( ../themes/newcorp/)</div>";
    }

    /*      PRIVATE FUNCTIONS       */
    private function loginCorrect()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->waitForPageToLoad(10000);

        // if you are logged fo log out
        if( $this->selenium->isTextPresent('Log Out') ){
            $this->selenium->click('Log Out');
            $this->selenium->waitForPageToLoad(1000);
        }

        $this->selenium->type('user', 'testadmin');
        $this->selenium->type('password', 'password');
        $this->selenium->click('submit');
        $this->selenium->waitForPageToLoad(1000);

        if( !$this->selenium->isTextPresent('Log in') ){
            $this->assertTrue("todo bien");
        } else {
            $this->assertFalse("can't loggin");
        }
    }

//    private function widgets()
//    {
//        $this->selenium->open( osc_admin_base_url(true) );
//        $this->selenium->click("link=Appearance");
//        $this->selenium->click("link=» Manage themes");
//        $this->selenium->waitForPageToLoad("10000");
//
//        // add header widget
//        $this->selenium->click("xpath=//div[@id='settings_form']/div/div[1]/div/a");
//
//        // add categories widget
//        $this->selenium->click("xpath=//div[@id='settings_form']/div/div[1]/div/a");
//
//        // add footer widget
//        $this->selenium->click("xpath=//div[@id='settings_form']/div/div[1]/div/a");
//    }

}
?>
