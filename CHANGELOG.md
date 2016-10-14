Version - 0.4.3
----

- 9cbb5e2: Adjusted config for suggestion cron
- cef8e08: Version bump [ci skip]

Version - 0.4.2
----

- 199498f: Version bump
- ede3e7c: Version bump [ci skip]
- 4ea00ab: Release Snorlax 0.4New CLI to run the suggestion engine for users
Some listeners to clean up suggested friends
Updates to available games
Delete Suggestion When Added To Friend Table
Created a new listener on attach.friend.post to call deleteSuggestions
When a user sends a friend request or receive a friend request, that suggestion is deleted.
When a user accepts a friend request and that suggestion is still in the table, it is deleted.
Added unit test for the listener.
Added integration test to see that the suggestion is actually being deleted.
Added an eventIdentifier property to friendservicedelegator
Modifying the test to attach the listener to an event manager and let the event manager trigger the event
Suggest Cron Job which injects suggest Jobs for users in the database
Adding the console usage messages and refactoring the commands

- d2a865b: Ditto 0.2Fixes:

1. Students now have 'view.groups' permission
2. Fixing the image module to correctly update the moderation status of the images
3. Updated the image to correctly return and set the moderation status
4. Fixed a bug where user type doesn't get set correctly when sending an email

Updates

1. Updating email templates to have cmwn-wrapper
2. Made a default layout to use the same template for different emails
3. Updated the content to be displayed to users
4. Updated the listeners so set the correct templates based on the event
5. Updated logo in the email template
6. Change the time out for the temporary code
7. Basic feed API created
8. User-name normalization now exists to allow children to login easier
9. Added HAL Link for skribble
10. Group endpoint will now only return the child groups the user is assigned to

Version - 0.4.1
----

- 4ea00ab: Release Snorlax 0.4New CLI to run the suggestion engine for users
Some listeners to clean up suggested friends
Updates to available games
Delete Suggestion When Added To Friend Table
Created a new listener on attach.friend.post to call deleteSuggestions
When a user sends a friend request or receive a friend request, that suggestion is deleted.
When a user accepts a friend request and that suggestion is still in the table, it is deleted.
Added unit test for the listener.
Added integration test to see that the suggestion is actually being deleted.
Added an eventIdentifier property to friendservicedelegator
Modifying the test to attach the listener to an event manager and let the event manager trigger the event
Suggest Cron Job which injects suggest Jobs for users in the database
Adding the console usage messages and refactoring the commands

=======
Version - 0.3.9
----

- d2a865b: Ditto 0.2Fixes:

1. Students now have 'view.groups' permission
2. Fixing the image module to correctly update the moderation status of the images
3. Updated the image to correctly return and set the moderation status
4. Fixed a bug where user type doesn't get set correctly when sending an email

Updates

1. Updating email templates to have cmwn-wrapper
2. Made a default layout to use the same template for different emails
3. Updated the content to be displayed to users
4. Updated the listeners so set the correct templates based on the event
5. Updated logo in the email template
6. Change the time out for the temporary code
7. Basic feed API created
8. User-name normalization now exists to allow children to login easier
9. Added HAL Link for skribble
10. Group endpoint will now only return the child groups the user is assigned to

- 464231d: Revert "Merge branch 'release/ditto' into rc"This reverts commit 38b50dfddf070142362e1f4d9a940fe295c9cf18.
- 86ea486: Ditto 0.2Fixes:

1. Students now have 'view.groups' permission
2. Fixing the image module to correctly update the moderation status of the images
3. Updated the image to correctly return and set the moderation status
4. Fixed a bug where user type doesn't get set correctly when sending an email

Updates

1. Updating email templates to have cmwn-wrapper
2. Made a default layout to use the same template for different emails
3. Updated the content to be displayed to users
4. Updated the listeners so set the correct templates based on the event
5. Updated logo in the email template
6. Change the time out for the temporary code
7. Basic feed API created
8. User-name normalization now exists to allow children to login easier
9. Added HAL Link for skribble
10. Group endpoint will now only return the child groups the user is assigned to

Version - 0.3.8
----

- 54e9844: Version bump [ci skip]

Version - 0.3.7
----

- 6e6d51b: Version bump [ci skip]

Version - 0.3.6
----

- 7995bd4: updatedImportSuccessText
- 5faceac: addingDefaultEmailLayoutAndChangingImages
- 5ad3b55: Version bump [ci skip]

Version - 0.3.5
----

- 47fc7d3: Version bump [ci skip]

Version - 0.3.4
----

- dcecef4: Version bump [ci skip]
- d34c7a0: merge

Version - 0.3.3
----

- d34c7a0: merge

Version - 0.3.2
----

- b8596cc: Version bump [ci skip]

Version - 0.3.1
----

Version - 0.2.3
----

- 304bc6c: Fix for the changelog.sh
- 7385519: Update CHANGELOG.md

Version - 0.2.2
----

