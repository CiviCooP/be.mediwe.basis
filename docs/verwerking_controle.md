# Verwerking van een medische controle

## Een klant vraagt een medische controle aan

Een klant vraagt een medische controle aan op het publieke deel van de website van Mediwe.
Daarom is zijn identiteit niet éénduidig gekend.

De klant geeft ons volgende informatie:

* De gegevens van de te controleren medewerker: naam, adres
* De gegevens over de ziekteperiode
* De facturatiegegevens van het bedrijf
* Contact gegevens van de aanvrager (naam, telefoon, email)
* E-mail adressen wie bevestiging van de aanvraag ontvangt en wie het resultaat ontvangt (telkens max. 3 adressen)

De verwerking verloopt in volgende stappen:

## De klant opzoeken

In de meeste gevallen zal het BTW nummer bepalend zijn om de klant terug te vinden.
Helaas hebben diverse organisaties (bvb. overheidsinstzllingen) GEEN BTW nummer.
In dat geval zullen we gebruik maken van de naam en het adres van de instelling.
Misschien kan de domeinnaam van de opgegeven e-mail adressen een goed hulpmiddel zijn?


## De medewerker opzoeken

Het opzoeken van een medewerker gebeurt altijd binnen de context van de opgegeven klant.
Immers: een persoon kan bij verschillende werkgevers werken of gewerkt hebben.  
We zoeken een persoon BINNEN een bepaalde organisatie.

!!! important "Belangrijk"

Dat betekent dat als je voor een opdracht de organisatie verandert, de toewijzing aan de medewerker
vervalt. De medewerker moet opnieuw toegewezen worden aan een persoon binnen de organisatie.

Het meest voor de hand liggend argument is het rijksregisternummer of personeelsnummer.
Echter: kleinere organisaties geven ons enkel een naam en een adres door.

## De te volgen procedure bepalen

De standaard procedure is een huisbezoek met - indien afwezig - een uitnodiging om zich
voor onderzoek aan te bieden op het kabinet van de controlearts.

Maar hierop bestaan tal van varianten:

* Huisbezoek binnen opgegeven 4 aaneensluitende uren

** Indien afwezig binnen die uren, geen convocatie op het kabinet meer
** Indien afwezig binnen die uren, wel een convocatie op het kabinet van de arts
** Indien afwezig binnen die uren, opnieu een huisbezoek op dag + 1 ("hercontrole")

Zo de arts het bezoek toch buiten die uren uitvoert:

** Dat mag niet, liever helemaal geen controle in dat geval
** Indien afwezig convocatie op het kabinet van de arts
** Indien afwezig hercontrole aan huis

* Huisbezoek gevolgd door een hercontrole indien afwezig

* Rechtstreekse convocatie op het kabinet van de arts

## De controlearts toewijzen

### Hoe gebeurt dit nu?

Het werkgebied van de controlearts wordt bepaald door een opsomming van postcodes en gemeenten.
Wij voegen intern ook een prioriteit toe aan elke postcode, waarmee we willen aanduiden: in deze
postcode/gemeente verkiezen we deze arts boven die andere arts.

Daarnaast tonen we aan de medewerker van Mediwe:

* Of de arts onze applicatie gebruikt
* Hoever de arts woont van de woonplaats van de medewerker
* Hoeveel % van de opdrachten van die arts een "Akkoord" waren afgelopen 12 maand
* Hoeveel opdrachten de arts vandaag (of morgen) al toegewezen kreeg

De artsen die met vakantie zijn worden niet getoond.

Hiernaast gebruikt de medewerker extra parameters dire momenteel NIET aanwezig zijn in het informatica
systeem:

* Of de arts die dag werkt (sommige artsen werken niet op vrijdag bvb.)
* Dat de arts per dag maximaal bvb. slechts 3 opdrachten wenst
* Klantspecifiek: de klant wenst deze arts niet (dit moedigen we absoluut niet aan)
* Sommige artsen worden vooraf gebeld (geraadpleegd) vooraleer ze toegewezen worden aan een opdracht

### Hoe zou dit in de toekomst kunnen gebeuren?

Het is duidelijk dat een deel van het probleem in "rules" kan gegoten worden:

* We hebben enkele artsen die weinig of geen randvoorwaarden stellen en heel veel opdrachten uitvoeren voor Mediwe.
Deze artsen zouden meteen kunnen toegewezen worden zonder tussenkomst van de medewerker van Mediwe.

