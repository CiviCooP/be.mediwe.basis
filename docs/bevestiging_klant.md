# Bevestigingsbericht voor de klant

## Waarom dit bericht?

Om te bevestigen dat de opdracht correct door Mediwe ontvangen werd.
We gebruiken dit ook als "audit trail": om vast te leggen welke info aan ons werd doorgegeven,
zodat bij mogelijke discussies kan opgezocht worden wie/waar fouten gebeurd zijn.

Dit bericht wordt verstuurd **voor elke aanvraag van een medische controle**.

## Inhoud van dit bericht

We zullen dit bericht een nieuwe, klantvriendelijke inhoud geven.

!!! note "Klantvriendelijk"
    Persoonlijker, alsof het met de hand geschreven werd door een medewerker van Mediwe.
    Gegevens die niet aanwezig zijn worden niet vermeld in het bericht.
    Er zijn in het bericht geen lege vakjes.   
    
### Gegevens van de werkgever

Aan een klant die **niet aangemeld** is, worden de facturatiegegevens opgevraagd.
Voor dit soort klanten is het nodig om deze gegevens in dit bericht te herhalen:

* Bedrijf/Organisatie ()	
* Adres	 
* Postcode en gemeente	
* Uw ondernemingsnummer	

### Gegevens van de aanvrager

!!! note "Nog niet gemodelleerd"
    De aanvrager is een individu met een relatie "Aanvrager van controles" met de werkgever.

* Naam (display_name)
* Telefoon(telefoon van het contact)
* E-mail (e-mail adres van het contact)	

### Gegevens van de werknemer

!!! note "Belangrijk"
    Een veel voorkomende fout is dat het verblijfadres van de werknemer niet of niet correct doorgegeven werd.
    Dit moet zeker vermeld worden!
    
De werknemer is een contact van het type "Klantmedewerker". De werknemer is de client van het dossier "Medische controle".
Het controle adres wordt vermeld in de  activiteit  "Huisbezoek". Het wordt NIET gebruikt als adres van de werknemer
omdat dit kan wijzigen en omdat we steeds wensen te weten op welk adres een controle uitgevoerd werd.
 
* Naam (display_name)
* Adres van de controle (= woonplaats of verblijf) (mh_huisbezoek_adres)
* Postcode (mh_huisbezoek_postcode)
* Gemeente (mh_huisbezoek_gemeente)
* Telefoon (telefoon van het contact)
* Naam partner (mkm_partner)
* Taal van de werknemer (taal van het contact)

    
## Gegegevens over diens ziekteperiode

!!! bug "Niet correct gemodelleerd"
    De begin- en einddatum van de ziekteperiode ontbreekt.
    
* Begin- en einddatum van de ziekteperiode + of het een verlening is
* Soort: arbeidsongeval of ziekte (mzp_reden_ziekte)

## Gegevens over de controle opdracht

!!! warning "Nog uit te werken"
    In het huidige systeem is dit onvoldoende "clean" uitgewerkt.
    In het dossier zouden we moeten bewaren wat bij afwezigheid (binnen en buiten 4 wettelijke uren) de volgende
    stap moet zijn en of er wel een volgende stap moet zijn (sommige klanten wensen NIET dat een arts buiten de opgegeven uren
    bij een medewerker op bezoek komt om die 2de stap te ontwijken).
    
    Elementen:
     * Verplichte thuis aanwezigheid van ... tot ... (of geen verplichting)
     * Mag controle buiten deze uren? (Ja/Neen)
     * Wat als afwezig binnen deze uren? (geen vervolg - consultatie - hercontrole)
     * Wat als afwezig buiten deze uren? (niet van toepassing - consultatie - hercontrole)
     * Wat als afwezig zo er geen uren gelden? (Consultatie of hercontrole)

* Datum van de controle (mmc_controle_datum)
* Gewenste procedure
* Bericht voor Mediwe (mmc_opmerking_mediwe)
* Bericht voor de controlearts (mmc_opmerking_controlearts)
* Mag deze info gedeeld worden met de werknemer? (mmc_info_delen_patient)


## Communicatie 

* 3 email adressen voor debevestigingsmail (mmc_email1_contactpersoon, mmc_email2_contactpersoon, mmc_email3_contactpersoon)
* 3 email adressen voor het resultaat (mmc_result_email1, mmc_result_email2, mmc_result_email3)

## Klantspecifieke teksten

Voor 1 van onze klanten hebben we dit mail bericht laten beginnen met de woorden:

*Beste leidinggevende, ...*

Om bepaalde interne afspraken over de organisatie van medische controles bij die klant te vermelden.

    

 

