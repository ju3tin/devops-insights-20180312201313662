<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
/*
 * Handles the meter billing for a subscription after receiving 
 * the Pending Invoice Created event through webhook.
 */
class MeterBilling {
   /*
    * Close the pending invoice by adding the usage charge 
    * as well as addons if any used by the subscription.
    */
   function closePendingInvoice($invoiceObj) {

     
     $invoiceId =  $invoiceObj->id;
     $subscriptionId = $invoiceObj->subscriptionId;

     $invoiceDate = $invoiceObj->date;
     

     
     $chargeInCents = MeterBilling::getUsageCharge($invoiceDate, $subscriptionId);
     $addChargeParam = array("amount" => $chargeInCents ,
	                         "description" =>"monthly usage");
     /*
      * Calling ChargeBee Add Charge Invoice API and add Charge to invoice 
      * based on the usage made by customer.
      */
     ChargeBee_Invoice::addCharge($invoiceId, $addChargeParam);
     
        
     
     $addonQuantity = MeterBilling::getQuantityUsed($invoiceDate, $subscriptionId);   
     $addAddonCharge = array("addonId"=>"wallpapers",
	                         "addonQuantity" => $addonQuantity); 
     
     /* 
      * Calling the ChargeBee Add Addon Charge Invoice API and add the no of 
      * addons used by customers to the invoice.
      */ 
     ChargeBee_Invoice::addAddonCharge($invoiceId,$addAddonCharge);    
     
        
        
     
     
     /*
      * Closing the invoice and Collecting the payment(if auto collection is on)
      * by calling the ChargeBee Collect Invoice API.
      */
     ChargeBee_Invoice::close($invoiceId);
             
}

  /*
   * This method returns the amount to be charged based 
   * on the usage made by a subscription till the specified date.
   * For demo purpose the charge is get by random number.
   */ 
  private static function getUsageCharge($date,$subscriptionId) {
   $random = rand(1,100000);
   return $random;
  }
  
  /*
   * This method returns the no of addons used by a subscription 
   * till the specified date.
   * For demo purpose using no of quantity is get by random number.
   */
  private static function getQuantityUsed($date,$subscriptionId) {
   $random = rand(1,10);
   return $random;
  }

}

?>


