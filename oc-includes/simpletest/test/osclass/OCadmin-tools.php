<?php
require_once '../../../../oc-load.php';

//require_once('FrontendTest.php');

class OCadmin_tools extends OCadminTest {
    
    /*
     * Login oc-admin
     * Import sql
     * Remove imported data
     */
    /*function testImportData()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Tools");
        $this->selenium->click("link=Import data");
        $this->selenium->waitForPageToLoad("10000");
        $this->selenium->type("sql", $this->selenium->_path(LIB_PATH."simpletest/test/osclass/test.sql") );
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue($this->selenium->isTextPresent("Import complete"), "Import a sql file.");
        
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Tools");
        $this->selenium->click("link=Import data");
        $this->selenium->waitForPageToLoad("10000");
        $this->selenium->type("sql", $this->selenium->_path(LIB_PATH."simpletest/test/osclass/test_restore.sql") );
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue($this->selenium->isTextPresent("Import complete"), "Import a sql file.");
    }
    
    /*
     * Login oc-admin
     * Import bad file. 
     */
    /*function testImportDataFail()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Tools");
        $this->selenium->click("link=Import data");
        $this->selenium->waitForPageToLoad("10000");
        $this->selenium->type("sql", $this->selenium->_path(LIB_PATH."simpletest/test/osclass/img_test1.gif") );
        $this->selenium->click("//input[@type='submit']");
        $this->selenium->waitForPageToLoad("10000");
        $this->assertTrue($this->selenium->isTextPresent("There was a problem importing data to the database"), "Import image as sql.");
    }  
    
    /*
     * Login oc-admin
     * Backup database
     */
    function testBackupSql()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Tools");
        $this->selenium->click("link=Backup data");
        $this->selenium->waitForPageToLoad("30000");
        $this->selenium->click("//h3//p/input[@value='Backup (store on server)']");
        $this->selenium->waitForPageToLoad("30000");
        $this->assertTrue($this->selenium->isTextPresent("Backup has been done properly"), "Backup database.");
    }
    
    /*
     * Login oc-admin
     * Backup oclass
     */
    /*function testBackupZip()
    {
        $this->loginWith();
        $this->selenium->open( osc_admin_base_url(true) );
        $this->selenium->click("link=Tools");
        $this->selenium->click("link=Backup data");
        $this->selenium->waitForPageToLoad("30000");
        $this->selenium->click("//h3[contains(.,'Back up OSClass installation')]/p/input[@value='Backup (store on server)']");
        $this->selenium->waitForPageToLoad("30000");
        $this->assertTrue($this->selenium->isTextPresent("Backup has been done properly"), "Backup osclass.");
    }*/
    
    
}
?>
