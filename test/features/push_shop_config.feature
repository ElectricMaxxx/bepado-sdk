Feature: SDK receives shop configuration

    Scenario: SDK receives shop configuration
        Given The shop imports configuration from 1 other shop
         When Bepado synchronizes data with the shop
         Then The shop receives 1 shop configuration update

    Scenario: SDK receives shop configurations
        Given The shop imports configuration from 2 other shops
         When Bepado synchronizes data with the shop
         Then The shop receives 2 shop configuration updates
