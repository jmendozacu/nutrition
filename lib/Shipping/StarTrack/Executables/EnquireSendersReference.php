<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <?php
//     <document_root>/MyWebSite/EnquireSendersReference.php
//     Invoked by SendersReference.php
//     StarTrack
//     19 March 2012
//     Version 4.4

    require_once("eServices.php");     // Import the StarTrack PHP API - do not modify this file
    require_once("CustomerConnect.php");    // Import ConnectDetails class - customer to modify this class as required
    ?>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Enquire Sender's Reference</title>
    </head>

    <body>
        <h2>Selected Response Items (Up to 10 Consignments) </h2>

        <?php
// Set up for a connection to eServices
        $oConnect = new ConnectDetails();
        $connection = $oConnect->getConnectDetails();
        $oC = new STEeService();        // StarTrack eServices object
//	$oC = new startrackexpress\eservices\STEeService();	// *** If PHP V5.3 or later, uncomment this line and remove the line above ***
// First use searchConsignments to translate Sender's References to Consignment IDs
//Create the request, as per Request Schema described in eServices - Usage Guide.xls
        $parameters = array(
            'header' => array(
                'source' => 'TEAM',
                'accountNo' => $_POST['accountNo'],
                'userAccessKey' => $connection['userAccessKey']
            ),
            'choice' => array(
                'senderReferenceNumber' => explode(" ", $_POST['senderReferenceNumber']) // Allow for multiple Sender's References
            ),
            'despatchLocationCode' => explode(" ", $_POST['despatchLocationCode']), // Allow for multiple Despatch Location codes
            'pagination' => array(
                'maxRows' => 10, // Return only the first 10 rows
                // in this simple example
                'availableRows' => 0,
                'firstRow' => 1
            )
        );

// NOTE: Applications *must* validate all parameters passed (data type, mandatory fields non-null), as described in Readme.pdf,
//       or alerts are generated at StarTrack. Validation is omitted in this sample for reasons of clarity.

        $request = array('parameters' => $parameters);

        try {
            $response1 = $oC->invokeWebService($connection, 'searchConsignments', $request);   // $response1 is as per Response Schema
            // described in eServices - Usage Guide.xls.
            // Returned value is a stdClass object.
            // Faults to be handled as appropriate.
        } catch (SoapFault $e) {
            echo "<p>" . $e->detail->fault->fs_severity . "<br />";
            echo $e->faultstring . "<br />";
            echo $e->detail->fault->fs_message . "<br />";
            echo $e->detail->fault->fs_category . "<br />";
            echo $e->faultcode . "<br />" . "</p>";
            exit($e->detail->fault->fs_timestamp);
            //	Or if there is a higher caller:    throw new SoapFault($e->faultcode, $e->faultstring, $e->faultactor, $e->detail, $e->_name, $e->headerfault);	
        }

// Extract an array of consignment IDs from the response

        $consignments = $response1->consignmentSummary;           // Array of consignments
        $consignmentCount = count($consignments);            // Number of consignments
        for ($i = 0; $i < $consignmentCount; $i++) {
            $consignmentIDs[] = $consignments[$i]->id;           // Append consignment ID to array
        }

// Get details of the consignments

        $parameters = array(
            'header' => array(
                'source' => 'TEAM',
                'accountNo' => $_POST['accountNo'],
                'userAccessKey' => $connection['userAccessKey']
            ),
            'consignmentId' => $consignmentIDs
        );
        $request = array('parameters' => $parameters);

        try {
            $response2 = $oC->invokeWebService($connection, 'getConsignmentDetails', $request); // $response2 is as per Response Schema
            // described in eServices - Usage Guide.xls.
            // Returned value is a stdClass object.
            // Faults to be handled as appropriate.
        } catch (SoapFault $e) {
            echo "<p>" . $e->detail->fault->fs_severity . "<br />";
            echo $e->faultstring . "<br />";
            echo $e->detail->fault->fs_message . "<br />";
            echo $e->detail->fault->fs_category . "<br />";
            echo $e->faultcode . "<br />" . "</p>";
            exit($e->detail->fault->fs_timestamp);
            //	Or if there is a higher caller:    throw new SoapFault($e->faultcode, $e->faultstring, $e->faultactor, $e->detail, $e->_name, $e->headerfault);	
        }

        $consignments = $response2->consignment;
        $consignmentCount = count($consignments);
