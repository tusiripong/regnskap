<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Sevenoffice {

    public $CI;
    protected $username = "andre@lchl.no";  //Change this to your client user or community login
    protected $password = "Rina9066";  //Change this to your password
    protected $applicationid = "4b270639-540f-4fdc-9f87-176b199e90df";  //Change this to your applicationId
    protected $identityId = "00000000-0000-0000-0000-000000000000";  //Change this to your identityId
    protected $authentication = "";  //Change this to your identityId
    protected $Identitites  = "";

    // -------------------------------------------------------------------------

    public function __construct() {
        $this->CI = & get_instance();

        $params ["credential"]["Username"] = $this->username;
        $encodedPassword = md5(mb_convert_encoding($this->password, 'utf-16le', 'utf-8'));
        $params ["credential"]["Password"] = $encodedPassword;
        $params ["credential"]["ApplicationId"] = $this->applicationid;
        $params ["credential"]["IdentityId"] = $this->identityId;

        return $this->Authentication($params);
    }

    public function Authentication($params) {
        $options = array('trace' => true);
        try {
            $this->authentication = new SoapClient("https://api.24sevenoffice.com/authenticate/v001/authenticate.asmx?wsdl", $options);
            // log into 24SevenOffice if we don't have any active session. No point doing this more than once.
            $login = true;
            if (!empty($_SESSION['ASP.NET_SessionId'])) {
                $this->authentication->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
                try {
                    $login = !($this->authentication->HasSession()->HasSessionResult);
                } catch (SoapFault $fault) {
                    $login = true;
                }
            }
            if ($login) {
                $result = ($temp = $this->authentication->Login($params));
                // set the session id for next time we call this page
                $_SESSION['ASP.NET_SessionId'] = $result->LoginResult;
                // each seperate webservice need the cookie set
                $this->authentication->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
                // throw an error if the login is unsuccessful
                if ($this->authentication->HasSession()->HasSessionResult == false)
                    throw new SoapFault("0", "Invalid credential information.");
            }
			$this->Identitites = $this->authentication->GetIdentities();
        } catch (SoapFault $fault) {
            echo 'Authentication Exception: ' . $fault->getMessage();
        }
    }
    
    public function GetIdentityService() {
        try {
            return $this->Identitites;
        } catch (SoapFault $fault) {
            echo 'SetIdentityService Exception: ' . $fault->getMessage();
            return false;
        }
    }
    
    public function SetIdentityService($IdentityID) {
        try {
            foreach ($this->Identitites->GetIdentitiesResult->Identity[$IdentityID] as $key => $value) {
                $IdentityParameters["identity"][$key] = $value;
            }
            $this->authentication->SetIdentity($IdentityParameters);
            return true;
        } catch (SoapFault $fault) {
            echo 'SetIdentityService Exception: ' . $fault->getMessage();
            return false;
        }
    }

    public function TransactionService($DateStart, $DateEnd) {
        $options = array('trace' => true);
        try {
            $TransactionSearchParameters = array();
            $TransactionSearchParameters["searchParams"]["DateStart"] = $DateStart . "T00:00:00"; //Y-m-d 2015-01-04
            $TransactionSearchParameters["searchParams"]["DateEnd"] = $DateEnd . "T23:59:59"; //Y-m-d 2015-09-23
            $TransactionService = new SoapClient("https://api.24sevenoffice.com/Economy/Accounting/V001/TransactionService.asmx?WSDL", $options);
            $TransactionService->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
            $TransactionResult = $TransactionService->GetTransactions($TransactionSearchParameters,false);
            return $TransactionResult;
        } catch (SoapFault $fault) {
            echo 'TransactionService Exception: ' . $fault->getMessage();
            return false;
        }
    }

    public function objectToArray( $object ) {
     	if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( 'Sevenoffice::objectToArray', $object );
    }
	
    public function CompanyService($companyId) {
        $options = array('trace' => true);
        try {
            $CompanySearchParameters = array();
            $CompanySearchParameters["searchParams"]["CompanyId"] = $companyId;
            $CompanyService = new SoapClient("https://api.24sevenoffice.com/CRM/Company/V001/CompanyService.asmx?WSDL", $options);
            $CompanyService->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
            $CompanyResult = $CompanyService->GetCompanies($CompanySearchParameters,false);
            return $CompanyResult;
        } catch (SoapFault $fault) {
            echo 'CompanyService Exception: ' . $fault->getMessage();
            return false;
        }
    }
}
