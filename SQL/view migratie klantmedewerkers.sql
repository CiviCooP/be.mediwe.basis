DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `migratie_klantmedewerker` AS (
SELECT
  `p`.`id_personnel`             AS `external_identifier`,
  `cst`.`nbr_company`            AS `employer_external_identifier`,
  `mediwe_joomla`.`convert`(
`p`.`nbr_personnel`,'2016%sechour2016')  AS `mkm_personeelsnummer`,
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
  `p`.`partner`                  AS `mkm_partner`,
  (CASE `p`.`language` WHEN 'NL' THEN 'nl_NL' WHEN 'FR' THEN 'fr_FR' ELSE NULL END) AS `preferred_language`,
  `p`.`memo`                     AS `mkm_opmerkingen`,
  `p`.`code_service`             AS `mkm_code_niveau2`,
  `p`.`function_personnel`       AS `mkm_functie`,
  `p`.`entity_personnel`         AS `mkm_niveau1`,
  `p`.`contract_type_prersonnel` AS `mkm_contract`,
  `p`.`contract_personnel`       AS `mkm_contract_omschrijving`,
  `p`.`service`                  AS `mkm_niveau2`,
  `p`.`sub_service`              AS `mkm_niveau3`,
  `mediwe_joomla`.`convert`(
`p`.`rsz_nbr`,'2016%sechour2016')  AS `mkm_rijksregister_nummer`,
 CASE `p`.`controlevrij` WHEN 'nee' THEN 0 WHEN 'ja' THEN 1 ELSE `p`.`controlevrij` END  AS `mkm_is_controlevrij`,
  `p`.`date_birth`               AS `birth_date`,
  `p`.`date_in`                  AS `mkm_datum_in_dienst`,
  `p`.`date_out`                 AS `mkm_datum_uit_dienst`,
  `p`.`FTE`                      AS `mkm_bezetting`,
  `p`.`shift_system`             AS `mkm_ploegensysteem`,
  `p`.`costcenter`               AS `mkm_kostenplaats`,
  `p`.`zwartelijst`              AS `mkm_steeds_aangewezen`,
  IFNULL(`p`.`free1`,`p`.`stp_contracts`) AS `mkm_vrij_veld1`,
  `mediwe_joomla`.`convert`(
`p`.`street_residence`,'2016%sechour2016')  AS `verblijf_street_address`,
  `mediwe_joomla`.`convert`(
`p`.`zip_residence`,'2016%sechour2016')  AS `verblijf_postal_code`,
  `mediwe_joomla`.`convert`(
`p`.`city_residence`,'2016%sechour2016')  AS `verblijf_city`,
  (CASE `p`.`country_residence` WHEN 'B' THEN 1020 WHEN 'F' THEN 1076 WHEN 'NL' THEN 1152 WHEN 'LU' THEN 1126 WHEN 'D' THEN 1082 ELSE 1020 END) AS `verblijf_country`,
  `p`.`date_controlevrij_tot`    AS `mkm_controlevrij_tot`,
  `p`.`date_verplicht_tot`       AS `mkm_aangewezen_tot`,
  `p`.`contact_id`               AS `contact_id`,
  `p`.`employer_contact_id`               AS `employer_contact_id`,
FROM (`jos_mediwe_personnel` `p`
   JOIN `jos_mediwe_customer` `cst`
     ON ((`p`.`id_company` = `cst`.`id_bedrijf`)))
WHERE (`p`.`is_deleted` = 0))$$

DELIMITER ;