// Loop through consignments
        for ($i = 0; $i < $consignmentCount; $i++) {
            $consignment = $consignments[$i];  // The i-th consignment	
            // Retrieve items of interest from the response
            // Consignment ID:
            $consignmentId[$i] = $consignment->id;
            // Service Description:
            $serviceDescription[$i] = $oC->serviceDescription($consignment->serviceCode);
            // Despatch Date:
            $despatchDate[$i] = $consignment->despatchDate;
            // ETA Date:
            $etaDate[$i] = $consignment->etaDate;
            // Despatch Depot:
            $despatchDepotDescription[$i] = $oC->locationDescription($consignment->despatchDepot);
            // Delivery Depot:
            $deliveryDepotDescription[$i] = $oC->locationDescription($consignment->deliveryDepot);
            // Consignment Status:
            $consignmentStatus = $consignment->status;
            $consignmentStatusDescription[$i] = $oC->statusDescription($consignmentStatus, 'consignment', 'full');
            // Most Recently Seen Place and Time:
            $trackingEvents = $consignment->trackingEvents;  // Tracking events
            $trackingEvent = $trackingEvents[0];      // Most recent tracking event
            if (!is_null($trackingEvent)) {
                $scanningDepotDescription[$i] = $oC->locationDescription($trackingEvent->scanningDepot);
                $mostRecentScanDateTime[$i] = $trackingEvent->eventDateTime;
            } else {
                $scanningDepotDescription[$i] = "";
                $mostRecentScanDateTime[$i] = "";
            }

            // Sender Name:
            $senderName[$i] = $consignment->sender->name[0] . ', ' . $consignment->sender->name[1];
            //Sender Address:
            $address = $consignment->sender->contactDetails[0]->address;
            $addressLine = $address->addressLine;
            $senderAddress[$i] = $addressLine[0] . ' ' . $addressLine[1] . ' ' . $addressLine[2] . ' ' . $address->suburbOrLocation . ' ' . $address->state . ' ' . $address->postCode;
            $senderCountry = $address->country;
            if ($senderCountry != "") {
                $senderAddress[$i] .= ' ' . $senderCountry;
            }
            //Sender Phone:
            $senderPhone[$i] = $consignment->sender->contactDetails[0]->phoneNumber;
            // Receiver Name:
            $receiverName[$i] = $consignment->receiver->name[0] . ', ' . $consignment->receiver->name[1];
            // Receiver Address:
            $address = $consignment->receiver->contactDetails[0]->address;
            $addressLine = $address->addressLine;
            $receiverAddress[$i] = $addressLine[0] . ' ' . $addressLine[1] . ' ' . $addressLine[2] . ' ' . $address->suburbOrLocation . ' ' . $address->state . ' ' . $address->postCode;
            $receiverCountry = $address->country;
            if ($receiverCountry != "") {
                $receiverAddress[$i] .= ' ' . $receiverCountry;
            }
            //Receiver Phone:
            $receiverPhone[$i] = $consignment->receiver->contactDetails[0]->phoneNumber;
            // Receiver Mobile:
            $receiverMobile[$i] = $consignment->receiver->contactDetails[0]->mobileNumber;
            // Special Instructions:
            $specialInstructions[$i] = $consignment->specialInstructions[0] . ' ' . $consignment->specialInstructions[1];
            // Total Weight
            $totalWeight[$i] = $consignment->totalWeight . 'kg';

            // Proof of Delivery
            // Note: When delivery has been completed, getConsignmentDetails provides a POD signature image if the recipient 
            // signed on-screen. However currently a proportion of PODs are signed manually, not on-screen, and in that case
            // getConsignmentDetails cannot provide the signature image. Manual signature occurs primarily with agents. 
            // A complete POD solution for B2B use is not yet available.
            // Proof-of-Delivery Signatory:
            $signatory = $consignment->podSignatoryName;
            if (is_null($signatory)) {
                $podSignatory[$i] = "";
            } else {
                $podSignatory[$i] = $oC->substituteAnyPODSignatoryCode($signatory); // Usually $signatory is a real person's name but
                // it can be a defined code. If it is a code 
                //(e.g. *LAI), substitute a corresponding descriptive 
                // string (e.g. LEFT AS INSTRUCTED).
            }

            // Proof-of-Delivery Signature:	
            $podSignatureBase64 = $consignment->podSignature;
            if (is_null($podSignatureBase64)) {     // Is there an electronic signature?
                $podSignature[$i] = "";       // No
            } else {
                $podSignature[$i] = '<img src="data:image/png;base64,' . $podSignatureBase64 . '" alt="POD Signature" width="300" height="100">';
            }

            //Proof-of-Delivery Date:
            $podDateTime[$i] = "";

            $images = $consignment->image;
            $imageCount = count($images);    // Image may be POD or an attachment
            for ($k = 0; $k < $imageCount; $k++) {
                $image = $images[$k];
                if ($image->isPOD) {
                    $podDateTime[$i] = $image->creationDateTime;
                    break;
                }
            }

            // Carrier's Quality Control:
            $qualityControl[$i] = $oC->qualityControlDescription($consignment->publishedQcCode);
        }

        $html = new DynamicHtml();  // Utility class for dynamic html
        ?>

        <table width="600" border="1">

            <tr>
                <th scope="row" align="left">Consignment Id&nbsp;</th>
        <?php $html->addColumns($consignmentCount, $consignmentId); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Service&nbsp;</th>
        <?php $html->addColumns($consignmentCount, $serviceDescription); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Despatch Date&nbsp;</th>
        <?php $html->addColumns($consignmentCount, $despatchDate); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Expected Delivery Date&nbsp;</th>
        <?php $html->addColumns($consignmentCount, $etaDate); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Carrier Despatch Depot&nbsp;</th>
        <?php $html->addColumns($consignmentCount, $despatchDepotDescription); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Carrier Delivery Depot&nbsp;</th>
