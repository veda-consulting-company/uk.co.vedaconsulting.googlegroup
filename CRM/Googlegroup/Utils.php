<?php

class CRM_Googlegroup_Utils {

  static function googleClient() {
    $client = new Google_Client();
    //MV#3747 GOOGLE_CLIENT_KEY and GOOGLE_SECERT_KEY not set in googlegroup.php then use client key and client secret from configuration settings
    $clientKey = GOOGLE_CLIENT_KEY;
    $clientSecret = GOOGLE_SECERT_KEY;
    if (empty($clientKey)) {
      $clientKey = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP, 'client_key');
    }
    if (empty($clientSecret)) {
      $clientSecret = CRM_Core_BAO_Setting::getItem(CRM_Googlegroup_Form_Setting::GG_SETTING_GROUP, 'client_secret');
    }    
    $client->setClientId($clientKey);
    $client->setClientSecret($clientSecret);
    $client->setAccessType('offline');
    $client->addScope(Google_Service_Directory::ADMIN_DIRECTORY_GROUP);
    return $client;
  }
  
  static function getGroupsToSync($groupIDs = array(), $gc_group_id = null) {
    $params = $groups = array();
    if (!empty($groupIDs)) {
      $groupIDs = implode(',', $groupIDs);
      $whereClause = "entity_id IN ($groupIDs)";
    } else {
      $whereClause = "gc_group_id IS NOT NULL AND gc_group_id <> ''";
    }

    if ($gc_group_id) {
      // just want results for a particular MC list.
      $whereClause .= " AND gc_group_id = %1 ";
      $params[1] = array($gc_group_id, 'String');
    }
    $query  = "
      SELECT  entity_id, gc_group_id, cg.title as civigroup_title, cg.saved_search_id, cg.children
      FROM    civicrm_value_googlegroup_settings mcs
      INNER JOIN civicrm_group cg ON mcs.entity_id = cg.id
      WHERE $whereClause";
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $groups[$dao->entity_id] =
        array(
          'group_id'              => $dao->gc_group_id,
          'civigroup_title'       => $dao->civigroup_title,
          'civigroup_uses_cache'  => (bool) (($dao->saved_search_id > 0) || (bool) $dao->children),
        );
    }
    return $groups;
  }
  
  static function getGroupContactObject($groupID, $start=null) {
    $group           = new CRM_Contact_DAO_Group();
    $group->id       = $groupID;
    $group->find();

    if($group->fetch()){
      //Check smart groups (including parent groups, which function as smart groups).
      if($group->saved_search_id || $group->children){
        $groupContactCache = new CRM_Contact_BAO_GroupContactCache();
        $groupContactCache->group_id = $groupID;
        if ($start !== null) {
          $groupContactCache->limit($start, CRM_Googlegroup_Form_Sync::BATCH_COUNT);
        }
        $groupContactCache->find();
        return $groupContactCache;
      }
      else {
        $groupContact = new CRM_Contact_BAO_GroupContact();
        $groupContact->group_id = $groupID;
        $groupContact->whereAdd("status = 'Added'");
        if ($start !== null) {
          $groupContact->limit($start, CRM_Googlegroup_Form_Sync::BATCH_COUNT);
        }
        $groupContact->find();
        return $groupContact;
      }
    }
    return FALSE;
  }
  
}
