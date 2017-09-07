<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");


$uri = $_SERVER["REQUEST_URI"];

if(endsWith(substr($uri,0,strpos($uri,"?")) , "/update")) {
   updateCardHostedPage();     
} else if(endsWith(substr($uri,0,strpos($uri,"?")),"/redirect_handler")) {
   redirectFromChargeBee();
} else {
   customError400();
}

/*
 * Redirects the customer to ChargeBee Update Card Hosted Page API.
 */
function updateCardHostedPage() {
   /*
    * Calling the ChargeBee Update Card Hosted Page API to update card for 
    * a customer by passing the particular customers' customer id.
    * 
    * Note : To use this API return url for Update Card API's page must be set.
    */
   
   $hostUrl = getHostUrl();
   $result = ChargeBee_HostedPage::updatePaymentMethod(
   	 array("customer"=> array("id"=>$_GET['customer_id']), 
        "embed"=>"false",
	    "redirectUrl"=> $hostUrl . "/update_payment_method/redirect_handler",
        "cancelUrl"=> $hostUrl . "/update_payment_method/profile?customer_id=".
					  				        urlencode($_GET['customer_id'])
	   ));
   
   
   $url = $result->hostedPage()->url;
   header("Location: $url");
   
}

/*
 * Handles the redirection from ChargeBee server.
 */
function redirectFromChargeBee(){
   /* The request will have hosted page id and state of the customer card
    * update status.
    */
   
   if("succeeded" == $_GET['state'] ) {
     /* 
      * Acknowledge the update payment method hosted page id passed in return URL. 
      * The response will have customer and their masked payment details.
      */
     $result = ChargeBee_HostedPage::acknowledge($_GET['id']);
     $hostedPage = $result->hostedPage();
     
     
     $customerId = $hostedPage->content()->customer()->id;
     $queryParameters = "customer_id=" . urlencode($customerId) . "&updated=" . urlencode("true");
     header("Location: profile?".$queryParameters);
     
   } else {
     header("HTTP/1.0 400 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }

}

?>
