Feature: Shipping costs are updated in SDK

    Scenario: Initial revision
         When The updater requests the shipping costs revision
         Then The shipping costs revision is ""

    Scenario: Push shipping costs to SDK
         When Shipping costs are pushed to the SDK for shop "1" with revision "123"
          And The updater requests the shipping costs revision
         Then The shipping costs revision is "123"

    Scenario: Push multiple shipping costs to SDK
         When Shipping costs are pushed to the SDK for shop "1" with revision "1234"
          And Shipping costs are pushed to the SDK for shop "2" with revision "123"
          And The updater requests the shipping costs revision
         Then The shipping costs revision is "1234"
