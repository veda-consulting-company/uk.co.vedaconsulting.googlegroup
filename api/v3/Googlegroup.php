<?php

/**
 * Googlegroups API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
 
/**
 * Googlegroups Get Googlegroups Groups API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_googlegroup_getgroups($params) {
  $groups = array();
  $client = CRM_Googlegroup_Utils::googleClient();
  
  $accessToken = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP,
    'access_token', NULL, FALSE
  );
  if ($accessToken) {
    $results = array();
    $client->refreshToken($accessToken);
    $service = new Google_Service_Directory($client);
    $domains = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP, 'domain_name');
    if (!empty($domains)) {
      foreach ($domains as $domain) {
        try {
	  $pageToken = "";
	  do {
	    $optParams = array('domain' => trim($domain), 'pageToken' => $pageToken);
	    $results = $service->groups->listGroups($optParams);
	    foreach($results->getGroups() as $result) {
	      $groups[$result['id']] = "{$domain}:{$result['name']}::{$result['email']}";
	    }
	    $pageToken = $results->nextPageToken;
	  } while($pageToken);
        } 
        catch (Exception $e) {
          return array();
        }
      }
    }
  }
  return civicrm_api3_create_success($groups);
}

function civicrm_api3_googlegroup_getmembers($params) {
  $members = array();
  $client = CRM_Googlegroup_Utils::googleClient();
  
  $accessToken = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP,
    'access_token', NULL, FALSE
  );
  if ($accessToken) {
    $client->refreshToken($accessToken);
    $service = new Google_Service_Directory($client);
    try {
      $pageToken = "";
      do {
	$optParams = array('pageToken' => $pageToken);
	$results = $service->members->listMembers($params['group_id'], $optParams);
	foreach($results->getMembers() as $result) {
	  $members[$result['id']] = $result['email'];
	}
	$pageToken = $results->nextPageToken;
      } while($pageToken);
    } 
    catch (Exception $e) {
      return array();
    }
    
  }
  return civicrm_api3_create_success($members);
}

function _civicrm_api3_googlegroup_getmembers_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroup_deletemember($params) {
  $members = array();
  $client = CRM_Googlegroup_Utils::googleClient();
  
  $accessToken = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP,
    'access_token', NULL, FALSE
  );
  if ($accessToken) {
    $client->refreshToken($accessToken);
    $client->setUseBatch(TRUE);
    $batch = new Google_Http_Batch($client);
    $service = new Google_Service_Directory($client);
    try {
      foreach ($params['member'] as $key => $member) {
        $batch->add($service->members->delete($params['group_id'], $member));
      }
      $client->execute($batch);
    } 
    catch (Exception $e) {
      return array();
    }
  }
  return civicrm_api3_create_success($members);
}

function _civicrm_api3_googlegroup_deletemember_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroup_subscribe($params) {
  $results = array();
  $accessToken = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP,
    'access_token', NULL, FALSE
  );
  if ($accessToken) {
    $client = CRM_Googlegroup_Utils::googleClient();
    $client->refreshToken($accessToken);
    $client->setUseBatch(TRUE);
    $batch = new Google_Http_Batch($client);
    $service = new Google_Service_Directory($client);
    try {
      foreach ($params['emails'] as $email) {
        $member = new Google_Service_Directory_Member();
        $member->setEmail($email);
        $member->setRole($params['role']);
        $batch->add($service->members->insert($params['group_id'], $member));
        
      }
      $client->execute($batch);
    } 
    catch (Exception $e) {
      return array();
    }
  }
  return civicrm_api3_create_success($results);
  
}

function _civicrm_api3_googlegroup_subscribe_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroup_sync($params) {
  $result = array();
	// Do push from CiviCRM to Google Group 
  $runner = CRM_Googlegroup_Form_Sync::getRunner($skipEndUrl = TRUE);
  if ($runner) {
    $result = $runner->runAll();
  }

  if ($result['is_error'] == 0) {
    return civicrm_api3_create_success();
  }
  else {
    return civicrm_api3_create_error();
  }
}
