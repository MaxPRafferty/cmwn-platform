Code Style Guide
----------------

1. Follow PSR 1, 2, 3, 4, 6 and 7
1. PSR 13 will be followed us
1. For gateways, use fetch, fetchBy, save, attach, detach and delete ex: fetchByEmail, saveChannel, deleteProduct, attachUser
    1. Fetch MUST include the noun on singular and All using the plural with fetching multiple ex: fetchProduct, fetchAllAccounts, fetchAllActiveUsers
    1. FetchBy MUST include the noun ex: fetchByEmail, fetchByName
    1. Save, delete, attach and detach MUST include the noun ex: attachUser, saveJob, detachAccount
    1. If you need to update part of an document, use the term update and the singular noun
      Good: updateUserPassword, updateProductPrice
      Bad: updateUsersPassword, saveNewProductPrice
1. When Talking with 3rd party services, follow the rules for gateways however
    1. use import instead of fetch
    1. use send instead of save
    1. use remove instead of delete
1. Abstract classes MUST HAVE the first word be "Abstract" ex: AbstractFooClass
1. Interfaces MUST HAVE the last word be "Interface" ex: FooBarInterface
1. Traits MUST HAVE the last word be "Trait" ex: FooBarTrait
1. Events on object that will be series MUST be in the follow order: event.pre, event, event.post,  Events names MUST also
 be lowercase separated by dots
    ex: save.user.pre, save.user, save.user.post
1. Use @inheritDoc and keep all the documentation on the Trait or Interface
    
__Note: PSR-0 is being followed until ZF3 is stable and ready for use__ 