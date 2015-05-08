# Google Group Integration for CiviCRM  #

### Overview ###

CiviCRM can be integrated with Google Group

### Installation ###

* Install the extension manually in CiviCRM. More details [here](http://wiki.civicrm.org/confluence/display/CRMDOC/Extensions#Extensions-Installinganewextension) about installing extensions in CiviCRM.
* Fill in GOOGLE_CLIENT_KEY, GOOGLE_SECERT_KEY and GROUP_DOMAIN in googlegroup.php
* Configure Google Group details in Mailings >> Googlegroup Settings(civicrm/googlegroup/settings?reset=1)

### Usage ###
* Warning: Contacts in CiviCRM will exactly match in Google Group after sync. That means contacts which are not in CiviCRM but in Google Group will be removed during the sync to keep contacts same in both
* Sync CiviCRM with Google group in Mailings >> Sync Civi Contacts To Googlegroup

### Support ###

support (at) vedaconsulting.co.uk


