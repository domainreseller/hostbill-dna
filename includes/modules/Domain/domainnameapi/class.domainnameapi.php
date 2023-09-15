<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name hostbill_dna
 * 4.12.2020 23:01
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */
/** @noinspection PhpUndefinedClassInspection */

require_once __DIR__.'/lib/dna.php';

class domainnameapi extends DomainModule{

    protected $version     = '1.0.52';
    protected $modname     = "Domain Name Api";
    protected $description = 'Domain Name API - ICANN Accredited Domain Registrar from TURKEY ';

    /**
     * @var null DomainNameAPI_PHPLibrary
     */
    private   $dna         = null;


    protected $configuration = [
        'username' => [
            'value'   => '',
            'type'    => 'input',
            'default' => false
        ],
        'password' => [
            'value'   => '',
            'type'    => 'password',
            'default' => false
        ],
        'testmode' => [
            'value'   => '0',
            'type'    => 'check',
            'default' => '0'
        ]
    ];

    protected $lang = [
        'english' => [
            'username' => 'User Name',
            'password' => 'Password',
            'testmode' => 'Use Test Mode',
        ],
        'turkish' => [
            'username' => 'Kullanıcı Adı',
            'password' => 'Şifre',
            'testmode' => 'Test Modu',
        ]
    ];

    protected $commands = [
        'Register',
        'Transfer',
        'Renew',
        'ContactInfo',
        'RegisterNameServers',
        'EppCode'
    ];

    protected $clientCommands = [
        'ContactInfo',
        'RegisterNameServers',
        'EppCode'
    ];


    /**
     * @return DomainNameAPI_PHPLibrary
     */
    private function dna(){

        $testmode = false;
        if ($this->configuration['testmode']['value'] == '1'){
            $testmode=true;
        }

        if($this->dna==null){
            $this->dna = new \DomainNameApi\DomainNameAPI_PHPLibrary($this->configuration['username']['value'],$this->configuration['password']['value'],$testmode);
        }

        return $this->dna;
    }


