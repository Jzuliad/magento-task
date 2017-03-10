<?php

class Julia_GiftProduct_Model_Observer {

    public function Giftproduct($observer) {

        //Fetch the current event
        $event = $observer->getEvent();
        $product = $event->getProduct();

        //Get configurations
        $productGiftId = intval(Mage::getStoreConfig('giftproduct/giftproduct_group/product_id'));
        $subtotalLimit = intval(Mage::getStoreConfig('giftproduct/giftproduct_group/limit'));
        
        $productGift = new Mage_Catalog_Model_Product();
        $productGift->load($productGiftId);
        
        //Check if product exists
        if( !$productGift->getId() ) { return; }

        $quote = Mage::getSingleton('checkout/session')->getQuote();

        //Check if there is already gift product in shopping cart OR cart's total less than config total limit
        if ($quote->hasProductId($productGiftId) || ($quote->getSubtotal() + ($product->price) * ($product->qty)) < $subtotalLimit) {
            return;
        }

        //Set zero price and maximum gift quantity '1'
        $quote->addProduct($productGift, 1)
                ->setOriginalCustomPrice(0)
                ->setCustomPrice(0)
                ->setMaxSaleQty(1)
                ->setIsSuperMode(true);

        $quote->collectTotals()->save();

        //Add Custom message to shopping cart 
        $message = "You've got a gift!";
        Mage::getSingleton('checkout/session')->addSuccess($message);
    }
    

}
