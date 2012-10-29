Feature: Interactions between shops on a purchase

    Scenario: Succussful purchase
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
         When The Customer checks out
         Then The customer will receive the product

    Scenario: Product is not available any more in remote shop
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
          And The Product is not available in remote shop
         When The Customer views the order overview
         Then The customer is informed about the unavailability

    Scenario: Product is not available any more in remote shop
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
          And The Product price has changed in the remote shop
         When The Customer views the order overview
         Then The customer is informed about the changed price

    Scenario: Product is not available any more in remote shop
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
         When The Customer views the order overview
         Then The product is reserved in the remote shop

    Scenario: The Buy process fails
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
         When The customer completes the checkout
          And The buy process fails
         Then The customer is informed about this.
            # This are actually two tests:
            #  * The preCommit fails
            #  * The doCommit fails

    Scenario: The Buy succeeds and everything is logged
        Given The Product is listed as available
          And A customer adds a product from a remote shop to basket
         When The customer completes the checkout
         Then The local shop logs the transaction with Mosaic
          And The remote shop logs the transaction with Mosaic

