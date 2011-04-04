<?php
require_once('../../autorun.php');
require_once('../../web_tester.php');
require_once('../../reporter.php');

// LOAD OSCLASS
require_once '../../../../oc-load.php';
require_once LIB_PATH . 'Selenium.php';

class TestOfAdminItems extends WebTestCase {

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

    function testInsertItem()
    {
        echo "<div style='background-color: green; color: white;'><h2>testInsertItem</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testInsertItem - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testInsertItem - ADD ITEM</div>";
        $this->insertItem() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testInsertItem - NO MEDIA/ NO COMMENTS</div>";
        $this->viewMedia_NoMedia();
        $this->viewComments_NoComments();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testInsertItem - ACTIVATE/DEACTIVATE</div>";
        $this->deactivate();
        $this->activate();
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testInsertItem - MARK/UNMARK AS PREMIUM</div>";
        $this->markAsPremium();
        $this->unmarkAsPremium();
        flush();
    }

    function testEditItem()
    {
        echo "<div style='background-color: green; color: white;'><h2>testEditItem</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testEditItem - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testEditItem - EDIT ITEM</div>";
        $this->editItem() ;
        flush();
    }

    function testDeleteItem()
    {
        echo "<div style='background-color: green; color: white;'><h2>testDeleteItem</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testDeleteItem - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testDeleteItem - DELETE ITEM</div>";
        $this->deleteItem() ;
        flush();
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

    // todo test minim lenght title, description , contact email
    private function insertItem()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Add new item");
        $this->selenium->waitForPageToLoad("10000");

        // insert non registered user
        $this->selenium->type("contactName" , "contact name");
        $this->selenium->type("contactEmail", "test@mail.com");

        $this->selenium->select("catId", "label=Cars");
        $this->selenium->type("title[en_US]", "title item");
        $this->selenium->type("description[en_US]", "description test description test description test");
        $this->selenium->type("price", "11");
        $this->selenium->select("currency", "label=Euro €");
        $this->selenium->select("regionId", "label=A Coruña");
        $this->selenium->select("cityId", "label=A Capela");
        $this->selenium->type("address", "address item");
        
        $this->selenium->click("//button[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertTrue($this->selenium->isTextPresent("A new item has been added"), "Can't insert a new item. ERROR");
    }

    private function viewMedia_NoMedia()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='View media']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("No matching records found"), "Show media when there aren't. ERROR");
    }

    private function viewComments_NoComments()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='View comments']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("No matching records found"), "Show media when there aren't. ERROR");
    }

    private function deactivate()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='Deactivate']");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertTrue($this->selenium->isTextPresent("The item has been deactivated"), "Can't deactivate item. ERROR");
    }

    private function activate()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='Activate']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The item has been activated"), "Can't activate item. ERROR");
    }

    private function markAsPremium()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='Mark as premium']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Changes have been applied"), "Can't mark as premium item. ERROR");
    }
    
    private function unmarkAsPremium()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='Unmark as premium']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Changes have been applied"), "Can't mark as premium item. ERROR");
    }


    private function editItem()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        sleep(2); // time enough to load table data

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/span/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        // insert non registered user
        $this->selenium->type("contactName" , "contact name_");
        $this->selenium->type("contactEmail", "test_@mail.com");

        $this->selenium->select("catId", "label=Cars");
        $this->selenium->type("title[en_US]", "title_item");
        $this->selenium->type("description[en_US]", "description_test_description test description_test");
        $this->selenium->type("price", "11");
        $this->selenium->select("currency", "label=Euro €");
        $this->selenium->select("regionId", "label=A Coruña");
        $this->selenium->select("cityId", "label=A Capela");
        $this->selenium->type("address", "address_item");

        $this->selenium->click("//button[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Changes saved correctly"), "Can't edit item. ERROR");
    }

    private function deleteItem()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title_item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title_item')]/td/span/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The item has been deleted"), "Can't delete item. ERROR");
    }
}

?>
