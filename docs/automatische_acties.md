### Automatische Acties

### Opdracht e-mail arts

Als een arts is toegewezen krijgt hij een email met daarin alle relevante gegevens. Deze e-mail wordt ook onderdeel van het ziektemelding dossier.

-- TODO beschrijving van de inhoud van de email.

#### Configuratie
1. De eerste stap is het aanmaken van een email template. Dit kan met _Mailings->Berichtsjablonen->Gebruikersberichten_.
2. Selecteer vervolgens de aangemaakte email in het _Beheer -> Mediwe Settings_ scherm. 

### Dagelijke belafspraak

Sommige artsen stellen er prijs op om gebeld te worden als één of meer medewerkers aan hem toegewezen is om hen te bezoeken. Om dit belprocess gestructeerd te kunnen verwerken word er een activiteit aangemaakt. 

Dit gebeurd met de volgende automatische activiteit.

1. Als een arts wordt toegewezen aan een ziektemelding wordt eerst nagegaan of hij gebeld wil worden.
1. Vervolgens wordt er een _Vastebelafspraak Arts_ activiteit bij hem aangemaakt. Indien de arts al een belafspraak heeft wordt deze aangevuld. In de tekst van de belafspraak staat het aantal bezoeken

-- TODO Deze activiteit kan aangevuld worden met andere gegevens. Leidend is hier dat ze bruikbaar zijn in het gesprek met de arts.

## CiviRules versus Post hooks

Automatische akties kunnen gerealiseerd worden met CiviRules en Post hooks.

Voordeel CiviRules
* Voorwaarden zijn via de gebruikers interface aan te passen.
* De aktie kent gebruikers documentatie vanuit de interface.
* Een automatische aktie kan eenvoudig uitgezet worden.

Voordeel PostHooks
* Complexe situaties kunnen uitgeprogrammeerd worden.

Per automatische aktie kan besloten worden welke methodiek wordt gebruikt.