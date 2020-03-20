<?php
class CRM_Googlegroup_Form_Setting extends CRM_Core_Form {

  const 
    GG_SETTING_GROUP = 'Googlegroup Preferences';
    
  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {

    $this->addElement('text', 'client_key', ts('Client Key'), array(
       'size' => 48,
     ));    
    $this->addElement('text', 'client_secret', ts('Client Secret'), array(
       'size' => 48,
     ));
    
     $this->addElement('text', 'domain_name', ts('Domain Names'), array(
      'size' => 48,
    ));  
    

    $accessToken = CRM_Core_BAO_Setting::getItem(self::GG_SETTING_GROUP,
       'access_token', NULL, FALSE
    );
    
    if (empty($accessToken)) {
      $buttons = array(
        array(
          'type' => 'submit',
          'name' => ts('Connect To My Google Group'),
        ),
      );
      $this->addButtons($buttons);
    } else {
      $buttons = array(
        array(
          'type' => 'submit',
          'name' => ts('Save Domains'),
          ),
        );
      $this->addButtons($buttons);
      CRM_Core_Session::setStatus('Connected To Google', 'Information', 'info');
    }
    
    if (isset($_GET['code'])) {
      $client = CRM_Googlegroup_Utils::googleClient();
      $redirectUrl    = CRM_Utils_System::url('civicrm/googlegroup/settings', 'reset=1',  TRUE, NULL, FALSE, TRUE, TRUE);
      $client->setRedirectUri($redirectUrl);
      $client->authenticate($_GET['code']);
      CRM_Core_BAO_Setting::setItem($client->getRefreshToken(), self::GG_SETTING_GROUP, 'access_token' );
      header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
    }
  }

   public function setDefaultValues() {
    $defaults = $details = array();
    //MV#3747 fix warning message if client key and secret key is not exists
    if(!empty(GOOGLE_CLIENT_KEY) && !empty(GOOGLE_SECERT_KEY)) {
      $defaults['client_key']    = GOOGLE_CLIENT_KEY;
      $defaults['client_secret'] = GOOGLE_SECERT_KEY;
    }
    //MV#3747 set default client key and client secret key from setting table
    foreach (array('client_key', 'client_secret', 'domain_name') as $fieldName) {
      $fielValue = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP, $fieldName);
      if (!empty($fielValue)) {
        if ($fieldName == 'domain_name') {
          $fielValue = implode(',', $fielValue);
        }
        $defaults[$fieldName] = $fielValue;
      }
    }
    return $defaults;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    $params = $this->controller->exportValues($this->_name); 
    $domains = array();
    //MV#3747 seems only domain name was saved into settings table, save client key and secret key into settings table as well.
    //If GOOGLE_CLIENT_KEY and GOOGLE_SECERT_KEY not set in googlegroup.php then use client key and client secret from configuration settings
    foreach (array('client_key', 'client_secret', 'domain_name') as $fieldName) {
      if ($fieldName == 'domain_name') {
        $fielValue = explode(',', trim($params[$fieldName]));
      }
      else{
        $fielValue = trim($params[$fieldName]);
      }
      CRM_Core_BAO_Setting::setItem($fielValue,
        self::GG_SETTING_GROUP, $fieldName
      );
    }
     $accessToken = CRM_Core_BAO_Setting::getItem(self::GG_SETTING_GROUP,
       'access_token', NULL, FALSE
    );
    
    if (empty($accessToken)) {
      $client = CRM_Googlegroup_Utils::googleClient();
      $redirectUrl    = CRM_Utils_System::url('civicrm/googlegroup/settings', 'reset=1',  TRUE, NULL, FALSE, TRUE, TRUE);
      $client->setRedirectUri($redirectUrl);
      $service = new Google_Service_Directory($client);
      $auth_url = $client->createAuthUrl();
      CRM_Core_Error::debug_var('$auth_url', $auth_url);
      header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    }
  }
}
