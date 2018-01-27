# Documentatie CiviCRM Basis Extensie Mediwe

Mediwe () gebruikt CiviCRM als centrale applicatie voor het beheren van relaties en de controles die uitgevoerd worden.

Deze extensie **Mediwe Basis CiviCRM Extensie** *(be.mediwe.basis)* bevat alle basis instellingen en klasses die nodig zijn. Deze documentatie bevat een algemene beschrijving van de geleverde functionaliteit en alle technische documentatie over wat er precies in de extensie zit.

## Algemene beschrijving
Mediwe heeft een enkel basisproces: het doen van medische controles bij zieke werknemers namens een opdrachtgever. Dat kan allerlei vormen aannemen en zeer verschillende flows hebben, maar dat zal altijd de basis zijn.

Er worden een aantal types van contacten (*contact subtypes*) in CiviCRM gebruikt om de soorten van contacten die belangrijk zijn voor het proces van Mediwe te kenmerken. Dit zijn Klant, KlantMedewerker, Controlearts, Inspecteur en Zorgfonds gebruiker.

CiviCase wordt gebruikt om verschillende [Typen van Dossiers](dossier_types.md) vast te kunnen leggen.

Er zijn een aantal zogenaamde [CiviRules](org_civicoop_civirules.md) in gebruik om delen van de procesgang te automatiseren.

## Configuratie Items
Met [Configuratie items voor CiviCRM](config_items.md) worden allerlei basis zaken in CiviCRM bedoeld zoals contact subtypen, types activiteiten, dossiertypes, groepen etc. die automatisch aangemaakt worden bij installatie. Deze kunnen op een later tijdstip ook weer bijgewerkt worden.

## Automatische Acties
[Automatische Acties](automatische_acties.md) zijn taken automatisch worden uitgevoerd als er binnen een dossier aan bepaalde voorwaarden voldaan is. Een voorbeeld is de email die aan de arts verstuurd wordt zodra is toegewezen als controle arts aan een medewerker.

## Extensies
Er worden bij Mediwe een aantal [CiviCRM extensies](civiextensions.md) gebruikt.

!!! note "Ondersteuning"
    [CiviCooP](https://civicoop.org) ondersteunt Mediwe bij alle zaken rondom CiviCRM.
    
    **e-mail** : [helpdesk@civicoop.org](mailto:helpdesk@civicoop.org)
    
    **telefoon** : +31 (0)55 57 62 855





