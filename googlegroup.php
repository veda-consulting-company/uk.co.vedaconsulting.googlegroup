<?php

require_once 'googlegroup.civix.php';
require_once 'google-api-php-client/src/Google/autoload.php';
set_include_path(get_include_path() . PATH_SEPARATOR . 'google-api-php-client/src');
define('GOOGLE_CLIENT_KEY', '');
define('GOOGLE_SECERT_KEY', '');
define('GROUP_DOMAIN', '');

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function googlegroup_civicrm_config(&$config) {
  _googlegroup_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function googlegroup_civicrm_xmlMenu(&$files) {
  _googlegroup_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function googlegroup_civicrm_install() {
  $extensionDir       = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $customDataXMLFile  = $extensionDir  . '/xml/auto_install.xml';
  $import = new CRM_Utils_Migrate_Import( );
  $import->run( $customDataXMLFile );
  _googlegroup_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function googlegroup_civicrm_uninstall() {
  _googlegroup_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function googlegroup_civicrm_enable() {
  _googlegroup_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function googlegroup_civicrm_disable() {
  _googlegroup_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function googlegroup_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _googlegroup_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function googlegroup_civicrm_managed(&$entities) {
  _googlegroup_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function googlegroup_civicrm_caseTypes(&$caseTypes) {
  _googlegroup_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function googlegroup_civicrm_angularModules(&$angularModules) {
_googlegroup_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function googlegroup_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _googlegroup_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function googlegroup_civicrm_pageRun( &$page ) {
  if ($page->getVar('_name') == 'CRM_Group_Page_Group') {
    $params = array(
      'version' => 3,
      'sequential' => 1,
    );
    // Get all the google groups and pass it to template as JS array
    // To reduce the no. of AJAX calls to get the group name in Group Listing Page
    $result = civicrm_api('Googlegroup', 'getgroups', $params);
    if(!$result['is_error']){
    $groups = json_encode($result['values']);
    $page->assign('lists_and_groups', $groups);
   }
  }
}

function googlegroup_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Group_Form_Edit' AND ($form->getAction() == CRM_Core_Action::ADD OR $form->getAction() == CRM_Core_Action::UPDATE)) {
    // Get all the Mailchimp lists
    $lists = array();
    $defaults = array();
    $params = array(
      'version' => 3,
      'sequential' => 1,
    );
    
    $lists = civicrm_api('Googlegroup', 'getgroups', $params);
    $form->add('select', 'google_group', ts('Google Group'), array('' => '- select -') + $lists['values'], FALSE );
    $templatePath = realpath(dirname(__FILE__)."/templates/CRM/Group/Form");
    CRM_Core_Region::instance('page-body')->add(array('template' => "{$templatePath}/Edit.extra.tpl"));
        
    $groupId = $form->getVar('_id');
    if ($form->getAction() == CRM_Core_Action::UPDATE AND !empty($groupId)) {
      $ggDetails  = CRM_Googlegroup_Utils::getGroupsToSync(array($groupId));

      if (!empty($ggDetails)) {
        $defaults['google_group'] = $ggDetails[$groupId]['group_id'];
      }
    }
    $form->setDefaults($defaults);  

  }
}

function googlegroup_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  if ($formName != 'CRM_Group_Form_Edit' ) {
    return;
  }
  
  if (!empty($fields['google_group'])) {
    $otherGroups = CRM_Googlegroup_Utils::getGroupsToSync(array(), $fields['google_group']);
    $thisGroup = $form->getVar('_group');
    if ($thisGroup && $otherGroups) {
      unset($otherGroups[$thisGroup->id]);
    }
    if (!empty($otherGroups)) {
      $otherGroup = reset($otherGroups);
      $errors['google_group'] = ts('There is already a CiviCRM group tracking this Group, called "'
        . $otherGroup['civigroup_title'].'"');
    }
  }
}

function googlegroup_civicrm_navigationMenu(&$params){
  $parentId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Mailings', 'id', 'name');
  $googlegroupSettings  = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Googlegroup_Settings', 'id', 'name');
  $googlegroupSync      = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Googlegroup_Sync', 'id', 'name');
  $maxId                = max(array_keys($params));
  $googlegroupMaxId     = empty($googlegroupSettings) ? $maxId+1             : $googlegroupSettings;
  $googlegroupsyncId    = empty($googlegroupSync)     ? $googlegroupMaxId+1  : $googlegroupSync;

  $params[$parentId]['child'][$googlegroupMaxId] = array(
        'attributes' => array(
          'label'     => ts('Googlegroup Settings'),
          'name'      => 'Googlegroup_Settings',
          'url'       => CRM_Utils_System::url('civicrm/googlegroup/settings', 'reset=1', TRUE),
          'active'    => 1,
          'parentID'  => $parentId,
          'operator'  => NULL,
          'navID'     => $googlegroupMaxId,
          'permission'=> 'administer CiviCRM',
        ),
  );
  $params[$parentId]['child'][$googlegroupsyncId] = array(
        'attributes' => array(
          'label'     => ts('Sync Civi Contacts To Googlegroup'),
          'name'      => 'Googlegroup_Sync',
          'url'       => CRM_Utils_System::url('civicrm/googlegroup/sync', 'reset=1', TRUE),
          'active'    => 1,
          'parentID'  => $parentId,
          'operator'  => NULL,
          'navID'     => $googlegroupsyncId,
          'permission'=> 'administer CiviCRM',
        ),
  );
}
