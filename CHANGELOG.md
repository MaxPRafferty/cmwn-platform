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

0.2.2
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
