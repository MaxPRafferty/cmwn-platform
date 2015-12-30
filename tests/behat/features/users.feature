Feature: Users

    Scenario: Login
        When I request "GET csrf_token"
        Then I get a "200" response
        And the "token" property exists
        Then I can try to login with the username "arron.kallenberg@gmail.com" and the password "business"
        And I get a "200" response

    Scenario: Sidebar
        Given I am logged in with the username "arron.kallenberg@gmail.com" and the password "business"
        When I request "GET sidebar"
        Then I get a "200" response
        And the "data" property exists
        And scope into the first "data" property
            And the properties exist:
                        """
                        Members
                        Roles
                        Ditricts
                        Organizations
                        Groups
                        Games
                        Edit Profile
                        Upload CSV
                        Cloudinary Image
                        """

    Scenario: Friends
        Given I am logged in with the username "arron.kallenberg@gmail.com" and the password "business"
        When I request "GET friends"
        Then I get a "200" response

    Scenario: Groups
        Given I am logged in with the username "arron.kallenberg@gmail.com" and the password "business"
        When I request "GET groups"
        Then I get a "200" response
