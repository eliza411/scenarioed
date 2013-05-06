Feature: View the home page
  In order to learn about and use ScenarioEd
  As a site visitor
  I need the homepage to load

  Scenario: Visit the homepage
    When I am on the homepage
    Then I should see "Scenarioed"
    And I should not see "Access denied"
    
  Scenario: Visit the project page
    Given I am on the homepage
    When I follow "Projects"
    Then I should see "Username:"
