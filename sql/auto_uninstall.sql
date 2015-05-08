-- drop custom value table
DROP TABLE IF EXISTS civicrm_value_googlegroup_settings;

-- drop custom set and their fields
DELETE FROM `civicrm_custom_group` WHERE table_name = 'civicrm_value_googlegroup_settings';

