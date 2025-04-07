<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name hostbill_dna
 * 4.12.2020 23:01
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */
/** @noinspection PhpUndefinedClassInspection */

/**
 * Domain Name API Module for HostBill
 * @author Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

require_once __DIR__.'/lib/dna.php';

class domainnameapi extends DomainModule implements  DomainModuleContacts, DomainModuleListing, DomainLookupInterface, DomainSuggestionsInterface, DomainPremiumInterface, DomainPriceImport{

    protected $version     = '1.1.8';
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
        'EppCode',
    ];

    protected $clientCommands = [
        'ContactInfo',
        'RegisterNameServers',
        'EppCode'
    ];


    /**
     * Initialize DNA API connection
     * @return DomainNameAPI_PHPLibrary
     */
    private function dna(){

        $testmode = false;
        if ($this->configuration['testmode']['value'] == '1'){
            $testmode=true;
        }

        if($this->dna==null){
            $this->dna = new \DomainNameApi\DomainNameAPI_PHPLibrary($this->configuration['username']['value'],$this->configuration['password']['value']);
        }

        return $this->dna;
    }


         /**
     * Test API connection
     * @return bool
     */
    public function testConnection() {

        $result = $this->dna()->GetCurrentBalance();



        if($result["ErrorCode"] == 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Register a new domain
     * @return bool
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

        if($this->details["idprotection"] == "1"){
            $idprotection=true;
        }

        $contacts = [
            "Administrative" => $this->_makeContact($this->domain_contacts['admin']),
            "Billing"        => $this->_makeContact($this->domain_contacts['billing']),
            "Technical"      => $this->_makeContact($this->domain_contacts['tech']),
            "Registrant"     => $this->_makeContact($this->domain_contacts['registrant']),
        ];


        $additional = [];

        if(substr($this->options["tld"], -2) == "tr" && isset($this->options["ext"]) && !empty($this->options["ext"])) {
            $registrantInfo = $this->_makeContact($this->domain_contacts['registrant']);
            $externalData = $this->options["ext"];
            $additional['TRABISDOMAINCATEGORY'] = $externalData['TRABISDOMAINCATEGORY'];
            $additional['TRABISCOUNTRYID']      = $registrantInfo['Country'] == "TR" ? 215 : 888;
            $additional['TRABISCOUNTRYNAME']    = $registrantInfo['Country'];
            $additional['TRABISCITYNAME']       = $registrantInfo['City'];
            $additional['TRABISCITIYID']        = 888;

            if(strlen($registrantInfo['Company'])>1){
                $additional['TRABISNAMESURNAME']=$externalData['TRABISNAMESURNAME'];
                $additional['TRABISCITIZIENID']=$externalData['TRABISCITIZIENID'];
            }else{
                $additional['TRABISORGANIZATION']=$externalData['TRABISORGANIZATION'];
                $additional['TRABISTAXOFFICE']=$externalData['TRABISTAXOFFICE'];
                $additional['TRABISTAXNUMBER']=$externalData['TRABISTAXNUMBER'];
            }
        }


        $result = $this->dna()->RegisterWithContactInfo($domain,$period,$contacts,$nameservers,$idprotection,$privacy,$additional);

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
     * Transfer domain to another registrar
     * @return bool
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
     * Renew domain registration
     * @return bool
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
     * Get domain nameservers
     * @return array|bool
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
     * Update domain nameservers
     * @return bool
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
     * Get domain contact information
     * @return array|bool
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
     * Update domain contact information
     * @return bool
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
     * Get domain registrar lock status
     * @return bool|int 1|0
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
     * Update domain registrar lock status
     * @return bool
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
     * Get registered nameservers
     * @return array|bool
     */
    public function getRegisterNameServers(){

            $domainDetail = $this->api->getDetails($this->name);

        if ($domainDetail["result"] != "OK") {

            return false;
        }


        $childNsList = $domainDetail["data"]["ChildNameServers"] ? $domainDetail["data"]["ChildNameServers"] : [];

        if ($childNsList) {
            $result = [];
            foreach ($childNsList as $v) {
                $result[str_replace("." . $this->name, "", $v["ns"])] = $v["ip"];
            }
            return $result;
        } else {
            return [];
        }

    }
    /**
     * Register custom nameserver
     * @return bool
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
     * Modify custom nameserver IP
     * @return bool
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
     * Delete custom nameserver
     * @return bool
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
     * Get domain EPP code
     * @return string|bool
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
     * Get domain details from registrar
     * @return array|bool
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
     * Check domain availability
     * @param string $sld
     * @param string $tld
     * @param array $settings
     * @return array
     */
    public function lookupDomain($sld, $tld, $settings = [])
    {
        $result = $this->dna()->CheckAvailability([$sld], [ltrim($tld,'.')], 1, "create");
        $response = ["available" => false];

        if (isset($result[0])) {
            $domain = $result[0];
            if (isset($domain['TLD'])) {
                if ($domain['Status'] == 'available') {
                    $response['available'] = true;
                } else {
                    $response['available'] = false;
                }

                if (isset($domain["IsFee"]) && $domain["IsFee"] == 1) {
                    $response['premium'] = [
                        'register' => number_format($domain["Price"], 2, '.', ''),
                        'currency' => $domain["Currency"]
                    ];
                    $response['available'] = true;
                }
            }
        }


        return $response;
    }
    /**
     * Get domain suggestions
     * @param string $sld
     * @param string $tld
     * @param array $settings
     * @return array
     */
    public function suggestDomains($sld, $tld, $settings = [])
    {
        $tlds=[];
        if(!empty($settings["tlds"])) {
            foreach ($settings["tlds"] as $k => $v) {
              $tlds[]=ltrim($v,'.');
            }
        }

        $result = $this->dna()->CheckAvailability([$sld], $tlds, 1, "create");
        $returned_domains = [];
        foreach ($result as $k => $domain) {
            if (isset($domain['TLD'])) {
                if ($domain['Status'] == 'available' && $domain['IsFee'] !== true) {
                    $returned_domains[] = $domain['DomainName'].'.'.$domain['TLD'];
                }
            }
        }

        file_put_contents(__DIR__.'/log.'.$sld.'.json', json_encode(['j'=>$tlds,'s'=>$result], JSON_PRETTY_PRINT));

        return $returned_domains;
    }

    /**
     * Get extended domain attributes
     * @return array
     */
    public function getExtendedAttributes()
    {
        if(!isset($this->name) || $this->name == "") {
            $this->name = $this->options["sld"] . "." . $this->options["tld"];
        }
        $attributes = [];

        if(substr($this->options["tld"], -2) == "tr") {
            $extension = "tr";

            $attributes[]= [
                "description" => "Domain Category",
                "name"        => "TRABISDOMAINCATEGORY",
                "type"        => "select",
                "option"      => [
                    0 => 'Company',
                    1 => 'Personal'
                ]
            ];

            $attributes[] = [
                "description" => "Citizen ID (Blank if Company)",
                "name"        => "TRABISCITIZIENID",
                "type"        => "input",
                "option"      => false
            ];
            $attributes[] = [
                "description" => "Personal name and surname (Blank if Company)",
                "name"        => "TRABISNAMESURNAME",
                "type"        => "input",
                "option"      => false
            ];

            $attributes[] = [
                "description" => "Company Name (Blank if personal)",
                "name"        => "TRABISORGANIZATION",
                "type"        => "input",
                "option"      => false
            ];

            $attributes[] = [
                "description" => "Company Tax Office (Blank if personal)",
                "name"        => "TRABISTAXOFFICE",
                "type"        => "input",
                "option"      => false
            ];
            $attributes[] = [
                "description" => "Company Tax Number (Blank if personal)",
                "name"        => "TRABISTAXNUMBER",
                "type"        => "input",
                "option"      => false
            ];

        }

        if(!empty($attributes)) {
            foreach ($attributes as &$attr) {
                if(isset($this->options["ext"][$attr["name"]])) {
                    $attr["default"] = $this->options["ext"][$attr["name"]];
                }
            }
            return [$extension => $attributes];
        }
    }

    /**
     * List all domains
     * @return array
     */
    public function ListDomains()
    {

        $listParams = [
            'PageNumber' => 0,
            'PageSize'   => 1000,
            'OrderColumn'=>'Id',
            'OrderDirection'=>'DESC',
        ];


        $response =  $this->dna()->GetList($listParams);

        $result    = [];
        $user_data = [];

        if (isset($response["data"]["Domains"]) && $response["data"]["Domains"]) {
            $result['total'] = $response["TotalCount"];

            foreach ($response["data"]["Domains"] as $res) {
                $domain = $res["DomainName"] ?? '';
                if ($domain) {
                    if ($res["Status"] == "Active") {
                        $result[] =['name'=> $domain];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Get domain pricing
     * @return array
     */
    public function getDomainPrices()
    {

        $response = $this->dna()->GetTldList(999);

        if ($response["result"] != "OK" && isset($response["error"]["Details"]) && strlen($response["error"]["Details"]) >= 3) {
            return  $response["error"]["Message"] . " : " . $response["error"]["Details"];

        }

        $result = [];

        foreach ($response["data"] as $row) {
            if ($row["status"] != "Active") {
                continue;
            }

            if (!isset($row["pricing"]["registration"][1])) {
                continue;
            }

            foreach (range($row['minperiod'], $row['maxperiod']) as $period) {

                $result['USD'][$row["tld"]][$period] = [
                    'period'   => $period,
                    'register' => number_format($row["pricing"]["registration"][1]*$period, 2, '.', ''),
                    'transfer' => number_format($row["pricing"]["transfer"][1]*$period, 2, '.', ''),
                    'renew'  => number_format($row["pricing"]["renew"][1]*$period, 2, '.', ''),
                    'redemption' => number_format($row["pricing"]["restore"][1]*$period, 2, '.', ''),
                ];

            }
        }
        return $result;
    }


    /**
     * Format contact data for API
     * @param array $cdata
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
     * Parse contact data from API response
     * @param string $type
     * @param array $data
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