- c02f488: Another test here for committing the file
- 80c4a6e: Trying Git Init in wercker
- 07131d5: wercker to do git fetch before committing
- 854d8e3: Got wercker working
- fb690b5: Version bump [ci skip]
- 1b9d144: Changing around the mv command in the changelog
- 36f0b49: Fixing changelog.sh for app.wercker.com environment
- 19e0574: Running chmod for app.wercker.com
- f01d279: Debugging app.wercker.com
- 32ff84e: Updated run permissions and added $PWD to wercker
- 3c8153a: Updated Wercker.yml to call changelog.sh not changelog.md
- c850ce4: Adding Version bump and Changelog script Updated Deploy setp for wercker
- 078ee78: Removing Draft constraint as wercker will only build on release/* branches Adding correct version in composer
- 49470d2: Grouping some functions together
- 2528569: Seems to still be failing on app.wercker.com  Adding some more stuff for a test
- 6d3b069: Got the build working in a more stable way
- 4e15db0: Another wercker fix
- 6e4d8dc: Version bump - Wercker fixes for deploy Docker-compose updates to no longer build the images but pull from docker hub
- f1eb614: Small fix to the security test
- ad414d8: Updated students to be able to view groups
- 99c6b72: Updated Scope to now include the type for groups and orgs Revoked view.user.orgs for asst_principal
- 666cd53: Updated link in email template
- 459ab63: Removing school HAL Link for children in a hacky way
- df0d529: Updated importer to no longer check for invalid birthday when the row is bad
- 8c8e170: Updated test since it was wrong
- 98ddc47: Updated Security route to set permissions for DELETE on api.rest.user
- 0b4f174: Damn one line of code from a function that is now depreated
- 52d4c07: Added the user type for the scope listener
- 734838b: made group type required in db and forms
- 3e3e342: Updating suggested friends service
- cc54666: Questioning reality
- a88c878: Removing worker for the moment so I can run manually to TS errors
- 206d05b: Adding debug to worker
- 412ddb6: Updated tests to respect the new permissions Missed some permissions with the last commit
- 70f02aa: Updated permissions Perm controller cleans up the csv in a hacky way
- cd72fa2: Adding the permission to the export
- 8ae91c6: Updating importer to check based on network_id rather than org_id
- 65fddbc: Added a CLI Helper to dump out the current permissions each role has Updated RBAC to include some helpers to get the roles and permissions
- e6dcda8: Fixed typo that was causing tests to fail
- f49fa4b: Fixing failing test DOH!
- d08a8b7: Fix for group validator using meta instead of type Removing the cache cleaning from box
- 5a87aae: Forcing in apigility version in composer Removing the step to install phinx and just making it required (hopefully will speed up deploys)
- 425c62e: Staic code cleanup
- cabdeb8: Completed whack-a-mole Removed pre-loading of the organization in the api No longer will we be retunring null for a role If no org_id, user_id or group_id is in the route, the Route listener will now assume the me role
- 75595c8: Updated Security Group Service to now take in a user id instead of a user Updated RouteListener to use Security group service Updated Permissions to include correct type for the roles Some General Clean upWhack-a-mole with permissions has started

- 0135a7b: Updated user group service to be aware of the network Id Moved documentation down to the interface Cleaned up Group Service
- c2dab03: Updated Import Resource to load the group correctly Added Static Type validator Update logging for JobRunner since Resque will not log exceptions Update worker to login a super user
- 74b818d: Revert "Updated Group Service Test"This reverts commit 4ccd1cde8569a76199ddf2c8bc4e4a80fd8b19d2.

- 14ac48d: Updated UserGroupService
- 1be79ca: Adding Network_id to all datasets for integration testing Adding PHPCS to check the test folder (which meant running PHPCBF on that folder) Updating test for Classroom action test to call createGroup instead of saveGroup
- ad91206: Updated Group Service delegator to trigger error event and re-throw exception Updated Group Serivce AddChildToGroup to have a small optimization Added network_id to tests
- 4ccd1cd: Updated Group Service Test
- 184db17: Adding skribble to staging
- 0dd7b1b: No longer checking CSRF for OPTIONS
- 14932dc: Updated composer.json to have explict apigiliy version to avoid deprecation exceptions Removed GroupServiceInterface::fetchAllForUser Refactored GroupServiceInterface::saveGroup into create and updated group
- 37a0dc1: Fixed warning on the composite
- 1aee499: Basic auth listener now sets a csrf token for the session Added tests for skribble notifier
- 7832687: Removing all-about-you from production
- e82a52b: Testing HTTP Auth Created Factories to Allow injecting adapters
- 867718d: Updated security permissions
- 67434ad: Adding games as playable
- b00d295: adding all-about-you survey to seeder
- ed902c7: PHPMD fixes (Need to fix the commit hooks)
- 65bc97e: Updated logging to be less verbose Added some logging in new places to help with trouble shooting PHPCS Cleanup
- 4268535: Hack in skribble
- 5c11f6c: Adding skribble arn to pull in arn from env
- 612b320: Updated docker image Updated wercker to install dev dep
- 3974905: Removing skribble arn
- a6ddc71: Adding skribble arn to pull in arn from env
- 80e60c5: Updated docker image Updated wercker to install dev dep
- cc5b6fe: Removing skribble arn
- a1030f0: Damn apigility update
- c440191: Updated default sort order of skribbles PHPCS fixes
- 4269dc9: removing survey from this branch
- 066cfab: Dep hell fixes
- 3841847: Goddamn fig erratas
- cd098fc: Static Code Clean up
- 15f1fc7: Import Controller now logs in a super user for processing
- 5f2dbcc: Updated Importer test to login a user
- 8a8f190: Fixed permissions after rebase for this branch
- 1eec5fb: fix phpcs check
- 45c9db1: removed mock logger
- b51dd80: Completed ExpireAuthSessionListenerTest
- 65240bb: Removing keys
- 84ec794: listener uses nooploggerawaretrait
- 51c7c26: adding test for core-1059
- 4dc8676: Adding test to check CORE-1060
- 7d6c5e7: adding test to see if principal can update other group.
- 2611cc6: fixed group input filters to make type a required field.
- e42349b: fix phpcs check
- a9017b9: removed mock logger
- fdf3c0a: removing logger
- a0938b9: wrecker fix
- cb5d650: Finished Xsrf Guard Test
- 0f7e8aa: Completed ExpireAuthSessionListenerTest
- ad848f7: added a parent function in AbstractApigilityTestCase to check change password exception
- 7767b71: adding tickets
- f5cec69: making corrections
- 3cb66b9: i
- 091ad70: adding test for change password user
- 7db694b: change
- 89c5477: phpcs check
- 1bb1a1f: fixing game api to have meta field
- cd249a4: Added HTTP Basic Auth Listener to allow our own services to be able to log in
- c1a1c80: Moving into the if
- 0de9541: removing unnecessary check
- 8b4456e: Updating log level for pagoda
- 8e8d550: Updated filter for skribble read flag
- f7dea76: adding new flips
- 921f093: updating games to have meta field and adding all-about-you to games.
- 4a599cc: updating for read flag
- 0c76a32: Updating service to return correct skribbles
- f48bfbb: cloned correct staging state
- 5d944b5: increased friend and skribble limits
- b666e53: Marking tests skipped for bcmath
- e576e4f: phpcs fix
- 5714338: Fixing tests
- bf50f3e: fixed remaining skribble tests also gitignored swp files
- d0cedb9: fixed several skribble tests
- ec712ce: added test and order fields
- 043b34e: huge commit-Changing role names to append type of user and updating related tests
Changing the group route listener to remove on route
changing the resources to remove dependende or route parameters for prefetched resources
Updating tests

- a0c6917: Filling in Skribble URL when Skribble is marked as complete
- 0cccc9d: Fixed sprintf to printf
- d241512: Fixed SQL error when fetching groups using a parentt # with '#' will be ignored, and an empty message aborts the commit.

- 91d80c7: Tech branch to clean up docker-compose.yml
- e844fd8: PHPCS Fix
- b12a337: Expanding tests to check group relations
- bdf095b: Updated importer and tests to now be compliant with new group service interface
- db14624: Adding the DDBNNN to the student ID in order to allow schools to share student id
- 6acf11c: Found an easier way to fix this bug.  Require group service to have an organization id when fetching by internal id
- 2216a17: Revert "Adding the DDBNNN to the student ID in order to allow schools to share student id"This reverts commit 7e7e1bfddaa2f9f8771335963bd1215bc4d9b2a9.

- c07fb9e: FINIAL_FINIAL_FINIAL_DRAFT_REVIESED_DRAFT
- 8695595: Adjusting production
- d068894: Working in referse for coming soon
- 65338aa: Adjusting game list
- 9f83de6: Fixed config for resources Set default page size to be 25 for all resources
- e64685e: Updated default page size to 150 for all pages until pagination can be resolved
- 727d1e7: wrecker fix
- 5e0857f: Finished Xsrf Guard Test
- a6b3c4c: PHPCS Fix
- 5cd9906: Small merge failure
- 2bb1316: Updadting game descriptions Adding Skribble Removing default coming_soon for polar-bear
- 109153d: Updated Security\GroupServiceListner to use userGroupService for fetching groups
- 95a3aaf: Children no longer have district Hal Links returned
- a284cfb: Children no longer have district Hal Links returned
- 5eb6e35: added a parent function in AbstractApigilityTestCase to check change password exception
- 252cc85: Added some debugging to the dev seed
- 0c3f4e5: Adding the DDBNNN to the classroom id
- 975c24f: adding tickets
- eb6d101: checking change password exception for all routes and made minor changes to fix route listener
- 7e7e1bf: Adding the DDBNNN to the student ID in order to allow schools to share student id
- 46b5f4f: making corrections
- 2478086: i
- 730e357: adding test for change password user
- 1b46f94: change
- 18b7c2c: flip
- 779cfcb: Adding Skribble
- fd30633: Fixing Seed and configs
- 14e5ea4: Updating game seed to make it easier to adjust games
- 6a3870c: Fixing box file
- 75beceb: Fixing Seed and configs
- ea83cf5: Attach Friend Validator Test`
- 4860d04: Game Seeding debug
- 4c4cf99: Adding a try catch for fetching games
- ba1a627: Fixed Skribble job and SNS Service
- 72b94b4: Adding nikki to super seeding
- af2493a: Updating game seed to make it easier to adjust games
- c0a7a8f: Updated query to get correct role in a group
- 5b7f739: adding test for core-727
- 903850c: added test to check CORE-725
- 26ea8b2: added file
- f225404: Updating to Sns
- 4c61c53: Friend Service Tests
- 88a4ec9: Doh!! Forgot to update the job for the new route
- 33ab6df: Skribble Notifcation API Skribble Resource will update status of skribble
- 95c907b: Updated specification for asset type instead of type Fixing failing tests
- fb56df7: Fixing Wrecker build
- b6d3e98: suggested Friend service test
- fd28554: Updated rules to check for the correct field
- 8c0da97: Fixed spaces in path for skribble urls Opening up routes to get testing underway
- 55d8322: Added SqS Job Service Added the ability for the Skribble Resources to skramble the skribbles Update in skribble rules to check for empty instead of null Updated Skribble validation to accepts numbers for the version
- 6512a62: stubbing out sending to sqs
- bb883ab: Fixing Boxfile
- 939310d: adding AWS Sdk
- e2cbb17: CS Fix
- 4534571: Adam was wacthing me type when I was typing skirbble instead of skribble Updated Database to allow friend to be null Updated Database to have rules field Adding integration Tests for SkribbleService and SkribbleResource Updated Skribble Service to allow Id for Deleting Skribbles Updated Skribble Delegator to re-throw exceptions
- 1f9351c: deleting empty files
- d60c693: suggested friend service delegator test
- 5b5ce4a: FriendServiceDelegatorTest
- 8727eba: Created Rule Validator Stubbed AreFriendsValidator Updated SkirbbleResouce to not longer be stubbed
- d64734d: Created Skribble API Updated Exceptions to be part of the skribble Module Fixed the Skribble Delegator Factory to be in correct place Made createdBy and friendTo idempontent
- 9cfd5b9: Created Skribble Serivce Created Skribble Delegator
- c02d918: minor changes
- 9e937a4: dding test for the bug.
- 8f58c67: changes to user service test
- f4eb859: Added a listener for updating username of me user
- 93d2bea: Completed Skribble testing
- e5065b8: Expanding rules testing
- 7a24c3e: code cleanup  Please enter the commit message for your changes. Lines starting
- fc2ecc9: Adding tickets
- 96e1b5c: Teacher cannot set child username now. Only the user can change his username.
- cba1bc1: Fixed the password reset bug. Added a message to indicate the case change.
- 29af460: fixed core-773
- 33540c5: added the order for statements
- 95727a3: Finished the incomplete tests.
- f7288df: Fixed Regex for Origin Match
- 35760cc: Adding Intern Seeding
- c45f4fa: Temp data
- 21cb713: Removing file logging from master
- 62df49e: Fixing typo in config
- a8d6226: Fixing typo in config
- 1012707: Adding Logging to file since rollbar is cutting off my trace
- 6ce92a7: Found 2 build keys in boxfile
- 96346e7: Trying to force remove composer file again
- e315692: Updating game descriptions
- 9ea3fa2: Updating box to force delete vendor
- ba9645b: Adding Tag-It Adjusting Comming Soon games
- 245d6b8: Adding MediaProperties Updated Skribble.json for test
- 75a21f0: Adding properties to media objects
- faf4bcf: Updated Rule Static Factory to normalize out name of classes Created test for Skribble Rules

Version - 0.2.1
----

- 3af3e09: Update to aws-sync to no longer remove previous builds from s3
- 454c5b8: Updated Wercker to create a release Added Version Bump and Change log scripts
- 685675b: Removed hard names
- f1eb614: Small fix to the security test
- 9d36a3f: Enabling all-about-you game
- ad414d8: Updated students to be able to view groups
- 99c6b72: Updated Scope to now include the type for groups and orgs Revoked view.user.orgs for asst_principal
- 666cd53: Updated link in email template
- 459ab63: Removing school HAL Link for children in a hacky way
- df0d529: Updated importer to no longer check for invalid birthday when the row is bad
- 8c8e170: Updated test since it was wrong
- 98ddc47: Updated Security route to set permissions for DELETE on api.rest.user
- 0b4f174: Damn one line of code from a function that is now depreated
- b3dccb2: Dont edit files in github kids
- 169e21a: Adding TODO
- 52d4c07: Added the user type for the scope listener
- 734838b: made group type required in db and forms
- 3e3e342: Updating suggested friends service
- cc54666: Questioning reality
- a88c878: Removing worker for the moment so I can run manually to TS errors
- 206d05b: Adding debug to worker
- 412ddb6: Updated tests to respect the new permissions Missed some permissions with the last commit
- 70f02aa: Updated permissions Perm controller cleans up the csv in a hacky way
- cd72fa2: Adding the permission to the export
- 2540520: Updated permissions for me
- 8ae91c6: Updating importer to check based on network_id rather than org_id
- 7ac4c38: Lowering log level
- 5874b01: small importer fix that was missed
- 65fddbc: Added a CLI Helper to dump out the current permissions each role has Updated RBAC to include some helpers to get the roles and permissions
- e6dcda8: Fixed typo that was causing tests to fail
- f49fa4b: Fixing failing test DOH!
- d08a8b7: Fix for group validator using meta instead of type Removing the cache cleaning from box
- 5a87aae: Forcing in apigility version in composer Removing the step to install phinx and just making it required (hopefully will speed up deploys)
- 425c62e: Staic code cleanup
- cabdeb8: Completed whack-a-mole Removed pre-loading of the organization in the api No longer will we be retunring null for a role If no org_id, user_id or group_id is in the route, the Route listener will now assume the me role
- 75595c8: Updated Security Group Service to now take in a user id instead of a user Updated RouteListener to use Security group service Updated Permissions to include correct type for the roles Some General Clean upWhack-a-mole with permissions has started

- 0135a7b: Updated user group service to be aware of the network Id Moved documentation down to the interface Cleaned up Group Service
- c2dab03: Updated Import Resource to load the group correctly Added Static Type validator Update logging for JobRunner since Resque will not log exceptions Update worker to login a super user
- 8c609ab: Updating to include skribble
- 74b818d: Revert "Updated Group Service Test"This reverts commit 4ccd1cde8569a76199ddf2c8bc4e4a80fd8b19d2.

- 14ac48d: Updated UserGroupService
- 1be79ca: Adding Network_id to all datasets for integration testing Adding PHPCS to check the test folder (which meant running PHPCBF on that folder) Updating test for Classroom action test to call createGroup instead of saveGroup
- ad91206: Updated Group Service delegator to trigger error event and re-throw exception Updated Group Serivce AddChildToGroup to have a small optimization Added network_id to tests
- 4ccd1cd: Updated Group Service Test
- 184db17: Adding skribble to staging
- 0dd7b1b: No longer checking CSRF for OPTIONS
- 14932dc: Updated composer.json to have explict apigiliy version to avoid deprecation exceptions Removed GroupServiceInterface::fetchAllForUser Refactored GroupServiceInterface::saveGroup into create and updated group
- 37a0dc1: Fixed warning on the composite
- 1aee499: Basic auth listener now sets a csrf token for the session Added tests for skribble notifier
- 7832687: Removing all-about-you from production
- e82a52b: Testing HTTP Auth Created Factories to Allow injecting adapters
- 867718d: Updated security permissions
- 67434ad: Adding games as playable
- b00d295: adding all-about-you survey to seeder
- ed902c7: PHPMD fixes (Need to fix the commit hooks)
- 65bc97e: Updated logging to be less verbose Added some logging in new places to help with trouble shooting PHPCS Cleanup
- 4268535: Hack in skribble
- 5c11f6c: Adding skribble arn to pull in arn from env
- 612b320: Updated docker image Updated wercker to install dev dep
- 3974905: Removing skribble arn
- a6ddc71: Adding skribble arn to pull in arn from env
- 80e60c5: Updated docker image Updated wercker to install dev dep
- cc5b6fe: Removing skribble arn
- a1030f0: Damn apigility update
- c440191: Updated default sort order of skribbles PHPCS fixes
- 4269dc9: removing survey from this branch
- 066cfab: Dep hell fixes
- 3841847: Goddamn fig erratas
- cd098fc: Static Code Clean up
- 15f1fc7: Import Controller now logs in a super user for processing
- 5f2dbcc: Updated Importer test to login a user
- 8a8f190: Fixed permissions after rebase for this branch
- 1eec5fb: fix phpcs check
- 45c9db1: removed mock logger
- b51dd80: Completed ExpireAuthSessionListenerTest
- 65240bb: Removing keys
- 84ec794: listener uses nooploggerawaretrait
- 51c7c26: adding test for core-1059
- 4dc8676: Adding test to check CORE-1060
- 7d6c5e7: adding test to see if principal can update other group.
- 2611cc6: fixed group input filters to make type a required field.
- e42349b: fix phpcs check
- a9017b9: removed mock logger
- fdf3c0a: removing logger
- a0938b9: wrecker fix
- cb5d650: Finished Xsrf Guard Test
- 0f7e8aa: Completed ExpireAuthSessionListenerTest
- ad848f7: added a parent function in AbstractApigilityTestCase to check change password exception
- 7767b71: adding tickets
- f5cec69: making corrections
- 3cb66b9: i
- 091ad70: adding test for change password user
- 7db694b: change
- 89c5477: phpcs check
- 1bb1a1f: fixing game api to have meta field
- cd249a4: Added HTTP Basic Auth Listener to allow our own services to be able to log in
- c1a1c80: Moving into the if
- 0de9541: removing unnecessary check
- 8b4456e: Updating log level for pagoda
- 8e8d550: Updated filter for skribble read flag
- f7dea76: adding new flips
- 921f093: updating games to have meta field and adding all-about-you to games.
- 4a599cc: updating for read flag
- 0c76a32: Updating service to return correct skribbles
- f48bfbb: cloned correct staging state
- 5d944b5: increased friend and skribble limits
- b666e53: Marking tests skipped for bcmath
- e576e4f: phpcs fix
- 5714338: Fixing tests
- bf50f3e: fixed remaining skribble tests also gitignored swp files
- d0cedb9: fixed several skribble tests
- ec712ce: added test and order fields
- 043b34e: huge commit-Changing role names to append type of user and updating related tests
Changing the group route listener to remove on route
changing the resources to remove dependende or route parameters for prefetched resources
Updating tests

- a0c6917: Filling in Skribble URL when Skribble is marked as complete
- 0cccc9d: Fixed sprintf to printf
- d241512: Fixed SQL error when fetching groups using a parentt # with '#' will be ignored, and an empty message aborts the commit.

- 91d80c7: Tech branch to clean up docker-compose.yml
- e844fd8: PHPCS Fix
- b12a337: Expanding tests to check group relations
- bdf095b: Updated importer and tests to now be compliant with new group service interface
- db14624: Adding the DDBNNN to the student ID in order to allow schools to share student id
- 6acf11c: Found an easier way to fix this bug.  Require group service to have an organization id when fetching by internal id
- 2216a17: Revert "Adding the DDBNNN to the student ID in order to allow schools to share student id"This reverts commit 7e7e1bfddaa2f9f8771335963bd1215bc4d9b2a9.

- c07fb9e: FINIAL_FINIAL_FINIAL_DRAFT_REVIESED_DRAFT
- 8695595: Adjusting production
- d068894: Working in referse for coming soon
- 65338aa: Adjusting game list
- 9f83de6: Fixed config for resources Set default page size to be 25 for all resources
- e64685e: Updated default page size to 150 for all pages until pagination can be resolved
- 727d1e7: wrecker fix
- 5e0857f: Finished Xsrf Guard Test
- a6b3c4c: PHPCS Fix
- 5cd9906: Small merge failure
- 2bb1316: Updadting game descriptions Adding Skribble Removing default coming_soon for polar-bear
- 109153d: Updated Security\GroupServiceListner to use userGroupService for fetching groups
- 95a3aaf: Children no longer have district Hal Links returned
- a284cfb: Children no longer have district Hal Links returned
- 5eb6e35: added a parent function in AbstractApigilityTestCase to check change password exception
- 252cc85: Added some debugging to the dev seed
- 0c3f4e5: Adding the DDBNNN to the classroom id
- 975c24f: adding tickets
- eb6d101: checking change password exception for all routes and made minor changes to fix route listener
- 7e7e1bf: Adding the DDBNNN to the student ID in order to allow schools to share student id
- 46b5f4f: making corrections
- 2478086: i
- 730e357: adding test for change password user
- 1b46f94: change
- 18b7c2c: flip
- 779cfcb: Adding Skribble
- fd30633: Fixing Seed and configs
- 14e5ea4: Updating game seed to make it easier to adjust games
- 6a3870c: Fixing box file
- 75beceb: Fixing Seed and configs
- ea83cf5: Attach Friend Validator Test`
- 4860d04: Game Seeding debug
- 4c4cf99: Adding a try catch for fetching games
- ba1a627: Fixed Skribble job and SNS Service
- 72b94b4: Adding nikki to super seeding
- af2493a: Updating game seed to make it easier to adjust games
- c0a7a8f: Updated query to get correct role in a group
- 5b7f739: adding test for core-727
- 903850c: added test to check CORE-725
- 26ea8b2: added file
- f225404: Updating to Sns
- 4c61c53: Friend Service Tests
- 88a4ec9: Doh!! Forgot to update the job for the new route
- 33ab6df: Skribble Notifcation API Skribble Resource will update status of skribble
- 95c907b: Updated specification for asset type instead of type Fixing failing tests
- fb56df7: Fixing Wrecker build
- b6d3e98: suggested Friend service test
- fd28554: Updated rules to check for the correct field
- 8c0da97: Fixed spaces in path for skribble urls Opening up routes to get testing underway
- 55d8322: Added SqS Job Service Added the ability for the Skribble Resources to skramble the skribbles Update in skribble rules to check for empty instead of null Updated Skribble validation to accepts numbers for the version
- 6512a62: stubbing out sending to sqs
- bb883ab: Fixing Boxfile
- 939310d: adding AWS Sdk
- e2cbb17: CS Fix
- 4534571: Adam was wacthing me type when I was typing skirbble instead of skribble Updated Database to allow friend to be null Updated Database to have rules field Adding integration Tests for SkribbleService and SkribbleResource Updated Skribble Service to allow Id for Deleting Skribbles Updated Skribble Delegator to re-throw exceptions
- 1f9351c: deleting empty files
- d60c693: suggested friend service delegator test
- 5b5ce4a: FriendServiceDelegatorTest
- 8727eba: Created Rule Validator Stubbed AreFriendsValidator Updated SkirbbleResouce to not longer be stubbed
- d64734d: Created Skribble API Updated Exceptions to be part of the skribble Module Fixed the Skribble Delegator Factory to be in correct place Made createdBy and friendTo idempontent
- 9cfd5b9: Created Skribble Serivce Created Skribble Delegator
- c02d918: minor changes
- 9e937a4: dding test for the bug.
- 8f58c67: changes to user service test
- f4eb859: Added a listener for updating username of me user
- 93d2bea: Completed Skribble testing
- e5065b8: Expanding rules testing
- 7a24c3e: code cleanup  Please enter the commit message for your changes. Lines starting
- fc2ecc9: Adding tickets
- 96e1b5c: Teacher cannot set child username now. Only the user can change his username.
- cba1bc1: Fixed the password reset bug. Added a message to indicate the case change.
- 29af460: fixed core-773
- 33540c5: added the order for statements
- 95727a3: Finished the incomplete tests.
- f7288df: Fixed Regex for Origin Match
- 35760cc: Adding Intern Seeding
- c45f4fa: Temp data
- 21cb713: Removing file logging from master
- 62df49e: Fixing typo in config
- a8d6226: Fixing typo in config
- 1012707: Adding Logging to file since rollbar is cutting off my trace
- 6ce92a7: Found 2 build keys in boxfile
- 96346e7: Trying to force remove composer file again
- e315692: Updating game descriptions
- 9ea3fa2: Updating box to force delete vendor
- ba9645b: Adding Tag-It Adjusting Comming Soon games
- 245d6b8: Adding MediaProperties Updated Skribble.json for test
- 75a21f0: Adding properties to media objects
- faf4bcf: Updated Rule Static Factory to normalize out name of classes Created test for Skribble Rules
- 9f65296: Updated rules to return type Completed Collection Test Created static rule factroy to help wil exchanging arrays
- 1f07beb: Modifying listener to check for UserInterface Rather than UserEntities
- 8529458: fixed the update organization bug and made slight changes to make the OrgnizationServiceTest run
- 350886c: Fixed config to work with mail gun and use ssl to connect
- 233df80: it was a testing bug. Fixed it.
- 144bf06: removed a meaningless annotation
- a9e1410: More Unit testing
- 2c9f282: Merged feature/CORE-770/bugfixes-ghastly to feature/CORE-845/backend-tests and added a few userimage resource tests
- ef5a775: changes
- 88541a3: Started testing Rules
- 2d1ceee: Stubbed out Rules
- 749d71f: deleted unnecessary tables in the login dataset
- 5e1c0ee: added tests, 3 incomplete tests
- 6cd8cb9: Adjusting Boxfile to create ssl keys
- d1d0610: org resource tests
- e6d0f11: Stubbing out interfaces and classes
- a4d5696: Added logout resource test
- 7c60862: made changes told by @manchuck Please enter the commit message for your changes. Lines starting
- ebf5bf0: Login Resource Tests
- 33cbbd3: Some Last min cleanup
- ec72fe3: Removing my IP from docker-compose.yml
- c28e9b6: Removing SSL Keys
- bf51fa1: Updates now that media server is all fleshed out Created Media API
- f62d8eb: Created Media Module Created HTTP Client Factory Updated Install To build dev ssl keys for http client Created TestStreamingAdapter Updated Gitignore for docker-compose.yml
- 8dd2080: GroupUserResourceTests
- 1199728: Added changes as per the comments from @manchuck and @adamwalzer
- 9cebd02: Fixed bug where group end point was not returning the parent groups
- 21cded0: Group Resource testsPlease enter the commit message for your changes. Lines starting

- 24e60a0: Fixed bug where group end point was not returning the parent groups
- dc048d6: Fixed bug where group end point was not returning the parent groups
- 1ac7eb6: added user and route tests
- de4eb04: Added tests for user logged in is the one requesting resource
- 5a21273: changes to flip and friend
- e1a689a: Created new Permission to allow other users to see profile images
- 983bdec: Unit Tests for flips and userflips
- a067a94: Fixing defualt dataset
- 5b74da5: Merge branch 'manchuck/CORE-645/change-user-type' into QA
- b6039ea: Children no longer have district Hal Links returned
- eeb5612: Updated UserService to no longer change the user type on update Added IntegrationTest for the UserService Moved the LoginUser to a trait to make testing easier
- b5b8e93: Fixing PHPCS so @Brunation11 can be happy
- 2707c48: Adding in Save game HAL Link Template Listener now helps fix encoded templates on entities
- f325154: Created API Endpoints Updated SaveGame to require a version number for the data
- 2386d6b: Adding Save Game Service and Delegator
- 669ab33: Fixing docker-compose.yml so the platform stops trying to connect to my computer
- f034ae6: Minor changes to hooks
- 8789a13: Fixing boxfile
- 569f1ce: Adjusted suggested friend service to return back users that are waiting for you to accept Adjusted name of REQUESTED to make @MaxPRafferty happy FriendService now returns correct status on user CORE-737
- b360642: No longer adding hal link on Me Entity in the listener
- 1a82fa5: No longer adding hal link on Me Entity in the listener
- 3365b23: Added check to friend listener to not add hal link if already set
- 2e66db7: Found issue with pointers not being followed
- c4d72ac: Adding CoC"
- 33c09af: Adding a nice comment to the user group service
- c408f2e: Updated suggested friend service to have it's own adapter Resolved CORE-703 and CORE-701
- 40f04de: Adding ignore to auto-generated file
- 2aa8351: Updated UserGroupService to take in Where so listeners can correcrlty filter out deleted/active users when looking at groups or organizations
- 166d9d2: Setting up suggested friend service testing for core-701
- 8159f95: Small fix to have the scripts removed the containers when done
- 518ff4c: Updated The username listener to fix checking for generated names on new users Updated Docker-compose to include helpful env vars for debugging cli Added Tests for UserName stuff
- 64d7a81: Updated Test to be more realworld
- ee2c588: Added Tests For Change Password Added back in updatepassword endpoint for when a user has a code and need to change it
- bc6ff62: Update phinx config for docker changes
- d64faef: Forcing names for containers
- 949f947: Updating wercker to set the pre-release flag for github
- 7737e63: Testing Delpoy pipline
- b8acbaf: Missing the step to build the directory
- fa7cf73: Adding package to pipeline and testing it out
- 7e37dbe: Adding sleep to allow mysql to get start
- 5c7f6e4: Updated Wercker.yml for new testing DB
- fda9f3a: Updated commit to include source (accidentlly removed) Moved API Intergration tests to a V1 folder Updated UserGroup Service to return all groups and all users for a user correctly
- 8db9cd5: Adding test DB as that was screwing up my development
- 2e53ea2: Updated failing tests Made the databasename the same across all environments
- f0c6b1b: Updated User group service to return the parent group
- f6258eb: Updating hooks to use correct machiene
- bef0272: Updated to use custom docker machiene
- 9f7ffe4: Adding xdebug
- 07968e8: Removing puppet and vagrant
- e6c0fbf: Updated all hooks to make calls using docker-compose
- 75baafe: Updated Readme for docker
- 9defc4a: Updating install script Updating git hooks to call docker for testing Fixing docker build scripts for docker-compose
- 90d236b: Got containers working in docker
- a359678: Adding in commit hooks Updating docker Addeing deploy to wercker
- 9481c71: Docker updates
- 1b06394: Adding build continer for docker Adjusting php container
- 0d74ccd: Adding slack URL
- 41364fa: Adding slack URL
- 541e366: Adding new wercker output to gitignore
- 9299697: Updated to pass unit tests for Me Entity
- 03cfb3f: Corrected permissions for super users Adjusted UserHalLinkListener to inject correct hal links Fix for Scope Listener to no longer warn with super users
- 5c3888a: Fixed Git merging function incorrectly
- e7f73a6: Updated route for reset password Added password Validator Factory to correctly build the password validator
- 18dd8bd: Removing supchuck
- d918e5c: Updated phinx to just use the test user and password from wercker
- fd6b535: Starting deploy testing
- a5e04d8: bad commit
- 7037ad7: Removing package pipeline
- 5e3d33b: PHPCS Fixes for updated PSR2 Errata Updted Docker container to not be broken     for wercker
- d4dde81: Updated config to deny me the right to remove my own user. This is why Donald Trump is winning all the primaries
- 187bfc6: Updated Entities to return correct hal links
- 08697c2: Added listener to add templated find links to collections
- c2d3ee6: Updating friend listener to allow suggest friends to be approved
- b364849: Updated container to include the dev packages Updated wercker to no longer include the dev packages for build
- f282d49: no more dire caching
- 8be65a1: Adding back in composer caching
- 65e3b0d: Removing sanity
- 6efc8f4: Found issue that was causing seg fault Switched datasets to use arrays instead of xml (libxml for php is buggy) Updated php docker file to have a nice entry point to keep the container running Updated the DbUnitconnection trait to only have one instance of the dataset
- a2369c4: Well I fixed the issue with the multiple DB connections but that is still failing at phpunit Updated ApigilityTestCase to return back the body when an invalid code is returned
- ec4faa9: Updating Security Test to reflect adults being able to update code
- 34601f8: Adding Hal Link for reset user
- 05173fa: Broke up forgot and reset to save on error headaches Created Reset Resource Allowing neighbors to send reset code to each other
- ee498fb: Added User Hal Link Added test for Token Resource Updated Me Entity to add missing links for children
- a2a86e4: Updating wercker to no longer show colors and perform the mysql sanity check
- 2e3d960: UserEntity::$type is now idompontent
- e87522d: User Service Listener will now remove active user on fetch.all.users
- 8a0a007: removing check pipline
- 774e7d7: testing check pipline
- 95f0371: Adding check pipline
- 41d6cb7: Adding check pipline
- 9880cbc: Wercker fixes
- df70f82: More wercker testing
- e445c8d: More wercker testing
- b416b5a: More wercker testing
- 3c89ab1: More wercker testing
- f9be28f: More wercker testing
- f00fbb5: More wercker testing
- 5776006: More wercker testing
- 35a7320: More wercker testing
- c773025: More wercker testing
- e00ca8c: More wercker testing
- 5eb2c54: More wercker testing
- ca48ad3: More wercker testing
- 9d0c004: More wercker testing
- ebe0bfc: More wercker testing
- 9de42ad: More wercker testing
- 1434e2f: Missed some links
- 2cc29c2: Adding labels to hal links for UI
- 2483406: Readme fix config fix for phinx and wrecker added mysql to docker for testing
- c23b0d3: Fixed bad merge of permissions and roles for user images
- 989fd00: More wercker testing
- 177f809: More wercker testing
- 029e5c8: More wercker testing
- ee29e5a: More wercker testing
- d8108ff: Updated permissions for image uploadingCORE-665

- 82131d3: More wercker testing
- 6ec63e8: More wercker testing
- 26f956d: More wercker testing
- bf3771a: More wercker testing
- 3ba76ff: More wercker testing
- 131ee09: More wercker testing
- fc78662: More wercker testing
- 6db9fc5: More wercker testing
- e1d7e05: More wercker testing
- a231de6: More wercker testing
- 8f9cd1f: More wercker testing
- 8b81ba9: More wercker testing
- ba8ebc6: More wercker testing
- a7890a5: More wercker testing
- cb82e43: More wercker testing
- f00871d: More wercker testing
- 1bdcfdc: More wercker testing
- 4ac3f96: More wercker testing
- a33ca55: More wercker testing
- a49c474: Trying out the shell for wercker
- 9505980: More DB testing
- 4b1e74f: More DB testing
- edb066f: Updated to singular
- 7670aa2: Another hotfix for user flips
- c0fd61f: Updated Test Removed debug query from GameService
- d6d0a1e: Updated Image Listener to pass along approved flag Updated User Image Service to include the moderation status of the image UPdated User Image Delegator to re-throw the exception on error
- 89aebd1: Updated UserImageService to return the latest approved by default Updated UserResourceTest to include image testing
- e4e5915: HOTFIX for alias for games
- 2908393: Updated UserRouteListener to inject email from the user if the user being edited is a child.
- 0e13fc4: Adding User Flip Link
- 2b06868: Updated Route listeners to skip checks when OPTIONS is being used Fixed AuthAdapter to check deleted flag on user
- 0e4105e: Adjusted User Group Listener to inject Group Entity instead of an array Updated Import Listener to check permission for group
- 7447caa: Added Sorting to queries Fixed ImportNotifier test missing model and logger
- 0fa609d: Updated Password validator too check code for the logged in user Added the Rbac and AuthService Initializer for validators
- d06b5f2: Added Listener to change session data when a user is updated
- adaec55: Adding Jackie to the super seed
- c36c765: Updated Email template for import success Fixed some configs for running on CLI Added Image link to config
- fc35d71: More Docker/Wercker work
- 68b70b2: Fix for Origin Guard when Origin Header not sent (not sure how the ternary disapppeared Updated Asset Manager to cache apigility assets to make admin loading faster
- d3bea49: Adding Suggested Friends HalLink
- fce8f9f: Created Suggestion endpoint Created Suggestion Service Updated Query for UserGroupService::fetchAllForUser Fix in Origin Guard for case when Origin Header not passed
- 3250102: Updated DB tests to cache connection
- 2cc833d: Locked down friends endpoint Friend Validator now returns back error if users cannot be friends Attaching Route listener to Application instead of all events
- b932eb0: Tidy up the friend validator Fixed Null Prototype causing fatal error in FriendService More tests
- 9ecc81f: Updated AbstractDBTest To use the DB Test Trait (easier to bootstrap integration tests) Fixes for Friend Service to correctly update and remove friends
- 4a532fb: More Wrecker work
- 449ad1a: Hacked in Friend status will de-hackify over the weekend
- 77992f4: Updated friend link to take in friend_idCORE-598
CORE-558

- 85c1649: Fixed firend Service when fecthing friend for a user Fixed friend resource when deleting friend Fixed friend Service Delegator calling wrong methodCORE-558
CORE-598

- 9156ac1: Created AttachFriendValidator Created FriendRouteListenerCORE-558
CORE-598

- 6009bdc: Wired up Friend Resource Fixed NotFriendsException Fixed FriendService QueriesCORE-558
CORE-598

- 0bc7e3c: Added fectchFriendForUser to Friend ServiceCORE-558
CORE-598

- 5cde589: Created Friend EndpointCORE-558

- b4ed77e: Created Friend Service Delegator Created Factories for Friend ServiceCORE-558
CORE-589

- 8321a47: Updated UserGroupService to include Friends UserGroupService::fetchAllUsersForUser will now exclude the user that is passed inCORE-558
CORE-643
CORE-598

- d2817fd: Created Friends Module Created Friends Table MigrationCORE-558
CORE-598

- 0d89e4e: Revert "Adjusting build file but it is still failing"This reverts commit 2ba7c84afa831b4b793d353170c1bd2abb3e528e.

- 2ba7c84: Adjusting build file but it is still failing
- f410223: Adding wercker file as a test
- 988b823: Added Tests for fetchAll for User Resource Added Tests For Update for User Resource
- 6b2fc75: Created User GET integration Test Created Helpers for Setting up Test Fixtures and Making Calls through Apigility
- 7c1e97b: Adding test groups
- b916ee2: Re-ordered the listener priority Updated the permission for the role
- 7fc0eb6: Added Flip HAL Links CORE-233
- 2398a57: Updated Security Test to include flips CORE-233
- 2a86483: Created Flip User Endpoint CORE-233
- 09f5a8f: Created Flip API End Point Updated Local to allow zf-apigility routes to be open
- 5b1a9ec: Completed tests for Flip Service and Flip Delegator
- 5211091: Created Earned Flip Created Flip Seed Started Tests For Flips
- 3f8cb35: CORE-233Created Flip
Created Flip Service
Started Flip UserService
Created Migration For Flips

- c479a8b: Code Cleanup to close out release
- 932870e: Added HideEntityListener to the UserGroupService
- 1de0aee: Adding neighbor
- 719317c: Adjusted roles around a bit
- 7771acb: Added new view.all.users permission Added UserGruopService::fetchUsersForUser Created Listener to redirect to UserGroupService::fetchUsersForUser Removed Unused methods in UserGroupService
- d5dcad2: Updated UserEntity to return password link if it is Me
- 451cc03: Fixed All HalLinks being added for non-super users
- ba0dc1f: Added GroupServiceInterface to SM Fixed GroupServiceDelegator to call correct method Fixed query for the group service fetch groups for user Added Test for group service
- 5ffceae: Removed session Validators and it will not work with cross domain cookies
- e89bdbd: Fixed bad Role Added missing scope for Me role Fixes for OrgResource to fetch orgs for user correctly
- 0bd3c80: Fix bad Merge BAD MANCHUCK Don't merge like this again
- 4364967: Fixed RBAC test with new permissions
- 66897dc: Fixed UserGroup Test Fixed FetchUsersForOrg since that is a different query
- ad02396: Fxied User Group Service to correctly descend down the tree fro the users Created a test for the UserGroupSerivce
- 1786b9c: Updated Scope listener to use SecurityGroupService
- 1bea12b: Got the permissions all sorted out
- 3194478: Adding DBUnit to allow integration tests Updated configs to use ::class constant to help with refactoring Some Code Cleanup Created SecurityGroupService to get the realtional role between 2 users Created Integration Tests For DB
- 6dfec10: Updated Group to use head and tail instead of lft and rgt Updated Dev seeder to use static UUID to prevent duplicate entriies Fixed failing Tests
- a5c2617: Updated username listener to set numbers when updating user
- db9dd7e: Added Hal Link for username
- 45d5df9: Added Hal Link for username
- 5951a87: Updated seperator to be dash instead of underscore
- f7f92cf: Updated GroupRouteListner to require the security user have the group types set Some Code Cleanup Me entity will now get injected with all group types for an organization the user belongs too
- 9dc2588: One last missed bad join
- 3d97f20: Updated Group to use MetaDataTrait
- 62dcbcf: Fixed timing on listeners Fixed Joins with paginators Listeners now catch ChangePasswordException when they need too
- de75d19: Ensuring that CORS listener runs
- 0c93c7b: Adding some logging to the CORS Guard
- c224635: Updated OriginGuard to listen on dispatch error as wells
- eb2b90f: Adjusted name list
- deaef67: Found bad setting for the last_seen
- 57b9ded: Adding Logged in time logging
- 40c898e: Fixed Failing test for new super user scope Updated Box file to run all seeders
- 93de4a6: Removed ResetPassWordListenerTest from Integration Test
- ccd05fe: Made Name Seed smarter
- 85e23e8: Updated development config
- e6dfa76: Created test for Reset Password Listener Created OpenRoute Trait to help listeners with common restriction checking Created Test DB to allow for integration Testing Updated Seeds to no longer error when duplicate indexes Updated SecurityConfig to use ::class magic constant to help when refactoring Implemented own Authention Service to Throw change password exception when asking for identity Fixes for Guards to handel new traits Removed Reset Password Guard since Auth Service will now throw Change password Exception when asking for identity
- 2cd1c21: Updated scope for me for super
- b6bb886: Updated domains for cookies
- 19ef19f: Removed old Reset Entity Updated Change Password Listener to use the exception message
- d43cd7f: Adding some logging to the auth expire listener
- d049d98: Updated exception code for reset password
- 9fdc372: Updated UserService and Delegator to re-throw exception Updated Resete Password guard to throw exception rather then set error on MVC event
- 7839c58: Found the fraking issue with the XSRF Guard.   One line of code was neeeded! ONE LINE!!!@!!!1 One line caused all that headache with the   mis-matched token ONE LINE !!!!!!!Created new listener to clear out the logged in identity if the time difference is more then ten min

- 411c7a5: Added TTL to redis for session Updated XSRF guard to only check on closed routes to avoid logout causing all these issues Updated puphpet to no longer created default vhost
- 58d13e3: Created User Image Listener to embed approved user images
- 16ac775: Logging when cloudinary is calling the web hook
- a53ea24: XsrfGuard now runs later to allow 404s Created Error Listener to record errors
- 3974617: Added Logging to XSRF and CSRF guards XSRF not listens to finish and route events instead of finish event Added Logging to Route Auth Listener
- 05a8cf6: Updated rollbar writer to include the user information Added logging to the auth adapter Added logging to the image resource
- b12c3fc: Added rollbar integration
- 90224c5: Updated vagrant for cloudinary secret Updated Image to no longer show not found when image not moderated Image Resource now fails gracefully if the cloudinary headers are not set
- 3e5539d: setting coming soon to be bool
- 25b8f5c: Added missing role for child testing
- 77fffad: Updated Child to allow taking in new user name Updated StaticName Generator to add more variety Created User Name Resource Add new Permission "pick.username" Created new Role child and security user will return this role when requested
- c93f9e3: Added coming soon to be retuned with game hydration
- 49c0c5e: Added FetchOrgTypes Added FetchGroupTypes Added new role: view.all.groups Created SuperMeListener to attach all group and org hal links based on type Fixed Seeders when new data needs to be inserted
- ce26747: Added Static type to allow labels to be returned correctly Fixed UserGroupDelegator to call getOrgTypesForUserCorrectly Updated UserGroupListener to no longer set the label for the hal links Updated Group and Org Hal links to set the label from StaticType
- 8dd2bee: Changed timeout for CSRF and Session to be 10 min
- 2ef3373: Adjusting BoxFile for build
- d13c282: Updated super admin to have 3 for me updated user entity and me entity to be scope aware
- 0914d79: Removed Sibling roles and made config easier to read Created Test for RBac setting roles/scope correctly
- 3096067: Updated super seed for everyone in the company
- 5acc031: Updated userentity to be scope aware Added me entity to security config Rbac no longer tries to copy permissions from siblings
- ab72922: Updated Group resource to use a group instead of GroupEntity when updating
- 417087f: Updated default page_size to 100
- 472a6d8: Fixed failing tests after rebase lost them (for some reason that I will not waste a lot of time looking into
- 707ade4: Skipping tests for group service until group service refactor No longer stopping on skipped, Incomplete or Failing tests
- 9fc4daf: Adding parent_id to group to make my life easy Group Resource will now append the parent to the groupentity
- 44e007f: Boxfile remove cached vendor
- 5a2d29b: another pagoda merge
- 87b7daf: Merge pagoda
- f567bd4: more cleaning
- 860c10b: Cleanup after rebase
- 6452c94: Updated names list
- 7308397: Styles Error/Warning Messages and Email templates
- e7a326d: Removed shared listeners that were upsetting Unit tests Fixed Alias for Org Module
- 4c648fe: XSRF Guard was not setting httpOnly correctly
- aa471f9: Route Listener now appends / to avoid the warning in the log
- 94458fc: Updated group link to take in org id updated Group resource to accept organization for query Added missing role view.all.groups to security config Updated Route Listener to set role as super when user is super
- ca75468: Create Org Route Listener to pre-load organization and attach hal links to OrgEntity Updated Org Resource to use the pre-loaded org Updated Org Service to be able to fetch group types for the org
- 4e16819: Updated Group Resource to attach the org to an org Entity
- c9554a0: Updated Group Service to include group type searching Updated Group Service to allow querying of child groups Updated Group resource to take in Parent and return child groups
- 1bfbc08: Swapped parent and node in getting role for user There is an issue with sibiling copying permissions from parents explicty add read group for group admins
- 902a82f: Updated User route listener to get role for the group Fixed Delegator to fetch users for the org not the group Added OrgUsersLink for OrgEntity Added new view.org.users permission
- 58e65a8: Maded type required for organizations
- 36c3f4d: Added permission for user to get group users
- 4e2989f: Clearing out master
- f371d6f: Updated User Resource to save a UserInterface instead of a UserEntity
- 7878b7a: Fixed Tests
- 406b96d: Updated Image for cloudinary Updated Image Resource to be webhook for cloudinary Created UserImageService and Delegator Fixed Config for the Image Service and UserImageService Fixed UserImageDelegator To call saveImageToUser correctly
- ad26ccf: Updated UserImageResource for new services
- 8e3b290: Added User Image Service
- 4b0a855: Updated forgot password email template
- 1eb001a: Wired up forgot endpoint
- 1a076e4: Styles Error/Warning Messages and Email templates
- 6b3f218: Removed shared listeners that were upsetting Unit tests Fixed Alias for Org Module
- 5fd1293: XSRF Guard was not setting httpOnly correctly
- 0b27114: Fixed Listener For attaching hal links to the me entity Moved functionality from security org service to user group service
- 6ff8901: Testing PHP Ini setting
- b6ce4e5: Route Listener now appends / to avoid the warning in the log
- 4ae5ba9: Updated group link to take in org id updated Group resource to accept organization for query Added missing role view.all.groups to security config Updated Route Listener to set role as super when user is super
- 437cc64: Create Org Route Listener to pre-load organization and attach hal links to OrgEntity Updated Org Resource to use the pre-loaded org Updated Org Service to be able to fetch group types for the org
- b66f561: Updated Group Resource to attach the org to an org Entity
- 596a555: Updated Group Service to include group type searching Updated Group Service to allow querying of child groups Updated Group resource to take in Parent and return child groups
- cd39701: Swapped parent and node in getting role for user There is an issue with sibiling copying permissions from parents explicty add read group for group admins
- 1f8c4e7: Updated User route listener to get role for the group Fixed Delegator to fetch users for the org not the group Added OrgUsersLink for OrgEntity Added new view.org.users permission
- acc4887: Updated Scope for super users to be -1 Started Tests For Rbac
- a402159: Maded type required for organizations
- fbfa5da: Added permission for user to get group users
- 680adf5: Added User Group Listener that will only fecth groups for the user when user is not allowed to see all groups
- c983af7: Updated group resource to take type for query Updated GroupDelegator to correctly build where
- 89e0639: Updated UserRoute Listener to be a shared listener Removed fetching orgs from security service as it is now moved to userGroupService Updated User Entity and Me Entity for new orgainization returns
- eac6661: Refactoring UserRouteService To Use the shared listener Created Get Groups for user in user group service
- 212e300: Removing excited
- 9d3083b: Changed 401 to 403 when not authorized
- 270a4ea: Added comming soon to games
- 9e11d6e: editing coming soon
- 6031453: adding more games to the games seeder
- fb4d013: Updated Listeners to load from config Correct Rbac Bits
- e2fbc82: Updated CSRF check to also use post var if header not set
- 625426e: Created SecurityServicesListener that will allow configuring listeners from the config Created RbacAwareInitializer to keep factories down for some listeners Created AuthServiceAwareInitializer to keep factories down Created Org Service Listener that will call fetch orgs for user when user is not allowed to view all orgs OrgService now has fetchOrgsForUser Defined HideEntityListner now accepts table alias in constructor when services are doing some big joins
- d531515: Updated OrgService to fetch Orgs for a User
- 5697803: Updated DevSeed to set type for group added Updated UserEntity to return default links for user Updated Me to pull links from userEntity
