Feature: Log into the site
  In order to access privileged content
  As a site visitor
  I need to be able to log in

  Scenario: Create an account
    Given I am on the homepage
    When I follow "Register" 
    Then I should see "Username"
    And I should see "Email"
