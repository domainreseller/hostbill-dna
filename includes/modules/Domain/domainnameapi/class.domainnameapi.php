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

    protected $version = '1.0.46';

    protected $modname = "Domain Name Api";

    protected $description = 'Domain Name API - ICANN Accredited Domain Registrar from TURKEY ';

    private $dna=null;


    protected $configuration = array(
        'username' => array(
            'value' => '',
            'type' => 'input',
            'default' => false
        ),
        'password' => array(
            'value' => '',
            'type' => 'password',
            'default' => false
        ),
        'testmode' => array(
            'value' => '0',
            'type' => 'check',
            'default' => '0'
        )
    );

    protected $lang = [
        'english' => [
            'username' => 'User Name',
            'password' => 'Password',
            'testmode' => 'Use Test Mode',
        ] ,
        'turkish' => [
            'username' => 'Kullanıcı Adı',
            'password' => 'Şifre',
            'testmode' => 'Test Modu',
        ]
    ];

    protected $commands = ['Register', 'Transfer', 'Renew', 'ContactInfo', 'RegisterNameServers', 'EppCode'];

    protected $clientCommands = ['ContactInfo', 'RegisterNameServers', 'EppCode'];


    /**
     * @return DomainNameAPI_PHPLibrary
     */
    private function dna(){

        if($this->dna==null){
            $this->dna = new  \DomainNameAPI_PHPLibrary();
        }
        if ($this->configuration['testmode']['value'] == '1'){
            $this->dna->useTestMode(true);
        }else{
            $this->dna->useTestMode(false);
        }
        $this->dna->setUser($this->configuration['username']['value'],$this->configuration['password']['value']);
        $this->dna->useCaching(false);

        return $this->dna;
    }

    /**
     * Checking connection
     * Test:1/1
     * @return bool
     */
    public function testConnection() {

        $result = $this->dna()->GetList();

        if($result["result"] == "OK"){
            return true;
        }else{
            return false;
        }

    }

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

    private function slack_message($message, $channel = "kriweb", $who = 'Seyyar Debugcu') {
        $ch   = curl_init("https://slack.com/api/chat.postMessage");
        $data = http_build_query([
            "token"    => "xoxp-470845361846-468707172800-949452384309-9bfb16eb31e1a8ec1242c69d1166abce",
            "channel"  => $channel,
            //"#mychannel",
            "text"     => $message,
            //"Hello, Foo-Bar channel message.",
            "username" => $who,
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

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

        //$this->slack_message(json_encode([$this->options,$this->domain_contacts,$nameservers,$contacts,$period,$domain]));

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
     * Getting nameservers
     * test : 3/3
     * @return array|false
     */
    public function getNameServers() {

        $result =$this->dna()->GetDetails($this->options['sld'].'.'.$this->options['tld']);

        if ($result["result"] == "OK") {
            $values = [];
            if (is_array($result["data"]["NameServers"][0])) {

                foreach (range(1, 5) as $k => $v) {
                    if (isset($result["data"]["NameServers"][0][$k])) {
                        $values[$v] = $result["data"]["NameServers"][0][$k];
                    }
                }

            } else {

                if (isset($result["data"]["NameServers"][0])) {
                    $values[1] = $result["data"]["NameServers"][0];
                }
            }
            return $values;
        } else {

            $this->addError('Can not access nameservers');
            return false;

        }


    }
    /**
     * Updating nameservers
     * test : 3/3
     * @return array|false
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
     * Getting Contact
     * test : 3/3
     * @return array
     */
    public function getContactInfo() {

        $result =$this->dna()->GetContacts($this->options['sld'].'.'.$this->options['tld']);

        //$this->slack_message(json_encode($result));


        $contact = [
            'registrant' => $this->_parseContact('Registrant', $result["data"]["contacts"]),
            'tech'       => $this->_parseContact('Technical', $result["data"]["contacts"]),
            'admin'      => $this->_parseContact('Administrative', $result["data"]["contacts"]),
            'billing'    => $this->_parseContact('Billing', $result["data"]["contacts"]),

        ];
        return $contact;

    }

    /**
     * Setting Contact
     * test : 3/3
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

        // $this->slack_message(json_encode([$this->options,$updateArr]));

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
     * Getting Registrarlock
     * test : 2/2
     * @return false|string
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
     * Setting registrarlock
     * test: 2/2
     * @return false
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
     * add childns
     * test: 2/2
     * @return bool
     */
    public function registerNameServer() {

        $result = $this->dna()->AddChildNameServer($this->options['sld'] . '.' . $this->options['tld'], $this->options['NameServer'], array($this->options['NameServerIP']));

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
     * modify childns
     * test: 2/2
     * @return bool
     */
    public function modifyNameServer() {

         $result = $this->dna()->ModifyChildNameServer($this->options['sld'] . '.' . $this->options['tld'], $this->options['NameServer'], array($this->options['NameServerNewIP']));

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
     * delte childns
     * test: 2/2
     * @return bool
     */
    public function deleteNameServer() {

         $result = $this->dna()->DeleteChildNameServer($this->options['sld'] . '.' . $this->options['tld'], $this->options['NameServer']);

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
     * EPP
     * test: 2/2
     * @return bool|string
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
     */
    public function synchInfo() {

        $result = $this->dna()->GetDetails($this->options['sld'] . '.' . $this->options['tld']);

        //$result = $this->dna()->GetList(2);



        $resp=[
          'status'=>'Active',
          'expires'=>date('Y-m-d',strtotime($result['data']['Dates']['Expiration'])),
          'reglock'=>$result['data']['LockStatus']=='true',
          'ns'=>$result['date']['NameServers'][0],
          'idprotection'=>$result['data']['PrivacyProtectionStatus']==true
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




        //WaitingForRegistration
        //Domain Aktif İse
        //Active,

        //Belge beklenen domainler
        //Yenileme Modu Auto Expire İse
        //Domain ilk kayıtta ise
        //WaitingForRegistration,

        //Belge Bekleyen Domainler
        //WaitingForDocument,//2

        //İç Transfer Talebi
        //WaitingForIncomingTransfer,//3

        //Dışarıya TRansfer Oldu
        //TransferredOut,//4

        // Bize gelen transfer dışardan iptal edildi.
        //TransferRejectedFromOpposed,//5

        //Bize gelen transfer bayi tarafından iptal edildi
        //TransferCancelledFromClient,//6

        // Silinmek İçin Bekleniyor
        //PendingDelete,//7

        //Silindi
        //Deleted,//8

        //Transferler için email onayı iletildi.
        //ConfirmationEmailSend,//9

        //Hiçbiri
        //None,//10

        //Yeni kayıt edilen domainlerde önkayıt işlemi
        //PreRegistration,//11

        //Domain Bizden Transfer Ediliyor
        //WaitingForOutgoingTransfer,//12,

        //Domain durduruldu
        //PendingHold,//13

        // Snkronize olamama durumunda
        //SynchronizationBlocked,//14

        //Zaman Aşımına uğrayan domainler // 15
        //TimeOut,

        //Güncelleme Bekleniyor //16
        //ModificationPending,
        //MigrationPending, //17
        //ModificationFailed//18,



    }

}