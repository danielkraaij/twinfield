<?php
namespace Pronamic\Twinfield\Customer\DOM;

use \Pronamic\Twinfield\Customer\Customer;

/**
 * The Document Holder for making new XML customers. Is a child class
 * of DOMDocument and makes the required DOM tree for the interaction in
 * creating a new customer.
 * 
 * @package Pronamic\Twinfield
 * @subpackage Invoice\DOM
 * @author Leon Rowland <leon@rowland.nl>
 * @copyright (c) 2013, Pronamic
 */
class CustomersDocument extends \DOMDocument
{
    /**
     * Holds the <dimension> element 
     * that all additional elements should be a child of
     * @var \DOMElement
     */
    private $dimensionElement;

    /**
     * Creates the <dimension> element and adds it to the property
     * dimensionElement
     * 
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->dimensionElement = $this->createElement('dimension');
        $this->appendChild($this->dimensionElement);
    }

    /**
     * Turns a passed Customer class into the required markup for interacting
     * with Twinfield.
     * 
     * This method doesn't return anything, instead just adds the invoice to 
     * this DOMDOcument instance for submission usage.
     * 
     * @access public
     * @param \Pronamic\Twinfield\Customer\Customer $customer
     * @return void | [Adds to this instance]
     */
    public function addCustomer(Customer $customer)
    {
        // Elements and their associated methods for customer
        $customerTags = array(
            'code'      => 'getID',
            'name'      => 'getName',
            'type'      => 'getType',
            'website'   => 'getWebsite',
            'cocnumber' => 'getCocNumber'
        );
        
        // Go through each customer element and use the assigned method
        foreach($customerTags as $tag => $method ) {
            
            // Make text node for method value
            $node = $this->createTextNode($customer->$method());
            
            // Make the actual element and assign the node
            $element = $this->createElement($tag);
            $element->appendChild($node);
            
            // Add the full element
            $this->dimensionElement->appendChild($element);
        }
        
        // Check if the financial information should be supplied
        if($customer->getDueDays() > 0) {
            
            // Financial elements and their methods
            $financialsTags = array(
                'duedays'      => 'getDueDays',
                'payavailable' => 'getPayAvailable',
                'paycode'      => 'getPayCode',
                'vatcode'      => 'getVatCode',
                'ebilling'     => 'getEBilling',
                'ebillmail'    => 'getEBillMail'
            );
            
            // Make the financial element
            $financialElement = $this->createElement('financials');
            $this->dimensionElement->appendChild($financialElement);
            
            // Go through each financial element and use the assigned method
            foreach($financialsTags as $tag => $method) {
                
                // Make the text node for the method value
                $node = $this->createTextNode($customer->$method());
                
                // Make the actual element and assign the node
                $element = $this->createElement($tag);
                $element->appendChild($node);
                
                // Add the full element
                $financialElement->appendChild($element);
            }
        }
        
        // Address elements and their methods
        $addressTags = array(
            'name'      => 'getName',
            'country'   => 'getCountry',
            'city'      => 'getCity',
            'postcode'  => 'getPostcode',
            'telephone' => 'getTelephone',
            'telefax'   => 'getFax',
            'email'     => 'getEmail',
            'field1'    => 'getField1',
            'field2'    => 'getField2',
            'field3'    => 'getField3',
            'field4'    => 'getField4',
            'field5'    => 'getField5',
            'field6'    => 'getField6'
        );

        // Make address element
        $addressesElement = $this->createElement('addresses');
        $this->dimensionElement->appendChild($addressesElement);
        
        // Go through each address assigned to the customer
        foreach($customer->getAddresses() as $address) {
            
            // Makes new address element
            $addressElement = $this->createElement('address');
            $addressesElement->appendChild($addressElement);

            // Set attributes
            $addressElement->setAttribute('default', $address->getDefault());
            $addressElement->setAttribute('type', $address->getType());
            
            // Go through each address element and use the assigned method
            foreach($addressTags as $tag => $method) {
                
                // Make the text node for the method value
                $node = $this->createTextNode($address->$method());
                
                // Make the actual element and assign the text node
                $element = $this->createElement($tag);
                $element->appendChild($node);
                
                // Add the completed element
                $addressElement->appendChild($element);
            }
        }
    }
}
