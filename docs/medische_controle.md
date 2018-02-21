# Dossier Medische controle
Dit type van dossier wordt aangemaakt op het moment dat een controle aangevraagd wordt. Het dossier wordt gesloten op het moment dat de factuur voor de controle gemaakt is.

## Functionele beschrijving

### Een klant vraagt een medische controle aan
Een klant vraagt een medische controle aan op het publieke deel van de website van Mediwe.
Daarom is zijn identiteit niet eenduidig gekend.

De klant geeft ons volgende informatie:

* De gegevens van de te controleren medewerker: naam, adres
* De gegevens over de ziekteperiode
* De facturatiegegevens van het bedrijf
* Contact gegevens van de aanvrager (naam, telefoon, email)
* E-mail adressen wie bevestiging van de aanvraag ontvangt en wie het resultaat ontvangt (telkens max. 3 adressen)

De verwerking verloopt in volgende stappen:

### De klant opzoeken
In de meeste gevallen zal het BTW nummer bepalend zijn om de klant terug te vinden.
Helaas hebben diverse organisaties (bvb. overheidsinstzllingen) GEEN BTW nummer.
In dat geval zullen we gebruik maken van de naam en het adres van de instelling.
Misschien kan de domeinnaam van de opgegeven e-mail adressen een goed hulpmiddel zijn?

### De medewerker opzoeken
Het opzoeken van een medewerker gebeurt altijd binnen de context van de opgegeven klant.
Immers: een persoon kan bij verschillende werkgevers werken of gewerkt hebben.  
We zoeken een persoon BINNEN een bepaalde organisatie.

!!! important "Belangrijk"
    
    Dat betekent dat als je voor een opdracht de organisatie verandert, de toewijzing aan de medewerker
    vervalt. De medewerker moet opnieuw toegewezen worden aan een persoon binnen de organisatie.

Het meest voor de hand liggend argument is het rijksregisternummer of personeelsnummer.
Echter: kleinere organisaties geven ons enkel een naam en een adres door.

### De te volgen procedure bepalen
De standaard procedure is een huisbezoek met - indien afwezig - een uitnodiging om zich
voor onderzoek aan te bieden op het kabinet van de controlearts.

Maar hierop bestaan tal van varianten:

* Huisbezoek binnen opgegeven 4 aaneensluitende uren
    * Indien afwezig binnen die uren, geen convocatie op het kabinet meer
    * Indien afwezig binnen die uren, wel een convocatie op het kabinet van de arts
    * Indien afwezig binnen die uren, opnieu een huisbezoek op dag + 1 ("hercontrole")

* Zo de arts het bezoek toch buiten die uren uitvoert:
    * Dat mag niet, liever helemaal geen controle in dat geval
    * Indien afwezig convocatie op het kabinet van de arts
    * Indien afwezig hercontrole aan huis

* Huisbezoek gevolgd door een hercontrole indien afwezig

* Rechtstreekse convocatie op het kabinet van de arts

### De controlearts toewijzen

#### Hoe gebeurt dit nu?
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

#### Hoe zou dit in de toekomst kunnen gebeuren?
Het is duidelijk dat een deel van het probleem in "rules" kan gegoten worden:

* We hebben enkele artsen die weinig of geen randvoorwaarden stellen en heel veel opdrachten uitvoeren voor Mediwe.
Deze artsen zouden meteen kunnen toegewezen worden zonder tussenkomst van de medewerker van Mediwe.

* Op termijn zouden de "rules" complexer kunnen gemaakt worden om steeds meer artsen volledig automatisch toe te wijzen.

#### Wat als we geen arts vinden?
In dat geval wordt de klant opgebeld voor overleg: soms is dat gesprek zeer moeilijk en zullen we vooralsnog een arts zoeken
waar eerder niet aan gedacht werd.
In het beste geval wordt de opdracht uitgesteld tot een andere dag.
In het slechtste geval wordt de opdracht geannuleerd.

### Doorsturen van opdrachten naar de controlearts

!!! important "Belangrijk"

    Opdrachten worden ALTIJD de dag van uitvoering van de opdracht naar de arts doorgestuurd.
    Dit om te vermijden dat een opdracht te vroeg uitgevoerd wordt.
    Eerder toegewezen opdrachten vertrekken om 8u30 naar de arts.

#### Standaard procedure
Een [E-mail bericht](bericht_arts.md) vertrekt direct na toewijzing naar de arts.

#### Diverse varianten
Flexibiliteit is een belangrijk verkoopsargument om artsen te binden aan Mediwe.
Daarom voorzien we tal van varianten:

* Niet mailen maar faxen
* Omtrent (= geen vast uur!) 12u een overzicht van de opdrachten mailen of faxen
* Omtrent de middag artsen opbellen om mee te delen hoeveel en welke opdrachten zij ontvangen hebben
* De arts raadplegen VOOR de toewijzing

