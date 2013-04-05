<?php


class Frontend_contactForm extends FrontendTest {

    /*
     * Send an email to 'contact' (link in footer)
     */

    function testContact()
    {
        $this->_contact($this->_email);
        $this->assertTrue($this->selenium->isTextPresent("Your email has been sent properly. Thank you for contacting us!"), 'Testing, contact form.');

    }

    /**
     * - invalid email
     * -
     */
    function testContact1()
    {
        $this->_contact('invalid@email');
        sleep(2);
        $this->assertTrue($this->selenium->isTextPresent("Invalid email address"), 'Testing, contact form.');
    }

    private function _contact($email)
    {
        $this->selenium->open( osc_contact_url() );
        $this->selenium->waitForPageToLoad("30000");
        $this->selenium->click("link=Contact");
        $this->selenium->waitForPageToLoad("30000");
        $this->selenium->type("subject", "subject");
        $this->selenium->type("message", "message\nto be\nsent");
        $this->selenium->type("yourName", "Carlos");
        $this->selenium->type("yourEmail", $email);
        $this->selenium->click("xpath=//span/button[text()='Send']");
        sleep(2);
    }
}
?>
