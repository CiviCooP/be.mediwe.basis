DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `migratie_klantmedewerker` AS (
SELECT
  `p`.`id_personnel`             AS `external_identifier`,
  `cst`.`nbr_company`            AS `employer_external_identifier`,
  `mediwe_joomla`.`convert`(
`p`.`nbr_personnel`,'2016%sechour2016')  AS `employee_personnel_nbr`,
  `mediwe_joomla`.`convert`(
`p`.`name`,'2016%sechour2016')  AS `display_name`,
  `mediwe_joomla`.`convert`(
`p`.`street`,'2016%sechour2016')  AS `domicilie_street_address`,
  `mediwe_joomla`.`convert`(
`p`.`zip`,'2016%sechour2016')  AS `domicilie_postal_code`,
  `mediwe_joomla`.`convert`(
`p`.`city`,'2016%sechour2016')  AS `domicilie_city`,
  (CASE `p`.`country` WHEN 'B' THEN 1020 WHEN 'F' THEN 1076 WHEN 'NL' THEN 1152 WHEN 'LU' THEN 1126 WHEN 'D' THEN 1082 ELSE 1020 END) AS `domicilie_country`,
  `mediwe_joomla`.`convert`(
`p`.`phone`,'2016%sechour2016')  AS `phone`,
  `mediwe_joomla`.`convert`(
`p`.`mobile`,'2016%sechour2016')  AS `mobile`,
  (CASE `p`.`sex` WHEN 'M' THEN 2 WHEN 'V' THEN 1 ELSE NULL END) AS `gender_id`,
  `p`.`partner`                  AS `employee_partner`,
  (CASE `p`.`language` WHEN 'NL' THEN 'nl_NL' WHEN 'FR' THEN 'fr_FR' ELSE NULL END) AS `preferred_language`,
  `p`.`memo`                     AS `employee_remarks`,
  `p`.`code_service`             AS `employee_code_level2`,
  `p`.`function_personnel`       AS `employee_function`,
  `p`.`entity_personnel`         AS `employee_level1`,
  `p`.`contract_type_prersonnel` AS `employee_contract`,
  `p`.`contract_personnel`       AS `employee_contract_desc`,
  `p`.`service`                  AS `employee_level2`,
  `p`.`sub_service`              AS `employee_level3`,
  `mediwe_joomla`.`convert`(
`p`.`rsz_nbr`,'2016%sechour2016')  AS `employee_national_nbr`,
 CASE `p`.`controlevrij` WHEN 'nee' THEN 0 WHEN 'ja' THEN 1 ELSE `p`.`controlevrij` END  AS `employee_is_free`,
  `p`.`date_birth`               AS `birth_date`,
  `p`.`date_in`                  AS `employee_date_in`,
  `p`.`date_out`                 AS `employee_date_out`,
  `p`.`FTE`                      AS `employee_fte`,
  `p`.`shift_system`             AS `employee_crew_system`,
  `p`.`costcenter`               AS `employee_costcenter`,
  `p`.`zwartelijst`              AS `employee_is_blacklisted`,
  IFNULL(`p`.`free1`,`p`.`stp_contracts`) AS `employee_free1`,
  `mediwe_joomla`.`convert`(
`p`.`street_residence`,'2016%sechour2016')  AS `verblijf_street_address`,
  `mediwe_joomla`.`convert`(
`p`.`zip_residence`,'2016%sechour2016')  AS `verblijf_postal_code`,
  `mediwe_joomla`.`convert`(
`p`.`city_residence`,'2016%sechour2016')  AS `verblijf_city`,
  (CASE `p`.`country_residence` WHEN 'B' THEN 1020 WHEN 'F' THEN 1076 WHEN 'NL' THEN 1152 WHEN 'LU' THEN 1126 WHEN 'D' THEN 1082 ELSE 1020 END) AS `verblijf_country`,
  `p`.`date_controlevrij_tot`    AS `employee_is_free_till`,
  `p`.`date_verplicht_tot`       AS `employee_is_blacklisted_till`,
  `p`.`contact_id`               AS `contact_id`,
  `p`.`employer_contact_id`               AS `employer_contact_id`,
FROM (`jos_mediwe_personnel` `p`
   JOIN `jos_mediwe_customer` `cst`
     ON ((`p`.`id_company` = `cst`.`id_bedrijf`)))
WHERE (`p`.`is_deleted` = 0))$$

DELIMITER ;