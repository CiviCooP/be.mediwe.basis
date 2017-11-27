DELIMITER $$

USE `mediwe_civicrm_dev`$$

DROP PROCEDURE IF EXISTS `sp_migratie_klantmedewerker`$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_migratie_klantmedewerker`()
BEGIN



DECLARE min_id INT DEFAULT 0;

SELECT MAX(id) INTO min_id FROM civicrm_contact;

-- Update the employer internal id in Joomla DB
UPDATE mediwe_joomla.jos_mediwe_personnel p
INNER JOIN mediwe_joomla.jos_mediwe_customer cst ON cst.id_bedrijf = p.id_company
INNER JOIN civicrm_contact ct ON ct.external_identifier = cst.nrb_company
SET p.employer_contact_id = ct.id;

-- Contact
INSERT INTO civicrm_contact (contact_type, contact_sub_type, external_identifier, sort_name, display_name, preferred_language, gender_id, birth_date)
SELECT 'Individual', 'mediwe_klant_medewerker', external_identifier, display_name, display_name, preferred_language, gender_id, birth_date
FROM mediwe_joomla.migratie_klantmedewerker
WHERE contact_id IS NULL;

-- Update the internal id in Joomla DB
UPDATE mediwe_joomla.jos_mediwe_personnel p
INNER JOIN civicrm_contact ct ON ct.external_identifier = p.id_personnel
SET p.contact_id = ct.id;


-- Custom data
INSERT INTO civicrm_value_medewerker_18 (entity_id, rijksregisternr_128, personeelsnummer_129, partner_130, niveau_1_131, niveau_2_code_132, niveau_2_133, niveau_3_134, functie_135, contract_137, contract_omschrijving_138, ploegensysteem_139, bezetting_140, kostenplaats_141, datum_in_dienst_142, datum_uit_dienst_143, opmerkingen_144, vrije_info_1_145, is_controlevrij_146, is_controlevrij_tot_147, controle_steeds_aangewezen_148, controle_steeds_aangewezen_tot_149)
SELECT contact_id, mkm_rijksregister_nummer, mkm_personeelsnummer, mkm_partner, mkm_niveau1, mkm_code_niveau2, mkm_niveau2, mkm_niveau3, mkm_functie, mkm_contract, mkm_contract_omschrijving, mkm_ploegensysteem, mkm_bezetting, mkm_kostenplaats, mkm_datum_in_dienst, mkm_datum_uit_dienst, mkm_opmerkingen, mkm_vrij_veld1, mkm_is_controlevrij, mkm_controlevrij_tot, mkm_steeds_aangewezen, mkm_aangewezen_tot
FROM mediwe_joomla.migratie_klantmedewerker
WHERE contact_id > min_id;

-- Adress home 
INSERT INTO `civicrm_address` (contact_id, location_type_id, street_address, postal_code, city, country_id)
SELECT contact_id, 1, domicilie_street_address, domicilie_postal_code, domicilie_city, domicilie_country
FROM mediwe_joomla.migratie_klantmedewerker 
WHERE contact_id > min_id;

-- Adress residence 
INSERT INTO `civicrm_address` (contact_id, location_type_id, street_address, postal_code, city, country_id)
SELECT contact_id, 4, verblijf_street_address, verblijf_postal_code, verblijf_city, verblijf_country
FROM mediwe_joomla.migratie_klantmedewerker 
WHERE contact_id > min_id 
AND IFNULL(verblijf_street_address, '') <> '';

-- Phone
INSERT INTO `civicrm_phone` (contact_id, location_type_id, phone_type_id, phone)
SELECT contact_id, 1, 1,  phone
FROM mediwe_joomla.migratie_klantmedewerker 
WHERE contact_id > min_id 
AND  IFNULL(phone, '') <> '';

-- Mobile
INSERT INTO `civicrm_phone` (contact_id, location_type_id, phone_type_id, phone)
SELECT contact_id, 1, 2,  mobile
FROM mediwe_joomla.migratie_klantmedewerker 
WHERE contact_id > min_id 
AND  IFNULL(mobile, '') <> '';

	END$$

DELIMITER ;