<?php $html->addColumns($consignmentCount, $deliveryDepotDescription); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Consignment Status&nbsp;</th>
<?php $html->addColumns($consignmentCount, $consignmentStatusDescription); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Most Recently Seen&nbsp;</th>
<?php $html->addColumns($consignmentCount, $scanningDepotDescription); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Date and Time Seen&nbsp;</th>
<?php $html->addColumns($consignmentCount, $mostRecentScanDateTime); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Sender&nbsp;</th>
<?php $html->addColumns($consignmentCount, $senderName); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Sender Address&nbsp;</th>
<?php $html->addColumns($consignmentCount, $senderAddress); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Sender Phone&nbsp;</th>
<?php $html->addColumns($consignmentCount, $senderPhone); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Receiver&nbsp;</th>
<?php $html->addColumns($consignmentCount, $receiverName); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Receiver Address&nbsp;</th>
<?php $html->addColumns($consignmentCount, $receiverAddress); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Receiver Phone&nbsp;</th>
<?php $html->addColumns($consignmentCount, $receiverPhone); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Receiver Mobile&nbsp;</th>
<?php $html->addColumns($consignmentCount, $receiverMobile); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Special Instructions&nbsp;</th>
<?php $html->addColumns($consignmentCount, $specialInstructions); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Total Weight&nbsp;</th>
<?php $html->addColumns($consignmentCount, $totalWeight); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Proof of Delivery Signatory&nbsp;</th>
<?php $html->addColumns($consignmentCount, $podSignatory); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Proof of Delivery Signature&nbsp;</th>
<?php $html->addColumns($consignmentCount, $podSignature); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Proof of Delivery Date&nbsp;</th>
<?php $html->addColumns($consignmentCount, $podDateTime); ?>
            </tr>

            <tr>
                <th scope="row" align="left">Carrier's Quality Control&nbsp;</th>
<?php $html->addColumns($consignmentCount, $qualityControl); ?>
            </tr>

        </table>

<?php
echo "<p>&nbsp;For information here is a dump of the searchConsignments response: </p>";
var_dump($response1);
echo "<p>&nbsp;For information here is a dump of the getConsignmentDetails response: </p>";
var_dump($response2);
?>

    </body>
</html>