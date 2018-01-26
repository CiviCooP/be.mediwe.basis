# Documentatie CiviCRM Basis Extensie Mediwe

Mediwe () gebruikt CiviCRM als centrale applicatie voor het beheren van relaties en de controles die uitgevoerd worden.

De extensie **Mediwe Basis CiviCRM Extensie** *(be.mediwe.basis)* bevat alle basis instellingen en klasses die nodig zijn. Deze documentatie bevat een algemene beschrijving van de geleverde functionaliteit en alle technische documentatie over wat er precies in de extensie zit.

## Installatie

## Extensies
Er worden bij Mediwe een aantal extensies gebruikt. Ze worden hieronder kort opgenoemd met eventuele afhankelijkheden. Daar waar nodig is er specifieke documentatie per extensie.

|Extensie              | Extensie waar deze afhankelijk van is |
|----------------------|---------------------------------------|
|be.mediwe.basis       |:geen:|
|be.mediwe.interneui   |:be.mediwe.basis:|
|org.civicoop.civirules|:geen:|
|org.civicoop.emailapi |:org.civicoop.civirules:|


### Installatie via drush

 ```
 drush cvapi Extension.download key="be.mediwe.basis" url="https://github.com/CiviCooP/be.mediwe.basis/archive/master.zip"
 drush cvapi Extension.download key="be.mediwe.interneui" url="https://github.com/CiviCooP/be.mediwe.interneui/archive/master.zip"
 drush cvapi Extension.download key="org.civicoop.civirules" url="https://github.com/CiviCooP/org.civicoop.civirules/archive/1.17.zip" install="1"
 drush cvapi Extension.download key="org.civicoop.emailapi" url="https://github.com/CiviCooP/org.civicoop.emailapi/archive/V1.12.zip" install="1"
 ```

## Ondersteuning

## Algemene beschrijving

## Gedetailleerde beschrijvingen
* [Laden en bijwerken van configuratie items](config_items.md)
>* [CiviCRM activiteit types](activity_types.md)
>* [CiviCRM contact types](contact_types.md)
>* [CiviCRM dossier types](case_types)
>* [CiviCRM keuzegroepen](option_groups.md)
>* [CiviCRM lidmaatschap types](membership_types)
>* [CiviCRM eigen velden](custom_groups.json)
>* [CiviCRM relatie types](relationship_types.md)



