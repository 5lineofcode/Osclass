<?php
require_once('../../autorun.php');
require_once('../../web_tester.php');
require_once('../../reporter.php');

// LOAD OSCLASS
require_once '../../../../oc-load.php';
require_once LIB_PATH . 'Selenium.php';

class TestOfAdminItems extends WebTestCase {

    private $selenium;
    private $email;
    private $password;

    function setUp()
    {
        $conn = getConnection();
        $conn->osc_dbExec(sprintf("INSERT INTO `%st_admin` (`s_name` ,`s_username` ,`s_password` ,`s_secret` ,`s_email`) VALUES ('Test Admin','testadmin','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','mvqdnrpt','testadmin@test.net')", DB_TABLE_PREFIX));

        echo "<br><div style='background-color: Wheat; color: black;'>init test</div>";

        $browser = "*firefox";
        $this->selenium = new Testing_Selenium($browser, "http://localhost/");
        $this->selenium->start();
        $this->selenium->setSpeed("300");
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

    function testComments()
    {
        echo "<div style='background-color: green; color: white;'><h2>testComments</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - INSERT ITEM AND COMMENTS TESTS</div>";
        $this->insertItemAndComments() ;
        flush();
    }

    function testMedia()
    {
        echo "<div style='background-color: green; color: white;'><h2>testMedia</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testMedia - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testMedia - MEDIA ITEM</div>";
        $this->insertItemAndMedia() ;
        flush();
    }

    function testSettings()
    {
        echo "<div style='background-color: green; color: white;'><h2>testSettings</h2></div>";
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testSettings - LOGIN </div>";
        $this->loginCorrect() ;
        flush();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testSettings - ITEMS SETTINGS</div>";
        $this->settings() ;
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

    private function addUserForTesting()
    {
        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);

        echo "insert new user for testing<br>";
        $input['s_secret']          = osc_genRandomPassword() ;
        $input['dt_reg_date']       = DB_FUNC_NOW ;
        $input['s_name']            = "Carlos";
        $input['s_website']         = "www.osclass.org";
        $input['s_phone_land']      = "931234567";
        $input['s_phone_mobile']    = "666121212";
        $input['fk_c_country_code'] = null ;
        $input['s_country']         = null ;
        $input['fk_i_region_id']    = null ;
        $input['s_region']          = "" ;
        $input['fk_i_city_id']      = null ;
        $input['s_city']            = "";
        $input['s_city_area']       = "";
        $input['s_address']         = "c:/address nº 10 2º2ª";
        $input['b_company']         = 0;
        $input['b_enabled']         = 1;
        $input['b_active']          = 1;
        $input['s_email']           = "carlos+user@osclass.org";
        $this->email                = "carlos+user@osclass.org";
        $input['s_password']        = sha1('carlos');
        $this->password             = "carlos";

        $this->array = $input;

        User::newInstance()->insert($input) ;
    }

    private function loginWebsite()
    {
        $this->selenium->open( osc_base_url(true) );
        $bool = $this->selenium->isElementPresent('login_open') ;
        if($bool){
            $this->selenium->click("login_open");
            $this->selenium->type("email"   , $this->email);
            $this->selenium->type("password", $this->password);

            $this->selenium->click("xpath=//button[@type='submit']");
            $this->selenium->waitForPageToLoad("30000");

            echo "<div style='background-color: green; color: white;padding-left:15px;'>Login user ...</div>";
            if($this->selenium->isTextPresent("User account manager")){
                $this->logged = 1;
                $this->assertTrue("ok");
                $this->assertTrue(true);
            }else {
                $this->assertTrue(false);
            }
        }
    }

    // todo test minim lenght title, description , contact email
    private function insertItem($bPhotos = FALSE )
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Add new item");
        $this->selenium->waitForPageToLoad("10000");

        // insert non registered user
        $this->selenium->type("contactName" , "contact name");
        $this->selenium->type("contactEmail", "test@mail.com");

        $this->selenium->select("catId", "label=regexp:\\s*Cars");
        $this->selenium->type("title[en_US]", "title item");
        $this->selenium->type("description[en_US]", "description test description test description test");
        $this->selenium->type("price", "11");
        $this->selenium->select("currency", "label=Euro €");

        $this->selenium->select("countryId", "label=Spain");

        $this->selenium->type('id=region', 'A Coruña');
        $this->selenium->click('id=ui-active-menuitem');

        $this->selenium->type('id=city', 'A Capela');
        $this->selenium->click('id=ui-active-menuitem');

        $this->selenium->type("address", "address item");

        if( $bPhotos ){
            $this->selenium->type("photos[]", LIB_PATH."simpletest/test/osclass/img_test1.gif");
            $this->selenium->click("link=Add new photo");
            $this->selenium->type("//div[@id='p-0']/div/input", LIB_PATH."simpletest/test/osclass/img_test2.gif");
        }
        
        sleep(4);
        
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='View media']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='View comments']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Deactivate']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Activate']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Mark as premium']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Unmark as premium']");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        // insert non registered user
        $this->selenium->type("contactName" , "contact name_");
        $this->selenium->type("contactEmail", "test_@mail.com");

        $this->selenium->select("catId", "label=regexp:\\s*Cars");
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
        $this->selenium->click("//table/tbody/tr[contains(.,'title_item')]/td/div/div/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The item has been deleted"), "Can't delete item. ERROR");
    }

    private function insertItemAndComments()
    {
        // insert item
        $this->insertItem() ;

        $mItem = new Item();
        $item = $mItem->findByConditions( array('s_contact_email' => 'test@mail.com') );
        
        // force moderation comments
        $enabled_comments = Preference::newInstance()->findValueByName('enabled_comments');
        if( $enabled_comments == 0 ) {
            Preference::newInstance()->update(array('s_value' => 1)
                                             ,array('s_name'  => 'enabled_comments'));
        }
        $moderate_comments = Preference::newInstance()->findValueByName('moderate_comments');
        if( $enabled_comments != 0 ) {
            Preference::newInstance()->update(array('s_value' => 0)
                                             ,array('s_name'  => 'moderate_comments'));
        }
        // insert comment from frontend
        echo "<".osc_item_url_ns( $item['pk_i_id'] )."><br>";

        $this->selenium->open(osc_item_url_ns( $item['pk_i_id'] ));

        $this->selenium->type("authorName"      , "carlos");
        $this->selenium->type("authorEmail"     , "carlos@osclass.org");
        $this->selenium->type("title"           , "I like it");
        $this->selenium->type("body"            , "Can you provide more info please :)");

        $this->selenium->click("//div[@id='comments']/form/fieldset/div/span/button");
        $this->selenium->waitForPageToLoad("30000");

        // test oc-admin
        $this->loginCorrect();

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Comments");
        $this->selenium->waitForPageToLoad("10000");

        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - ACTIVATE COMMENT</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'Can you provide more info please :)')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'Can you provide more info please :)')]/td/div/a[text()='Activate']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The comment has been approved"), "Can't activate comment. ERROR" );

        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - DEACTIVATE COMMENT</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'Can you provide more info please :)')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'Can you provide more info please :)')]/td/div/a[text()='Deactivate']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The comment has been disapproved"), "Can't deactivate comment. ERROR" );

        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - EDIT COMMENT</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'Can you provide more info please :)')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'Can you provide more info please :)')]/td/div/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        // edit comment
        $this->selenium->type("s_title", "I like it updated");
        $this->selenium->type("s_author_name", "carlos osclass");
        $this->selenium->type("s_body", "Can you provide more info please :) Regards");
        $this->selenium->click("//button[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("Great! We just updated your comment"), "Can't edit a comment. ERROR") ;

        echo "<div style='background-color: green; color: white;padding-left:15px;'>testComments - DELETE COMMENT</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'Can you provide more info please :)')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'Can you provide more info please :)')]/td/div/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertTrue($this->selenium->isTextPresent("The comment have been deleted"), "Can't delete a comment. ERROR") ;

        // DELETE ITEM
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The item has been deleted"), "Can't delete item. ERROR");

        // restore prefereces values
        Preference::newInstance()->update(array('s_value' => $enabled_comments)
                                         ,array('s_name'  => 'enabled_comments'));
        Preference::newInstance()->update(array('s_value' => $moderate_comments)
                                         ,array('s_name'  => 'moderate_comments'));
    }

    private function insertItemAndMedia()
    {
        // insert item
        $this->insertItem( TRUE ) ;

        $mItem = new Item();
        $item = $mItem->findByConditions( array('s_contact_email' => 'test@mail.com') );

        // test oc-admin
        $this->loginCorrect();

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage media");
        $this->selenium->waitForPageToLoad("10000");
        
        $this->assertTrue($this->selenium->isTextPresent("Showing 1 to 2 of 2 entries"), "Inconsistent . ERROR" );
        // only can delete resources
        echo "<div style='background-color: green; color: white;padding-left:15px;'>testMedia - MEDIA DELETE</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'image/jpeg')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'image/jpeg')]/td/div/span/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Resource deleted"), "Can't delete media. ERROR" );
        $this->assertTrue($this->selenium->isTextPresent("Showing 1 to 1 of 1 entries"), "Can't delete media. ERROR" );

        echo "<div style='background-color: green; color: white;padding-left:15px;'>testMedia - MEDIA DELETE</div>";
        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'image/jpeg')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'image/jpeg')]/td/div/span/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("Resource deleted"), "Can't delete media. ERROR" );
        $this->assertTrue($this->selenium->isTextPresent("Showing 0 to 0 of 0 entries"), "Can't delete media. ERROR" );

        // DELETE ITEM
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Items");
        $this->selenium->click("link=» Manage items");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'title item')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'title item')]/td/div/div/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The item has been deleted"), "Can't delete item. ERROR");
    }

    private function settings()
    {
// enabled_recaptcha_items
        echo "<div style='background-color: green; color: white;padding-left:15px;'>RECAPTCHA 1</div>";
        Preference::newInstance()->replace('enabled_recaptcha_items', 1,"osclass", 'BOOLEAN') ;
        $this->checkWebsite_recaptcha(1);
        echo "<div style='background-color: green; color: white;padding-left:15px;'>RECAPTCHA 0</div>";
        Preference::newInstance()->replace('enabled_recaptcha_items', 0,"osclass", 'BOOLEAN') ;
        $this->checkWebsite_recaptcha(0);
        flush();
// moderate_items
        // moderate only one item.
        Preference::newInstance()->replace('logged_user_item_validation', '0',"osclass", 'INTEGER') ;
        echo "<div style='background-color: green; color: white;padding-left:15px;'>MODERATE 1</div>";
        Preference::newInstance()->replace('moderate_items', '1',"osclass", 'INTEGER') ;
        $this->checkWebsite_moderate_items('1');
        // never moderate
        echo "<div style='background-color: green; color: white;padding-left:15px;'>MODERATE -1</div>";
        Preference::newInstance()->replace('moderate_items', '-1',"osclass", 'INTEGER') ;
        $this->checkWebsite_moderate_items('-1');
        // always moderate
        echo "<div style='background-color: green; color: white;padding-left:15px;'>MODERATE 0</div>";
        Preference::newInstance()->replace('moderate_items', '0',"osclass", 'INTEGER') ;
        $this->checkWebsite_moderate_items('0');
        flush();
// logged_user_item_validation
        echo "<div style='background-color: green; color: white;padding-left:15px;'>logged_user_item_validation 0 </div>";
        Preference::newInstance()->replace('logged_user_item_validation', '0',"osclass", 'INTEGER') ;
        $this->checkWebsite_logged_user_item_validation('0');
        echo "<div style='background-color: green; color: white;padding-left:15px;'>logged_user_item_validation 1 </div>";
        Preference::newInstance()->replace('logged_user_item_validation', '1',"osclass", 'INTEGER') ;
        $this->checkWebsite_logged_user_item_validation('1');
        flush();
// items_wait_time
        echo "<div style='background-color: green; color: white;padding-left:15px;'>items_wait_time 0s </div>";
        Preference::newInstance()->replace('items_wait_time', '0',"osclass", 'INTEGER') ;
        $this->checkWebsite_items_wait_time('0');
        $this->selenium->deleteAllVisibleCookies();
        echo "<div style='background-color: green; color: white;padding-left:15px;'>items_wait_time 5s </div>";
        Preference::newInstance()->replace('items_wait_time', '30',"osclass", 'INTEGER') ;
        $this->checkWebsite_items_wait_time('30');
        flush();
// reg_user_can_contact
        Preference::newInstance()->replace('items_wait_time', '0',"osclass", 'INTEGER') ;
        echo "<div style='background-color: green; color: white;padding-left:15px;'>reg_user_can_contact 0</div>";
        Preference::newInstance()->replace('reg_user_can_contact', '0',"osclass", 'BOOLEAN') ;
        $this->checkWebsite_reg_user_can_contact('0');
        echo "<div style='background-color: green; color: white;padding-left:15px;'>reg_user_can_contact 1</div>";
        Preference::newInstance()->replace('reg_user_can_contact', '1',"osclass", 'BOOLEAN') ;
        $this->checkWebsite_reg_user_can_contact('1');
        flush();
// enableField#f_price@items
        echo "<div style='background-color: green; color: white;padding-left:15px;'>enableField#f_price@items 0</div>";
        Preference::newInstance()->replace('enableField#f_price@items', '0',"osclass", 'BOOLEAN') ;
        $this->checkWebsite_enableField_f_price_items('0');
        echo "<div style='background-color: green; color: white;padding-left:15px;'>enableField#f_price@items 1</div>";
        usleep(25000);
        Preference::newInstance()->replace('enableField#f_price@items', '1',"osclass", 'BOOLEAN') ;
        $this->checkWebsite_enableField_f_price_items('1');
        flush();
// enableField#images@items  //  numImages@items
        echo "<div style='background-color: green; color: white;padding-left:15px;'>enableField#f_price@items = 0</div>";
        Preference::newInstance()->replace('enableField#images@items', '0',"osclass", 'BOOLEAN') ;
        $this->checkWebsite_enableField_images_items('0');
        echo "<div style='background-color: green; color: white;padding-left:15px;'>enableField#f_price@items = 1 & numImages@items = 1</div>";
        Preference::newInstance()->replace('enableField#images@items', '1',"osclass", 'BOOLEAN') ;
        Preference::newInstance()->replace('numImages@items', '1',"osclass", 'INTEGER') ;
        $this->checkWebsite_enableField_images_items('1','1');
        Preference::newInstance()->replace('numImages@items', '4',"osclass", 'INTEGER') ;
        flush();
    }

    private function post_item_website(){
        $this->selenium->open( osc_item_post_url() );
        $this->selenium->select("catId", "label=regexp:\\s*Animals");
        $this->selenium->type("id=title[en_US]", "foo title");
        $this->selenium->type("id=description[en_US]","description foo title");
        $this->selenium->select("countryId", "label=Spain");
        $this->selenium->select("regionId", "label=Albacete");
        $this->selenium->select("cityId", "label=Albacete");
        $this->selenium->type("cityArea", "my area");
        $this->selenium->type("address", "my address");

        $this->selenium->type('id=contactName' , 'foobar');
        $this->selenium->type('id=contactEmail', 'foobar@mail.com');

        $this->selenium->click("//button[text()='Publish']");
        $this->selenium->waitForPageToLoad("30000");
    }

    private function checkWebsite_recaptcha($bool)
    {
        // spam & boots -> fill  private & public keys
        $this->loginCorrect();
        $this->selenium->open( osc_admin_base_url(true) .'?page=settings&action=spamNbots' );
        $this->selenium->type('recaptchaPubKey', '6Lc5PsQSAAAAAEWQYBh5X7pepBL1FuYvdhEFTk0v') ;
        $this->selenium->type('recaptchaPrivKey' , '6Lc5PsQSAAAAADnbAmtxG_kfwIxPikL-mjSMyv22');
        $this->selenium->click("xpath=//input[@id='button_save']");
        $this->selenium->waitForPageToLoad("10000");

        // test website
        $this->selenium->open( osc_item_post_url() );
        $exist_recaptcha = $this->selenium->isElementPresent("xpath=//table[@id='recaptcha_table']");
        
        // recaptcha enabled
        if($bool){
            $this->assertTrue($exist_recaptcha, "Recaptcha is not present ! ERROR") ;
        // recaptcha disabled
        } else {
            $this->assertTrue(!$exist_recaptcha, "Recaptcha is present ! ERROR") ;
        }
    }

    private function checkWebsite_moderate_items($moderation, $user = 1)
    {
        // create user
        $this->addUserForTesting();
        // loginWebsite
        $this->loginWebsite();
        
        $this->post_item_website();
        if($moderation == -1) {
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Item need validation moderate_items = -1 (NEVER MODERATE). ERROR" );
        } else if($moderation == 0 || $moderation == 1) {
            $this->assertTrue($this->selenium->isTextPresent("Check your inbox to verify your email address"),"Need validation but message don't appear") ;
            // fake validate item
            $user = User::newInstance()->findByEmail($this->email);
            $new_i_item = $user['i_items']+1;
            User::newInstance()->update(array('i_items' => $new_i_item), array('pk_i_id' => $user['pk_i_id']));
        }

        $this->post_item_website();
        if($moderation == -1 || $moderation == 1) {
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Item need validation moderate_items = -1 (NEVER MODERATE). ERROR" );
        } else if($moderation == 0) {
            $this->assertTrue($this->selenium->isTextPresent("Check your inbox to verify your email address"),"Need validation but message don't appear" );
        }
        
        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
        
    }

    private function checkWebsite_logged_user_item_validation($bool)
    {
        // create user
        $this->addUserForTesting();
        // loginWebsite
        $this->loginWebsite();
        // force validation
        Preference::newInstance()->replace('moderate_items', '0',"osclass", 'INTEGER') ;
        // add new item
        $this->post_item_website();

        if($bool == 0){
            $this->assertTrue($this->selenium->isTextPresent("Check your inbox to verify your email address"),"Need validation but message don't appear" );
        } else {
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Item need validation moderate_items = -1 (NEVER MODERATE). ERROR" );
        }

        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
    }

    private function checkWebsite_items_wait_time($sec)
    {
        // create user
        $this->addUserForTesting();
        // loginWebsite
        $this->loginWebsite();
        Preference::newInstance()->replace('moderate_items', '-1',"osclass", 'INTEGER') ;
        if($sec == 0){
            $this->post_item_website();
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Cannot insert item. ERROR" );
            $this->post_item_website();
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Cannot insert item. ERROR" );
        } else if($sec > 0) {
            $this->post_item_website();
            $this->assertTrue($this->selenium->isTextPresent("Your item has been published"),"Cannot insert item. ERROR" );
            $this->post_item_website();
            $this->assertTrue($this->selenium->isTextPresent("Too fast. You should wait a little to publish your ad."),"CAN insert item. ERROR" );
        }

        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
    }

    private function checkWebsite_reg_user_can_contact($bool)
    {
        // create user
        $this->addUserForTesting();
        // loginWebsite
        $this->loginWebsite();
        Preference::newInstance()->replace('moderate_items', '-1',"osclass", 'INTEGER') ;

        $this->post_item_website();
        // ir a search

        $this->selenium->open( osc_base_url(true) );
        $this->selenium->click('link=Logout');
        $this->selenium->open( osc_search_url() );
        // visit fisrt item
        $this->selenium->click('link=foo title');
        
        if($bool == 1){
            $div_present = $this->selenium->isElementPresent("xpath=//div[@id='contact']");
            $this->assertFalse($div_present, "There are div contact form. ERROR");
        } else if($bool == 0) {
            $div_present = $this->selenium->isElementPresent("xpath=//div[@id='contact']");
            $this->assertTrue($div_present, "There aren't div contact form. ERROR");
        }

        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
    }

    private function checkWebsite_enableField_f_price_items( $bool )
    {
        $this->addUserForTesting();
        // loginWebsite
        $this->loginWebsite();
        Preference::newInstance()->replace('moderate_items', '-1',"osclass", 'INTEGER') ;
        // check item_post()
        $this->selenium->open( osc_item_post_url() );
        $exist_input_price = $this->selenium->isElementPresent("xpath=//input[@id='price']") ;

        if($bool == 1){
            $this->assertTrue($exist_input_price, "Not exist input price!. ERROR");
        } else {
            $this->assertTrue(!$exist_input_price, "Exist input price!. ERROR");
        }
        // insert item
        $this->post_item_website();

        $this->selenium->open( osc_search_url() );
        // visit fisrt item
        $this->selenium->click('link=foo title');

        $exist_span_price = $this->selenium->isElementPresent("xpath=//span[@class='price']") ;

        if($bool == 1) { //muestra precio
            $this->assertTrue($exist_span_price , "Not exist span price!. ERROR");
        } else {
            $this->assertTrue( !$exist_span_price , "Exist span price!. ERROR");
        }
        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
    }

    private function checkWebsite_enableField_images_items($bool, $num=0)
    {
        // crear user
        $this->addUserForTesting();
        // logear con user
        $this->loginWebsite();
        // entrar en la pag de post_item
        $this->selenium->open( osc_item_post_url() );
        $exist_input_photo = $this->selenium->isElementPresent("xpath=//input[@name='photos[]']") ;
        if($bool == 1) {
            $this->assertTrue($exist_input_photo, "Not exist input photos[]. ERROR");
        } else if ($bool == 0){
            $this->assertTrue( !$exist_input_photo, "Exist input photos[]. ERROR");
        }
        if($num>0){
            $this->selenium->open( osc_item_post_url() );
            for($i = 0;$i < $num; $i++)
                $this->selenium->click('link=Add new photo');

            $num_photo_input = (int)$this->selenium->getXpathCount("//input[@name='photos[]']") ;

            $this->assertTrue(($num == $num_photo_input), "More or less input photos[]! ERROR") ;
        }
        $user = User::newInstance()->findByEmail($this->email);
        User::newInstance()->deleteUser($user['pk_i_id']);
    }

    private function settings_()
    {
        $pref = $this->getPreferencesItems();

        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=General settings");
        $this->selenium->click("link=» Items");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->click("enabled_recaptcha_items");
        if( $pref['moderate_items'] == -1) {
            $this->selenium->click("moderate_items");
        }

        $this->selenium->type("num_moderate_items",'111');

        $this->selenium->type("items_wait_time", '120' );

        $this->selenium->click("logged_user_item_validation");
        $this->selenium->click("reg_user_post");
        $this->selenium->click("notify_new_item");
        $this->selenium->click("notify_contact_item");
        $this->selenium->click("notify_contact_friends");
        $this->selenium->click("enableField#f_price@items");
        $this->selenium->click("enableField#images@items");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Items' settings have been updated") , "Can't update items settings. ERROR");

        if( $pref['enabled_item_validation'] == 'on' ) {
            $this->assertEqual( $this->selenium->getValue('num_moderate_items'), '111' ) ;
        }
        $this->assertEqual( $this->selenium->getValue('items_wait_time'), '120' ) ;
        if( $pref['enabled_recaptcha_items'] == 'on' ){     $this->assertEqual( $this->selenium->getValue('enabled_recaptcha_items'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('enabled_recaptcha_items'), 'on' ) ;}
        if( $pref['logged_user_item_validation'] == 'on' ){ $this->assertEqual( $this->selenium->getValue('logged_user_item_validation'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('logged_user_item_validation'), 'on' ) ;}
        if( $pref['reg_user_post'] == 'on' ){               $this->assertEqual( $this->selenium->getValue('reg_user_post'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('reg_user_post'), 'on' ) ;}
        if( $pref['notify_new_item'] == 'on' ){             $this->assertEqual( $this->selenium->getValue('notify_new_item'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('notify_new_item'), 'on' ) ;}
        if( $pref['notify_contact_item'] == 'on' ){         $this->assertEqual( $this->selenium->getValue('notify_contact_item'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('notify_contact_item'), 'on' ) ;}
        if( $pref['notify_contact_friends'] == 'on' ){      $this->assertEqual( $this->selenium->getValue('notify_contact_friends'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('notify_contact_friends'), 'on' ) ;}
        if( $pref['enableField#f_price@items'] == 'on' ){   $this->assertEqual( $this->selenium->getValue('enableField#f_price@items'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('enableField#f_price@items'), 'on' ) ;}
        if( $pref['enableField#images@items'] == 'on' ){    $this->assertEqual( $this->selenium->getValue('enableField#images@items'), 'off' ) ;
        } else {                                            $this->assertEqual( $this->selenium->getValue('enableField#images@items'), 'on' ) ;}

        $this->selenium->click("enabled_recaptcha_items");
        $this->selenium->click("logged_user_item_validation");
        $this->selenium->click("reg_user_post");
        $this->selenium->click("notify_new_item");
        $this->selenium->click("notify_contact_item");
        $this->selenium->click("notify_contact_friends");
        $this->selenium->click("enableField#f_price@items");
        $this->selenium->click("enableField#images@items");
        if( $pref['moderate_items'] == -1) {
            $this->selenium->type("num_moderate_items", $pref['num_moderate_items'] );
            $this->selenium->click("moderate_items");
        }
        $this->selenium->type("num_moderate_items", $pref['num_moderate_items'] );
        $this->selenium->type("items_wait_time", $pref['items_wait_time'] );

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue( $this->selenium->isTextPresent("Items' settings have been updated") , "Can't update items settings. ERROR");

        $this->assertEqual( $this->selenium->getValue('enabled_recaptcha_items')        , $pref['enabled_recaptcha_items']) ;
        $this->assertEqual( $this->selenium->getValue('logged_user_item_validation')    , $pref['logged_user_item_validation'] ) ;
        $this->assertEqual( $this->selenium->getValue('reg_user_post')                  , $pref['reg_user_post'] ) ;
        $this->assertEqual( $this->selenium->getValue('notify_new_item')                , $pref['notify_new_item'] ) ;
        $this->assertEqual( $this->selenium->getValue('notify_contact_item')            , $pref['notify_contact_item'] ) ;
        $this->assertEqual( $this->selenium->getValue('notify_contact_friends')         , $pref['notify_contact_friends'] ) ;
        $this->assertEqual( $this->selenium->getValue('enableField#f_price@items')      , $pref['enableField#f_price@items']  ) ;
        $this->assertEqual( $this->selenium->getValue('enableField#images@items')       , $pref['enableField#images@items'] ) ;

        $this->assertEqual( $this->selenium->getValue('items_wait_time')                , $pref['items_wait_time'] ) ;
        $this->assertEqual( Preference::newInstance()->findValueByName('moderate_items'), $pref['num_moderate_items'] ) ;

        unset($pref);

    }

    private function getPreferencesItems()
    {
        $pref = array();
        $pref['enabled_recaptcha_items']        = Preference::newInstance()->findValueByName('enabled_recaptcha_items') ;
        $pref['enabled_item_validation']        = Preference::newInstance()->findValueByName('enabled_item_validation') ;
        $pref['logged_user_item_validation']    = Preference::newInstance()->findValueByName('logged_user_item_validation') ;
        $pref['reg_user_post']                  = Preference::newInstance()->findValueByName('reg_user_post') ;
        $pref['notify_new_item']                = Preference::newInstance()->findValueByName('notify_new_item') ;
        $pref['notify_contact_item']            = Preference::newInstance()->findValueByName('notify_contact_item') ;
        $pref['notify_contact_friends']         = Preference::newInstance()->findValueByName('notify_contact_friends') ;
        $pref['enableField#f_price@items']      = Preference::newInstance()->findValueByName('enableField#f_price@items') ;
        $pref['enableField#images@items']       = Preference::newInstance()->findValueByName('enableField#images@items') ;

        $pref['num_moderate_items']             = Preference::newInstance()->findValueByName('moderate_items') ;
        $pref['moderate_items']                 = Preference::newInstance()->findValueByName('moderate_items') ;
        $pref['items_wait_time']                = Preference::newInstance()->findValueByName('items_wait_time') ;

        if($pref['enabled_recaptcha_items'] == 1){  $pref['enabled_recaptcha_items'] = 'on'; }
        else {                                      $pref['enabled_recaptcha_items'] = 'off'; }
        if($pref['reg_user_post'] == 1){            $pref['reg_user_post']          = 'on'; }
        else {                                      $pref['reg_user_post']          = 'off'; }
        if($pref['notify_new_item'] == 1){          $pref['notify_new_item']        = 'on';}
        else {                                      $pref['notify_new_item']        = 'off'; }
        if($pref['notify_contact_item'] == 1){      $pref['notify_contact_item']    = 'on';}
        else {                                      $pref['notify_contact_item']    = 'off'; }
        if($pref['notify_contact_friends'] == 1){   $pref['notify_contact_friends'] = 'on';}
        else {                                      $pref['notify_contact_friends'] = 'off'; }
        if($pref['enableField#f_price@items'] == 1){$pref['enableField#f_price@items'] = 'on';}
        else {                                      $pref['enableField#f_price@items'] = 'off'; }
        if($pref['enableField#images@items'] == 1){ $pref['enableField#images@items'] = 'on';}
        else {                                      $pref['enableField#images@items'] = 'off'; }
        if($pref['logged_user_item_validation'] == 1){  $pref['logged_user_item_validation'] = 'on';}
        else {                                          $pref['logged_user_item_validation'] = 'off'; }

        return $pref;
    }
}

?>
