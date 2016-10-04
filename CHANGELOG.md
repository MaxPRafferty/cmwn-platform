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

=======
Version - 0.2.2
----

- 8da541a: Added missing token for build file
