# Configuratie Items
Mediwe heeft een aantal zogenaamde configuratie items nodig in CiviCRM zoals:

* contact sub types
* relatie types
* activiteit types
* dossier types
* groepen
* keuzegroepen
* lidmaatschap types
* eigen velden

In de extensie be.mediwe.basis is voorzien dat deze configuratie items bij het installeren van de extensie aangemaakt worden.

Daarnaast is het mogelijk ze met een geplande taak (entity *ConfigItems* action *Load*) bij te werken.

De definities zijn in JSON bestanden in de extensie map CRM/Basis/ConfigItems/resources opgenomen. De eigen velden staat per groep in de map custom_data binnen de resources map.

Voorbeeld definitie van contact types:

```json
{
  "mediwe_klant":
  {
    "name": "mediwe_klant",
    "label":"Mediwe Klant",
    "parent_id":3
  },
  "mediwe_controle_arts":
  {
    "name": "mediwe_controle_arts",
    "label":"Controlearts",
    "parent_id":3
  },
  "mediwe_inspecteur":
  {
    "name": "mediwe_inspecteur",
    "label":"Inspecteur",
    "parent_id":3
  },
  "mediwe_gebruiker":
  {
    "name": "mediwe_gebruiker",
    "label":"Gebruiker zorgfonds",
    "parent_id":1
  },
  "mediwe_klant_medewerker":
  {
    "name": "mediwe_klant_medewerker",
    "label":"KlantMedewerker",
    "parent_id":1
  }
}
```

## Activiteit types
De volgende activiteit types worden geladen:

* ziekte attest (voor dossiers)
* huisbezoek (voor dossiers)
* convocatie op kabinet (voor dossiers)
* onderzoek arbeidsongeval (voor dossiers)
* dagelijke belafspraak met de arts

## Contact types
De volgende contact (sub) types worden geladen:

* Mediwe Klant, gebaseerd op basis type Organisatie
* Controlearts, gebaseerd op basis type Organisatie
* Inspecteur, gebaseerd op basis type Organisatie
* Gebruiker zorgfonds, gebaseerd op basis type Persoon
* KlantMedewerker, gebaseerd op basis type Persoon

## Dossier types
De volgende dossier types worden geladen:

* Dossier Ziektemelding
>* activiteiten Open Dossier en Ziekte attest
>* rol Dossier Coördinator

* Dossier Medische Controle
>* activiteiten Open Dossier, Huisbezoek, Convocatie op kabinet, Onderzoek arbeidsongeval
>* rollen Dossier Coördinator en Controlearts

## Lidmaatschap types
De volgende lidmaatschap types worden geladen:

* Voorafbetaald
* Mijn Mediwe
* Maandelijks
* Controlearts
* Zorgfonds
* Inspecteur

## Relatie types
De volgende relatie types worden geladen:

* Is klant via / Mijn contract geldt ook voor
* Controlearts / Onderzocht door controlearts
* Meldt ziekte van / Werd ziekgemeld door
* Vraagt medische controle voor / Wordt gecontroleerd in opdracht van


