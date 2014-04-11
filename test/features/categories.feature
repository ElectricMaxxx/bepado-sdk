Feature: Categories are updated in SDK

    Scenario: Initial revision is empty
        When the updater requests the last categories revision
        Then the categories revision is ""

    Scenario: Push Categories
        Given categories are pushed to the shop with revision "123"
         When the updater requests the last categories revision
         Then the categories revision is "123"

    Scenario: No ordering of categories by revision
        Given categories are pushed to the shop with revision "1234"
          And categories are pushed to the shop with revision "123"
         When the updater requests the last categories revision
         Then the categories revision is "123"
