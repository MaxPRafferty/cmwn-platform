Feature: Users

Scenario: Returning a csrf token
    When I request "GET /csrf_token"
    Then I get a "200" response
    And the "token" property exists

Scenario: Logging In
    Given I have the payload:
        """
        {"is_following": "foo"}
        """
    When I request "POST /auth/login"
    Then I get a "400" response
