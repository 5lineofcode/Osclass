<?php
require_once '../../../../oc-load.php';

//require_once('FrontendTest.php');

class OCadmin_administrators extends OCadminTest {
    
    /*           TESTS          */
    function testInsertAdministrator()
    {
        $this->loginCorrect() ;
        $this->insertAdministrator() ;
    }

    function testInsertAdministratorTwice()
    {
        $this->loginCorrect() ;
        $this->insertAdministratorAgain();
    }

    function testInsertAdministratorFail()
    {
        $this->loginCorrect() ;
        $this->insertAdministratorInvalidEmail() ;
        $this->insertAdministratorExistentUsername();
    }

    function testEditYourProfile()
    {
        $this->loginCorrect() ;
        $this->editYourProfileAdministrator();
    }

    function testEditAdministrator()
    {
        $this->loginCorrect() ;
        $this->editAdministrator();
        $this->editAdministrator2();
    }

    function testEditAdministratorFailPasswMatch()
    {
        $this->loginCorrect() ;
        $this->editAdministratorFailPass();
    }


    function testDeleteAdministrator()
    {
        $this->loginCorrect() ;
        $this->deleteAdministrator();
    }

    /*      PRIVATE FUNCTIONS       */
    private function insertAdministrator()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» Add new administrator");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one");
        $this->selenium->type("s_username","useradminone");
        $this->selenium->type("s_password", "useradminpass");

        $this->selenium->type("s_email", "admin@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("The admin has been added"),"Add administrator");
    }

    private function insertAdministratorAgain()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» Add new administrator");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one");
        $this->selenium->type("s_username","useradminone");
        $this->selenium->type("s_password", "useradminpass");

        $this->selenium->type("s_email", "admin@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("Email already in use"),"Add administrator with existing email");
    }

    private function insertAdministratorInvalidEmail()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» Add new administrator");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one");
        $this->selenium->type("s_username","useradminone_");
        $this->selenium->type("s_password", "useradminpass_");

        $this->selenium->type("s_email", "admin(at)mailcom");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("Email invalid"),"Add administrator invalid email");
    }

    private function insertAdministratorExistentUsername()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» Add new administrator");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one");
        $this->selenium->type("s_username","useradminone");
        $this->selenium->type("s_password", "useradminpass");

        $this->selenium->type("s_email", "admin_@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("Username already in use"),"Add aministrator existing username");
    }

    private function editYourProfileAdministrator()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» Edit Your Profile");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Administrator updated");
        $this->selenium->type("s_username","adminUpdated");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("The admin has been updated"),"Edit administrator's profile");
    }

    private function editAdministrator()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» List administrators");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'useradminone')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'useradminone')]/td/div/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one NEW");
        $this->selenium->type("s_username","useradminoneNEW");
        $this->selenium->type("old_password", "useradminpass");
        $this->selenium->type("s_password"  , "useradminpassNEW");
        $this->selenium->type("s_password2" , "useradminpassNEW");

        $this->selenium->type("s_email", "admin@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");
        
        $this->assertTrue($this->selenium->isTextPresent("The admin has been updated"),"Edit administrator (other)");
    }

    private function editAdministrator2()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» List administrators");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'useradminone')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'useradminone')]/td/div/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one NEW");
        $this->selenium->type("s_username","useradminoneNEW");

        $this->selenium->type("s_email", "newadmin@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("The admin has been updated"),"Edit administrator (other 2)");
    }

    private function editAdministratorFailPass()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» List administrators");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'useradminone')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'useradminone')]/td/div/a[text()='Edit']");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->type("s_name","Real name user one NEW");
        $this->selenium->type("s_username","useradminoneNEW");
        $this->selenium->type("old_password", "useradminpassNEW");
        $this->selenium->type("s_password"  , "useradminpass");
        $this->selenium->type("s_password2" , "useradminpassNEW");

        $this->selenium->type("s_email", "admin@mail.com");

        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("The password couldn't be updated. Passwords don't match"),"Edit administrator password");
    }

    private function deleteAdministrator()
    {
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Administrators");
        $this->selenium->click("link=» List administrators");
        $this->selenium->waitForPageToLoad("10000");

        $this->selenium->mouseOver("//table/tbody/tr[contains(.,'useradminone')]");
        $this->selenium->click("//table/tbody/tr[contains(.,'useradminone')]/td/div/a[text()='Delete']");
        $this->selenium->waitForPageToLoad("10000");

        $this->assertTrue($this->selenium->isTextPresent("The admin has been deleted correctly"),"Delete administrator");    
    }
}
?>
