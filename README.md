# bepado SDK

This package contains the SDK for bepado - the Vending Network, http://www.bepado.de

To request an account for testing the SDK in integration with bepado send an
e-mail to `bepado@shopware.com`.

The license of this SDK is [MIT](https://github.com/ShopwareAG/bepado-sdk/tree/master/LICENSE).

## Installation

Via [Composer](http://getcomposer.org):

    {
        "require": {
            "bepado/sdk": "@stable"
        }
    }

Via ZIP, go to [Releases](https://github.com/ShopwareAG/bepado-sdk/releases)
and download the latest version.

## Documentation

See the `docs/` folder for the API documentation.

There is also a  for a [demo shop
implementation](https://github.com/QafooLabs/bepado-demo-shop) that may help
you get started.

## Support

You can open issues on this project or use your bepado Account to open a
feedback request from within the bepado SocialNetwork.

## Running the Tests

You can run the testsuite through Ant Build Commons, to do this call
from the root directory:

    $ git submodule init --update
    $ ant verify

You might need to create a file `build.properties.local` with adjusted
database settings.

The testsuite is a combination of acceptance tests written in Behat and
Unit-tests written in PHPUnit.
