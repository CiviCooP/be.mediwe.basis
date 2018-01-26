# Contact Types Mediwe
Bij Mediwe gebruiken we natuurlijk de standaard CiviCRM contact types *Organisatie* en *Persoon* (en mogelijk ook nog *Huishouden*). 
Daarnaast zijn er een aantal belangrijke specifieke contact types voor het proces van Mediwe, namelijk [Klant](#Klant)

## Klant

## Klant Medewerker

## Controlearts

De controlearts is de arts die in principe het hele dossier medische controle lang de arts van dienst is. 
Het is overigens wel mogelijk dat een arts een dossier aan een andere arts overdraagt, bijvoorbeeld als hij/zij op vakantie gaat en het dossier nog een activiteit bevat. Dan wordt de controlearts op het dossier vervangen en wordt deze wijziging vastgelegd in het dossier middels de standaard CiviCRM _change case role_ activiteit.

De controlearts wordt vastgelegd in het dossier [Medische Controle](dossiertypes.md#medischecontrole.md) als relatie.

!!! important "Belangrijk"
    Let op: de controlearts wordt in principe niet door de gebruiker in het dossier vastgelegd, maar automatisch als de activiteit **huisbezoek** aan de controle arts wordt toegekend. Op het moment dat dat gebeurt wordt automatisch de relatie op het dossier vastgelegd.
    
    
    