* Op termijn zouden de "rules" complexer kunnen gemaakt worden om steeds meer artsen volledig automatisch toe te wijzen.

### Wat als we geen arts vinden?

In dat geval wordt de klant opgebeld voor overleg: soms is dat gesprek zeer moeilijk en zullen we vooralsnog een arts zoeken
waar eerder niet aan gedacht werd.
In het beste geval wordt de opdracht uitgesteld tot een andere dag.
In het slechtste geval wordt de opdracht geannuleerd.


## Doorsturen van opdrachten naar de controlearts

!!! important "Belangrijk"

Opdrachten worden ALTIJD de dag van uitvoering van de opdracht naar de arts doorgestuurd.
Dit om te vermijden dat een opdracht te vroeg uitgevoerd wordt.
Eerder toegewezen opdrachten vertrekken om 8u30 naar de arts.

### Standaard procedure

Een E-mail bericht vertrekt direct na toewijzing naar de arts.

### Diverse varianten

Flexibiliteit is een belangrijk verkoopsargument om artsen te binden aan Mediwe.
Daarom voorzien we tal van varianten:

* Niet mailen maar faxen
* Omtrent (= geen vast uur!) 12u een overzicht van de opdrachten mailen of faxen
* Omtrent de middag artsen opbellen om mee te delen hoeveel en welke opdrachten zij ontvangen hebben
* De arts raadplegen VOOR de toewijzing

### Inhoud van het mailbericht 

* Naam, email of fax van de geadresseerde arts
* Naam van de te controleren werknemer
* Zo geen consultatie: adres van de werknemer
* Soort controle (huisbezoek/consultatie/hercontrole) en datum
* Naam van de werkgever, eventueel werkgever specifieke tekst
* Functie van de werknemer + omschrijving van die functie
* Begin- en einddatum van de ziekteperiode + of het een verlening is
* De te volgen procedure: tussen bepaalde uren of niet, wat bij afwezigheid
* Bij huisbezoek of hercontrole: afstand (km) voor de arts
* Opmerking van de klant bij de opdracht
* Later toe te voegen: historiek vorige controles afgelopen 12 maand (datum - ziekteperiode - resultaat)

 
## Verwerking van resultaten


### Resultaten komen binnen via de applicatie

Nu schrijft de applicatie het resultaat rechtstreeks weg in de databank.
Dit zal in het nieuw systeem vervangen worden door een API call.

#### Parameters van de call

* Identificatie van de arts en de opdracht
* Datum en uur van de controle
* Voorgeschreven einddatum van de ziekte
* Diagnose code
* Opmerking voor de klant
* Opmerking voor Mediwe
* Is verlenging mogelijk?
* Is advies van arbeidsarts gewenst?
* Is advies vertrouwenspersoon gewenst?
* Is de ziekte werkgerelateerd?
* De werknemer heeft nog geen zekte attest 

Zo de werknemer vervroegd het werk moet hervatten

* Datum van werkhervatting
* Is de behandelende arts hierover gecontacteerd?

Zo de werknemer afwezig was

* De volgende stap meegedeeld aan de werknemer: niets - consultatie - hercontrole 


De informatie wordt opgeslagen, maar wordt nog niet verstuurd naar de klant.
Hiertoe moet nagegaan worden of:

- De procedure zoals afgesproken uitgevoerd werd
- Geen Medische informatie aanwezig is in 'Opmerkingen voor de klant'
- De voorgeschreven einddatum verschilt van wat de klant meedeelde, zo ja wordt de datum van de arts overgenomen 
en wordt een berichtje toegevoegd "de voorgeschreven einddatum zou volgens de controlearts niet dit zijn maar dat".  

In een aantal gevallen zouden we het resultaat dus wél rechtstreeks kunnen versturen naar de klant!

De vergoeding van extra kilometers (arts ontvangt 0.35€/km boven 30 km en enkel indien hij dit opgeeft.
Omdat we dit niet aanmoedigen, wordt dit niet "structureel" voorzien en zal de arts dit meegeven als opmerking.


### Resultaten komen binnen via fax

In dat geval moet de informatie afgelezen worden van de fax en manueel verwerkt.
Zie hierboven.


### Wat als de controle NIET uitgevoerd werd?

In dat geval wordt de klant opgebeld voor overleg.
In het beste geval wordt de opdracht uitgesteld tot een andere dag.
In het slechtste geval wordt de opdracht geannuleerd.