         /**
     * Checking connection
     * Test:1/1
     * @return bool
     */
    public function testConnection() {

        $result = $this->dna()->GetCurrentBalance();

        if($result["result"] == "OK"){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Register domain name, using
     * $this->configuration - configuration/connection details for this registrars
     * $this->options - domain registration options array with keys:
     * - ns1,ns2,ns3... nameservers to use
     * - numyears - period to register for
     * - ext - domain extended attributes (required by some tlds)
     *
     * $this->domain_contacts - domain contacts array with keys:
     *  - registrant - array with registrant details
     *  - admin - array with admin person details
     *  - billing - array with billing person details
     *  - tech - array with tech person details
     *
     * @return bool
     * @test 15/15
     */
    public function Register() {

        $nameservers=[];
        $contacts=[];
        $period =$this->options['numyears'];
        $privacy=false;
        $domain = $this->options['sld'].'.'.$this->options['tld'];
        $idprotection=false;


        foreach (range(1,5) as $k => $v) {
            $_ns = trim(strtolower($this->options["ns{$v}"]));
            if(strlen($_ns)>0){
                $nameservers[]=$_ns;
            }
        }

        $contacts = [
            "Administrative" => $this->_makeContact($this->domain_contacts['admin']),
            "Billing"        => $this->_makeContact($this->domain_contacts['billing']),
            "Technical"      => $this->_makeContact($this->domain_contacts['tech']),
            "Registrant"     => $this->_makeContact($this->domain_contacts['registrant']),
        ];


        $result = $this->dna()->RegisterWithContactInfo($domain,$period,$contacts,$nameservers,$idprotection,$privacy);

        if ($result["result"] == "OK") {

            if (!$this->addDomain('Active')) {
                $this->addError('An error occured while adding domain to DB');
            }

            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Domain Register',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));
        }

    }


    /**
     * Transfer domain name, using
     * $this->configuration - configuration/connection details for this registrars
     * $this->options - domain registration options array with keys:
     * - ns1,ns2,ns3... nameservers to use
     * - numyears - period to register for
     * - ext - domain extended attributes (required by some tlds)
     * - epp_code - domain EPP/transfer code
     *
     * $this->domain_contacts - domain contacts array with keys:
     *  - registrant - array with registrant details
     *  - admin - array with admin person details
     *  - billing - array with billing person details
     *  - tech - array with tech person details
     *
     * @return bool
     * @test 5/5
     */
    public function Transfer() {

        $domain = $this->options['sld'] . '.' . $this->options['tld'];
        $epp    = $this->options['epp_code'];
        $year   = $this->options['numyears'];

         $result = $this->dna()->Transfer($domain,$epp);

        if ($result["result"] == "OK") {

             $this->addDomain('Pending Transfer');
            $this->addInfo('Action succedded. Transfer initialized');

            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Domain Transfer',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));
        }


    }


    /**
     * Renew registered domain name, using
     * $this->configuration - configuration/connection details for this registrars
     * $this->options - domain registration options array with keys:
     * - ns1,ns2,ns3... nameservers to use
     * - numyears - period to register for
     * - ext - domain extended attributes (required by some tlds)
     *
     * $this->domain_contacts - domain contacts array with keys:
     *  - registrant - array with registrant details
     *  - admin - array with admin person details
     *  - billing - array with billing person details
     *  - tech - array with tech person details
     *
     * @return bool
     * @test 6/6
     */
    public function Renew() {

        $domain = $this->options['sld'] . '.' . $this->options['tld'];
        $epp    = $this->options['epp_code'];
        $year   = $this->options['numyears'];

         $result = $this->dna()->Renew($domain,$year);

        if ($result["result"] == "OK") {

            $this->addPeriod();
            $this->addInfo('Renew: succeed');
            return true;

            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Domain Renew',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));
        }
    }

    /**
     * Return array of nameservers registered for domain stored in
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return array|bool
     * @test 7/6
     * @subtrimal  phonenumber_prefix
     */
    public function getNameServers() {

        $result =$this->dna()->GetDetails($this->options['sld'].'.'.$this->options['tld']);

        if ($result["result"] == "OK") {
            $values = [];

            if (is_array($result["data"]["NameServers"])) {
                foreach ([0, 1, 2, 3, 4] as $k => $v) {
                    if (isset($result["data"]["NameServers"][$v])) {
                        $values[$v] = $result["data"]["NameServers"][$v];
                    }
                }
            } else {
                // Only one nameserver
                if (isset($result["data"]["NameServers"])) {
                    $values[1] = $result["data"]["NameServers"];
                }
            }
            return $values;
        } else {

            $this->addError('Can not access nameservers');
            return false;

        }


    }



    /**
     * Update domain nameservers:
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * $this->options['ns*'] - domain nameserver to save (ns1,ns2,ns3...)
     * @return bool
     * @test 4/4
     */
    public function updateNameServers() {

        $nsList = array();
        foreach (range(1, 5) as $k => $v) {
            if (strlen(trim($this->options["ns1"])) > 0) {
                $nsList[] = $this->options["ns{$v}"];
            }
        }

        // Process request
        $result = $this->dna()->ModifyNameserver($this->options['sld'] . '.' . $this->options['tld'], $nsList);

        if ($result["result"] == "OK") {
            $this->addInfo('Update Name Servers: Success');
            $this->logAction(array(
                'action' => 'Update Name Servers',
                'result' => true,
                'change' => $nsList,
                'error'  => false
            ));
            return true;
        } else {
            $this->logAction(array(
                'action' => 'Update Name Servers',
                'result' => false,
                'change' => $nsList,
                'error'  => $result["error"]["Message"] . ' - ' . $result["error"]["Details"]
            ));
            return false;
        }

    }


    /**
     * Ger array of contact information assigned to doman name, to display in contact update form
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return array|bool
     * @test 12/12
     */
    public function getContactInfo() {

        $result =$this->dna()->GetContacts($this->options['sld'].'.'.$this->options['tld']);


        $contact = [
            'registrant' => $this->_parseContact('Registrant', $result["data"]["contacts"]),
            'tech'       => $this->_parseContact('Technical', $result["data"]["contacts"]),
            'admin'      => $this->_parseContact('Administrative', $result["data"]["contacts"]),
            'billing'    => $this->_parseContact('Billing', $result["data"]["contacts"]),

        ];
        return $contact;

    }

    /**
     * Update domain contact informations related to domain name
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * $this->options also holds keys of submitted values:
     *  - registrant - array with registrant details
     *  - admin - array with admin person details
     *  - billing - array with billing person details
     *  - tech - array with tech person details
     * @return bool
     * @test 12/12
     */
    public function updateContactInfo() {

        $updateArr = [
            "Administrative" => $this->_makeContact($this->options['admin']),
            "Billing"        => $this->_makeContact($this->options['billing']),
            "Technical"      => $this->_makeContact($this->options['tech']),
            "Registrant"     => $this->_makeContact($this->options['registrant']),
        ];

        $result = $this->dna()->SaveContacts($this->options['sld'] . '.' . $this->options['tld'], $updateArr);


        if ($result["result"] == "OK") {
            $this->logAction(array(
                'action' => 'Update Contact Info',
                'result' => true,
                'change' => $updateArr,
                'error'  => false
            ));

            $this->addInfo('Contact Info has been updated');
            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Update Contact Info',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));


            return false;
        }


    }

    /**
     * Return status of domain registrar lock protection
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return bool|int 1|0
     * @test 4/4
     */
    public function getRegistrarLock() {

         $result = $this->dna()->GetDetails($this->options['sld'] . '.' . $this->options['tld']);


        if ($result["result"] == "OK") {

            if (isset($result["data"]["LockStatus"])) {

                $this->logAction(array(
                    'action' => 'Get Registrar Lock',
                    'result' => true,
                    'change' => false,
                    'error'  => false
                ));

                return $result["data"]["LockStatus"] == "true"?'1':'0';


            } else {

                $error = "EPP Code can not reveived from registrar!";

                $this->addError($error);

                $this->logAction(array(
                    'action' => 'Get Registrar Lock',
                    'result' => false,
                    'change' => false,
                    'error'  => $error
                ));

                return false;
            }

        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Get Registrar Lock',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;


        }
    }


    /**
     * Update status of domain registrar lock protection
     *
     * $this->options['registrarLock'] - submitted registrar lock value (1 or 0)
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return bool
     * @test 7/5
     * @todo Same registrar lock status will be check on demand
     * @todo unsaved statuses will be throw
     */
    public function updateRegistrarLock() {


         $result = $this->dna()->GetDetails($this->options['sld'] . '.' . $this->options['tld']);


        if ($result["result"] == "OK") {

            if (isset($result["data"]["LockStatus"])) {

                $result2 = false;
                if ($this->options['registrarLock'] == 1){
                  $result2 = $this->dna()->EnableTheftProtectionLock($this->options['sld'] . '.' . $this->options['tld']);
                }else{
                 $result2 =  $this->dna()->DisableTheftProtectionLock($this->options['sld'] . '.' . $this->options['tld']);
                }

                 if ($result2["result"] == "OK") {

                    $this->logAction(array(
                        'action' => 'Update Registrar Lock',
                        'result' => true,
                        'change' => false,
                        'error'  => false
                    ));

                    return true;


                } else {

                    $error = $result2["error"]["Message"] . " - " . $result2["error"]["Details"];

                    $this->addError($error);

                    $this->logAction(array(
                        'action' => 'Update Registrar Lock',
                        'result' => false,
                        'change' => false,
                        'error'  => $error
                    ));

                    return false;
                }


            } else {

                $error = "EPP Code can not reveived from registrar!";

                $this->addError($error);

                $this->logAction(array(
                    'action' => 'Update Registrar Lock',
                    'result' => false,
                    'change' => false,
                    'error'  => $error
                ));

                return false;
            }

        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Update Registrar Lock',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;


        }


    }


    /**
     * Register custom Name Server at registrar
     * Note: Not all registrars offer this feature
     * var_dump($this->options) to examine what has been submitted from user form
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return array|bool
     * @test 5/5
     */
    public function registerNameServer() {

        $_d = $this->options['sld'] . '.' . $this->options['tld'];

        $result = $this->dna()->AddChildNameServer($_d, $this->options['NameServer'].'.'.$_d, array($this->options['NameServerIP']));


        if ($result["result"] == "OK") {
            $this->logAction(array(
                'action' => 'Register Nameserver',
                'result' => true,
                'change' => false,
                'error'  => false
            ));

            return true;
        } else {
            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Register Nameserver',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;
        }

    }


    /**
     * Update custom Name Server IP at registrar
     * Note: Not all registrars offer this feature
     * var_dump($this->options) to examine what has been submitted from user form
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return array|bool
     * @test 4/4
     */
    public function modifyNameServer() {

         $_d = $this->options['sld'] . '.' . $this->options['tld'];

         $result = $this->dna()->ModifyChildNameServer($_d, $this->options['NameServer'].'.'.$_d, array($this->options['NameServerNewIP']));



        if ($result["result"] == "OK") {
            $this->logAction(array(
                'action' => 'Modify Nameserver',
                'result' => true,
                'change' => false,
                'error'  => false
            ));

            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Modify Nameserver',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;
        }

    }

    /**
     * Delete Name Server from registrar
     * Note: Not all registrars offer this feature
     * var_dump($this->options) to examine what has been submitted from user form
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     * @return array|bool
     * @test 2/2
     */
    public function deleteNameServer() {


         $_d = $this->options['sld'] . '.' . $this->options['tld'];
         $result = $this->dna()->DeleteChildNameServer($_d, $this->options['NameServer'].'.'.$_d);

        if ($result["result"] == "OK") {
            $this->logAction(array(
                'action' => 'Delete Nameserver',
                'result' => true,
                'change' => false,
                'error'  => false
            ));

            return true;
        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Delete Nameserver',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;
        }
    }


    /**
     * Return domain EPP/Security code. This code will be sent to customer by HostBill by email.
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     *
     * @return string|bool Return false if failed.
     * @test 3/3
     */
    public function getEppCode() {

         $result = $this->dna()->GetDetails($this->options['sld'] . '.' . $this->options['tld']);


        if ($result["result"] == "OK") {

            if (isset($result["data"]["AuthCode"])) {

                $this->logAction(array(
                    'action' => 'Get Epp Code',
                    'result' => true,
                    'change' => false,
                    'error'  => false
                ));

                return $result["data"]["AuthCode"];


            } else {

                $error = "EPP Code can not reveived from registrar!";

                $this->addError($error);

                $this->logAction(array(
                    'action' => 'Get Epp Code',
                    'result' => false,
                    'change' => false,
                    'error'  => $error
                ));

                return false;
            }

        } else {

            $error = $result["error"]["Message"] . " - " . $result["error"]["Details"];

            $this->addError($error);

            $this->logAction(array(
                'action' => 'Get Epp Code',
                'result' => false,
                'change' => false,
                'error'  => $error
            ));

            return false;


        }


    }


    /**
     *
     * Get domain details stored at registrar to synchronize those stored in HostBill db.
     *
     * $this->options['sld'] - domain name (without extension/tld)
     * $this->options['tld'] - domain extension/tld
     *
     * @return array|bool
     * returned array CAN contain keys:
     *  - expires - date in Y-m-d format when domain will expire
     *  - status - d//rar in one of hb_domains.status value (Active,Expired,Pending Registration, Pending Transfer)
     *  - reglock - status of domain registrar lock
     *  - ns - array of nameservers for this domain name
     *  - idprotection - status of idprotection feature for this domain
     * @test 3/3
     */
    public function synchInfo() {

        $result = $this->dna()->GetDetails($this->options['sld'] . '.' . $this->options['tld']);

        $resp = [
            'status'       => 'Active',
            'expires'      => date('Y-m-d', strtotime($result['data']['Dates']['Expiration'])),
            'reglock'      => $result['data']['LockStatus'] == 'true',
            'ns'           => $result['date']['NameServers'][0],
            'idprotection' => $result['data']['PrivacyProtectionStatus'] == true
        ];

        if(in_array($result['data']['Status'],['WaitingForRegistration', 'WaitingForDocument', 'ConfirmationEmailSend', 'PreRegistration', 'PendingHold'])){
            $resp['status']='Pending Registration';
        }
        if(in_array($result['data']['Status'],['Deleted','TransferredOut','PendingDelete','WaitingForOutgoingTransfer'])){
            $resp['status']='Expired';
        }
        if(in_array($result['data']['Status'],['WaitingForIncomingTransfer'])){
            $resp['status']='Pending Transfer';
        }

        return $resp;

    }

    /**
     * This is private parsing function. No handler on hostbill
     * @param $cdata
     * @return array
     */
    private function _makeContact($cdata) {

        $contact = [
            "FirstName"        => mb_convert_encoding($cdata['firstname'], 'UTF-8', 'auto'),
            "LastName"         => mb_convert_encoding($cdata['lastname'], "UTF-8", "auto"),
            "Company"          => mb_convert_encoding($cdata['companyname'], "UTF-8", "auto"),
            "EMail"            => mb_convert_encoding($cdata['email'], "UTF-8", "auto"),
            "AddressLine1"     => mb_convert_encoding($cdata['address1'], "UTF-8", "auto"),
            "AddressLine2"     => mb_convert_encoding($cdata['address2'], "UTF-8", "auto"),
            "AddressLine3"     => mb_convert_encoding('', "UTF-8", "auto"),
            "City"             => mb_convert_encoding($cdata['city'], "UTF-8", "auto"),
            "Country"          => mb_convert_encoding($cdata['country'], "UTF-8", "auto"),
            "Fax"              => mb_convert_encoding('', "UTF-8", "auto"),
            "FaxCountryCode"   => mb_convert_encoding('', "UTF-8", "auto"),
            "Phone"            => mb_convert_encoding($cdata['phonenumber'], "UTF-8", "auto"),
            "PhoneCountryCode" => mb_convert_encoding('90', "UTF-8", "auto"),
            "Type"             => mb_convert_encoding("Contact", "UTF-8", "auto"),
            "ZipCode"          => mb_convert_encoding($cdata['postcode'], "UTF-8", "auto"),
            "State"            => mb_convert_encoding($cdata['state'], "UTF-8", "auto")
        ];


        return $contact;
    }


    /**
     * This is private parsing function. No handler on hostbill
     * @param $type
     * @param $data
     * @return array
     */
    private function _parseContact($type,$data){
        $contact = [
            'firstname'   => $data[$type]["FirstName"],
            'lastname'    => $data[$type]["LastName"],
            'companyname' => $data[$type]["Company"],
            'email'       => $data[$type]["EMail"],
            'address1'    => $data[$type]["Address"]["Line1"],
            'address2'    => $data[$type]["Address"]["Line2"],
            'city'        => $data[$type]["Address"]["City"],
            'state'       => $data[$type]["Address"]["State"],
            'postcode'    => $data[$type]["Address"]["ZipCode"],
            'country'     => $data[$type]["Address"]["Country"],
        ];
        $contact['phonenumber']='+'.$data[$type]['Phone']['Phone']['CountryCode'].'.'.$data[$type]['Phone']['Phone']['Number'];

        return $contact;
    }



}
