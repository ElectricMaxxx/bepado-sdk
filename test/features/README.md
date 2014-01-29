# Behat tests

Rough inner workings description:

Through a DirectAccess ShopFactory and ShopGateway an SDK is created within the SDK
during the tests, so that both parts of the local+remote stack during a transaction
are working.
