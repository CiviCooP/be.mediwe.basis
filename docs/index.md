# Documentatie CiviCRM Basis Extensie Mediwe

Mediwe (https://www.mediwe.be/nl/start) gebruikt CiviCRM als centrale applicatie voor het beheren van relaties en de controles die uitgevoerd worden.

Deze extensie **Mediwe Basis CiviCRM Extensie** *(be.mediwe.basis)* bevat alle basis instellingen en klasses die nodig zijn. Deze documentatie bevat een algemene beschrijving van de geleverde functionaliteit en alle technische documentatie over wat er precies in de extensie zit.

## Algemene beschrijving
Mediwe heeft een enkel basisproces: het doen van medische controles bij zieke werknemers namens een opdrachtgever. Dat kan allerlei vormen aannemen en zeer verschillende flows hebben, maar dat zal altijd de basis zijn.

Er worden een aantal [Contact Types](contacttypes.md) (*contact subtypes*) in CiviCRM gebruikt om de soorten van contacten die belangrijk zijn voor het proces van Mediwe te kenmerken. Dit zijn Klant, KlantMedewerker, Controlearts, Inspecteur en Zorgfonds gebruiker.

CiviCase wordt gebruikt om verschillende [Dossier Types](dossiertypes.md) vast te kunnen leggen.

Er zijn een aantal zogenaamde [CiviRules](org_civicoop_civirules.md) in gebruik om delen van de procesgang te automatiseren.

## Configuratie Items
Met [Configuratie items voor CiviCRM](configitems.md) worden allerlei basis zaken in CiviCRM bedoeld zoals contact subtypen, types activiteiten, dossiertypes, groepen etc. die automatisch aangemaakt worden bij installatie. Deze kunnen op een later tijdstip ook weer bijgewerkt worden.

## Extensies
Er worden bij Mediwe een aantal [CiviCRM extensies](civiextensions.md) gebruikt.

## Technische achtergrond en uitgangspunten
Er zijn een aantal belangrijke 'technische' redenen waarom de software van Mediwe nu opnieuw ontwikkeld wordt:

1. De huidige software is te veel een verstrengeling tussen Joomla en CiviCRM. Dat is nadelig voor de benodigde flexibiliteit
1. Er is te weinig documentatie waardoor alles erg afhankelijk is van Christophe die als enige de benodigde kennis en vaardigheden heeft
1. Het is essentieel voor de concurrentiepositie van Mediwe dat er snel gereageerd kan worden op wensen van klanten, en dat er optimale flexibiliteit geboden kan worden.

Als gevolg hanteren we bij de ontwikkeling de volgende uitgangspunten:

1. We ontwikkelen altijd API's voor alle voorkomende entiteiten en functionaliteiten. De achtergrond moet blijven dat we de data in CiviCRM via de API kunnen benaderen en onderhouden. Van de buitenkant moeten verschillende communicatiekanalen (de interne user interface (CiviCRM), de website, de app, toekomstige webservices etc.) via de API op dezelfde wijze contact kunnen leggen met de data in CiviCRM.
1. Tijdens de ontwikkeling documenteren we de zaken die we belangrijk vinden zodat toekomstige ontwikkeling en onderhoud door meerdere mensen uitgevoerd kan worden.



!!! note "Ondersteuning"
    [CiviCooP](https://civicoop.org) ondersteunt Mediwe bij alle zaken rondom CiviCRM.
    
    **e-mail** : [helpdesk@civicoop.org](mailto:helpdesk@civicoop.org)
    
    **telefoon** : +31 (0)55 57 62 855





