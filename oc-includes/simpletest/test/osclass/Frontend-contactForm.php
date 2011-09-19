<?php
require_once '../../../../oc-load.php';

//require_once('FrontendTest.php');

class Frontend_contactForm extends FrontendTest {
    
    /*
     * Send an email to 'contact' (link in footer)
     */

    function testContact()
    {
        $this->selenium->open(osc_base_url(true));
        $this->selenium->click("link=Contact");
        $this->selenium->waitForPageToLoad("30000");
        $this->selenium->type("subject", "subject");
        $this->selenium->type("message", "message\nto be\nsend");
        $this->selenium->type("yourName", "Carlos");
        $this->selenium->type("yourEmail", $this->_email);
        $this->selenium->click("xpath=//span/button[text()='Send']");
        $this->selenium->waitForPageToLoad("30000");

        $this->assertTrue($this->selenium->isTextPresent("Your e-mail has been sent properly. Thank your for contacting us!"), 'Testing, contact form.');
    }
}
?>
