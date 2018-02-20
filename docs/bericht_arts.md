# Het bericht bestemd voor de arts

De inhoud van het bericht is identiek of je dit nu mailt of via de fax verstuurt:

## Inhoud van het mailbericht 

* Later toe te voegen: historiek vorige controles afgelopen 12 maand (datum - ziekteperiode - resultaat)

### Gegevens van de geadresseerde arts

De controlearts is de arts die toegekend werd aan de activiteit "Huisbezoek" of "Consultatie".
Deze wordt ook gekopieerd in de rol "Onderzocht door controlearts" in het dossier "Medische controle".

Deze gegevens zijn noodzakelijk uiteraard om de bestemmeling van het bericht te kennen.
Voor het versturen per fax zien de medewerkers in het bericht wie de bestemmeling is.
Op hun Canon printer is een adressenbestand aanwezig en kunnen zij via de naam het juiste fax nummer bepalen.

* Naam, email of fax van de geadresseerde arts

### Gegevens van de te controleren werknemer

De werknemer is de "Client" waaraan het dossier "Medische Controle" gekoppeld is.

* Naam van de te controleren werknemer
* Zo geen consultatie: adres van de werknemer
* Functie van de werknemer
* De bradford factor over de afgelopen 12 maand 

!!! warning "De bradford niet altijd tonen"
    De bradford heeft slechts zin als we beschikken over alle ziekteperiodes van de werknemer.
    Dit is: als de klant een lidmaatschap "Mijn Mediwe" heeft van het subtype "Uitgebreid".

!!! bug "Subtype Mijn Mediwe lidmaatschap is nog niet voorzien"
    In het huidig systeem zijn er 2 subtypes: Basis - Uitgebreid.    

### Gegevens van de werkgever

De werkgever is de "current employer" van de werknemer.

* Naam van de werkgever, eventueel werkgever specifieke tekst.

!!! warning "Specifieke tekst"
    Dit moet nog uitgewerkt worden (voorstel Klaas).
     
### Gegevens van de activiteit (Huisbezoek of Consultatie)

Een "Hercontrole" is een huisbezoek dat binnen hetzelfde dossier "Medische controle" volgt op een eerder huisbezoek.

!!! warning "Hercontrole"
    Is dat een ander type activiteit? Hoe modelleren we dat?
    
* Soort controle (huisbezoek/consultatie/hercontrole) en datum

### Gegevens van de ziekteperiode

!!! bug "Niet correct gemodelleerd"
    De begin- en einddatum van de ziekteperiode ontbreekt.
    
* Begin- en einddatum van de ziekteperiode + of het een verlening is
    
### Gegevens van het dossier medische controle

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

* De datum van de controle (mmc_controle_datum)
* De omschrijving van de functie van de werknemer uit het dossier medische controle (mmc_job_beschrijving)
* De te volgen procedure: tussen bepaalde uren of niet, wat bij afwezigheid    
* Bij huisbezoek of hercontrole: afstand (km) voor de arts
* Opmerking van de klant bij de opdracht

### Historiek van medische controles van de voorbije 12 maand

Voor elk dossier "Medische controle":

* De datum van de controle (mmc_controle_datum)
* De ziekteperiode + of dit een verlenging was (=niveau dossier "Ziekteperiode")
* Het eindresultaat van de controle (mmc_resultaat)

## Voorbeelden van klantspecifieke teksten

!!! warning
    Deze voorbeelden tonen aan dat de meeste klantspecifieke teksten kunnen verdwijnen als we ons datamodel beter uitwerken.
    

* Indien huisbezoek niet mogelijk tussen 9u en 18u, geen controle.
Dit kan "automatisch" opgevangen worden voor alle klanten als we de procedure beter modelleren in onze data.
* Bepaalde tekst over "visie" van het bedrijf net onder de naam van het bedrijf.
* De convocatie op het kabinet moet altijd op dag + 1 (modelleren?)
* Geen huisbezoek na 17u, zo niet geen controle (voor deze klant geldt dit enkel op vrijdagen!)
* Tip van de week vanwege Mediwe

