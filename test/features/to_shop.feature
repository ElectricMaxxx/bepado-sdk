Feature: Product receives product updates from Mosaic

    Scenario: Initial import
        Given The shop did not synchronize any products
          And I configured the to-shop update interval to 100 products per hour
          And Mosaic has been configured to export 43 products
         When Export is triggered for the 1. time
         Then 43 updates are triggered

    Scenario: Chunked import
        Given The shop already synchronized 100 products
          And I configured the to-shop update interval to 100 products per hour
          And Mosaic has been configured to export 167 products
         When Export is triggered for the 2. time
         Then 67 updates are triggered

    Scenario: Chunked second import
        Given The shop did not synchronize any products
          And I configured the to-shop update interval to 100 products per hour
          And Mosaic has been configured to export 167 products
         When Export is triggered for the 1. time
         Then 100 updates are triggered

    Scenario: Product update
        Given The shop already synchronized 23 products
          And 15 products have been updated
         When Export is triggered for the 2. time
         Then 15 updates are triggered

    Scenario: Chunked product update
        Given The shop already synchronized 167 exported products
          And I configured the to-shop update interval to 100 products per hour
          And 123 products have been updated
         When Export is triggered for the 1. time
         Then 100 updates are triggered

    Scenario: Chunked second product update
        Given The shop already synchronized 167 exported products
          And I configured the to-shop update interval to 100 products per hour
          And 123 products have been updated
         When Export is triggered for the 2. time
         Then 23 updates are triggered

    Scenario: Product delete
        Given The shop already synchronized 23 exported products
          And 15 products have been deleted
         When Export is triggered for the 1. time
         Then 15 updates are triggered

    Scenario: Chunked product delete
        Given The shop already synchronized 167 exported products
          And I configured the to-shop update interval to 100 products per hour
          And 123 products have been deleted
         When Export is triggered for the 1. time
         Then 100 updates are triggered

    Scenario: Chunked second product delete
        Given The shop already synchronized 167 exported products
          And I configured the to-shop update interval to 100 products per hour
          And 123 products have been deleted
         When Export is triggered for the 2. time
         Then 23 updates are triggered