### Verwerking van resultaten

#### Resultaten komen binnen via de applicatie
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

In een aantal gevallen zouden we het resultaat dus wel rechtstreeks kunnen versturen naar de klant!

De vergoeding van extra kilometers (arts ontvangt 0.35 euro/km boven 30 km en enkel indien hij dit opgeeft.
Omdat we dit niet aanmoedigen, wordt dit niet "structureel" voorzien en zal de arts dit meegeven als opmerking).

#### Resultaten komen binnen via fax
In dat geval moet de informatie afgelezen worden van de fax en manueel verwerkt.
Zie hierboven.

#### Wat als de controle NIET uitgevoerd werd?
In dat geval wordt de klant opgebeld voor overleg.
In het beste geval wordt de opdracht uitgesteld tot een andere dag.
In het slechtste geval wordt de opdracht geannuleerd.

## Activiteiten in het dossier

## Rollen in het dossier

## Technische beschrijving
De medische controle komt in principe binnen met de API **MedischeControle** **create**.

!!! note

    De API Medische Controle kent ook de mogelijke actions **get** , **delete** en **update**. Deze worden aan het eind van dit hoofdstuk verder beschreven.
    
### Korte beschrijving CiviCRM acties

1. Verwerken klant: controleren of de klant al bestaat. Zo niet, nieuwe klant toevoegen.
2. Verwerken klant medewerker: bij bekende klant moet er gecontroleerd worden of de te controleren medewerker al bestaat (als medewerker van de klant!). Zo niet, klant medewerker toevoegen. Bij een nieuwe klant wordt de klant medewerker altijd toegevoegd.
3. Verwerken contactpersoon: bij bekende klant moet er gecontroleerd worden of de contactpersoon al bestaat. Zo niet, wordt de contactpersoon toegevoegd. Bij een nieuwe klant wordt de contactpersoon altijd toegevoegd.
4. Verwerken aanvrager: bij bekende klant moet er gecontroleerd worden of de aanvrager al bestaat. Zo niet, wordt de aanvrager toegevoegd. Bij een nieuwe klant wordt de aanvrager altijd toegevoegd.
5. Verwerken dossier: er wordt een nieuw dossier medische controle aangemaakt. Indien er al een actief dossier medische controle bestaat voor de combinatie klant, medewerker en datum wordt een fout gemeld.   

#### Verwerken klant
* indien het BTW nummer van de klant ingevuld is, zoek de klant met de API **Klant getvalue**. Als de klant niet gevonden wordt, maak een nieuwe klant aan met API **Klant create**. Als de klant gevonden wordt met het BTW nummer en de naam klant vanuit de API is niet gelijk aan de naam van de klant in de database, voeg dan ook de naam klant vanuit de API toe als contact identity *mediwe_synoniem_klant*.
* indien het BTW nummer niet ingevuld:
    * probeer als eerste de klant uniek te vinden met de naam via de API **Klant getvalue** of **getsingle**. Als dat lukt, gebruik deze klant.
    * als klant niet gevonden, gebruik dan de API **Contact findbyidentity** om de klant te vinden met de ingegeven naam. Als op die manier een klant gevonden wordt, gebruik die klant
    
    
!!!! question

    Wellicht is het handig de *Extended Contact Matcher* extensie te gebruiken?
    
    
!!!! note

    Bijj het opzoeken op naam zal de *Contact Identities* extensie gebruikt worden om ook op synoniemen te kunnen zoeken. Daartoe zal een nieuw _identity type_ gebruikt worden (*name* is *mediwe_synoniem_klant* en label * klant bekend als*). 
 
Eventueel worden ook automatisch synoniemen toegevoegd. Als de klant niet gevonden wordt zal een nieuwe klant toegevoegd worden.


#### Verwerken klant medewerker
* het personeelsnummer van de medewerker als dit ingevuld is
* het rijksregisternummer van de klant als dit ingevuld is
* de voor- en achternaam van de klant als dit ingevuld is 
Indien er geen medewerker gevonden wordt zal een nieuwe klantmedewerker toegevoegd worden.

!!! warning "Let op!"

    De medewerker bestaat altijd in de context van de klant. Als Pietje Puk eerst bij BedrijfA en daarna bij BedrijfB heeft gewerkt en beide bedrijven zijn Mediwe klanten dan zal Pietje Puk twee keer voorkomen! Bij het zoeken naar de klant medewerker zal dus ook gezocht worden binnen de subgroep die gedefinieerd wordt door alle contacten die een werknemer relatie met de klant hebben. Als er sprake is van een nieuwe klant zal er dus per definitie ook sprake zijn van een nieuwe medewerker.

     



