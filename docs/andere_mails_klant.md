# Andere email berichten naar klanten

!!! note "Strategie van Mediwe"
    Omdat we graag onze klanten voor lange tijd behouden, grijpen we graag in op de interne organisatie van onze klanten.
    We helpen hen graag om alle communicatie te verzorgen **binnen** hun organisatie.
    
Deze berichten worden nu nog niet gelogd in het systeem.
Ik denk dat dit eigenlijk niet nodig is.

## Soorten berichten

### Advies over voorstel medische controle

Sommige klanten vragen dat Mediwe kiest wie best een medische controle ondergaat.
In dat geval wordt vooraf een mail naar de klant gestuurd met de vraag of de klant hiermee akkoord gaat.
**dit is enkel voor bepaalde klanten, niet voor alle**.

Voor welke klanten moet dit? (mas_email_goedkeuring)

!!! bug "Niet correct gemodelleerd?"
        Moeten we het aantal uitgevoerde controles afgelopen maand niet toevoegen in 
        custom groep mediwe_expert_tellers?
    
 
!!! quote "Inhoud van deze mail"
				{mas_aanspreking_goedkeuring},
				
				We stellen voor om een medische controle uit te voeren bij volgende medewerker:
				
				  (naam, mkm_niveau1, mkm_niveau2, mkm_niveau3)
				  
				Ziekte attest verstuurd op: (mma_poststempel_datum) volgens poststempel.
				iek van (mma_begin_datum) tot mma_eind_datum.
				Bradford: (met_bradford)
				Aantal ziekteattesten afgelopen 12 maand: (met_ziekteperiodes)
				Aantal maal ziek op maandag: (met_maandag_ziektes)
				Reeds uitgevoerde controles: (???)
				Heb je vragen of opmerkingen over dit dossier, neem gerust contact op!</p>
				
				Gelieve per kerende of op het telefoonnummer 03/220 61 00
				aan Ria, Kristel, Cathy of Géraldine te laten weten of deze medische controle mag doorgaan.
				
                Met vriendelijke groeten,
               
                Het Mediwe team
    

### Bevestiging dat een ziekte attest geregistreerd werd    


Onze belangrijkste klant heeft ons gevraagd een mail te sturen naar het kantoor
van een medewerker nadat een ziekte attest geregistreerd werd.

!!! bug "Niet correct gemodelleerd?"
        Hiertoe hebben (enkel vootr die klant) een bestandje waarin we per kantoor code (mkm_code_niveau2)
        bewaren welke email adressen (max. 2) de bestemmelingen zijn en in welke taal de mail moet opgesteld worden.
        

!!! quote "Inhoud van deze mail"
    %MAIL_CERTIFICATE_INTRO%
    
    (mkm_niveau2)
    %SP_CODE_SERVICE% : (mkm_code_niveau2)
    %SP_NBR_CUSTOMER% : (mkm_vrij_veld1)
    			
    %SP_WERKNEMER%
    
    %NAME% : (display_name werknemer)
    %ADDRESS% : (adres van contact)
    %ZIPANDCITY% : (postcode en gemeente van de contact)
    	
    	
    %CERTIFICATE%
    
    
    %FROM%  (mma_begin_datum) %TILL% (mma_eind_datum)


### Bericht dat de einddatum van een ziekte ingebracht werd

Onze belangrijkste klant heeft ons gevraagd een mail te sturen naar het kantoor
van een medewerker nadat de einddatum aan een bestaande ziekteperiode toegevoegd werd.        

!!! bug "Niet correct gemodelleerd"
    Begin- en einddatum van een ziekteperiode moeten nog toegevoegd worden in custom groep mediwe_ziekte_periode.    


!!! quote "Inhoud van deze mail"
    %MAIL_ENDDATE_INTRO%
    
    %KANTOOR%
    %SP_CODE_SERVICE% : (mkm_code_niveau2)
    %SP_NBR_CUSTOMER% :(mkm_vrij_veld1)
    			
    %SP_WERKNEMER%
    
    %NAME% : (display_name werknemer)
    %ADDRESS% : (adres van contact)
    %ZIPANDCITY% : (postcode en gemeente van de contact)
    	
    	
    %ABSENT%
    
    
    %FROM%  (mzp_???) %TILL% (mzp_???)	     


### Bericht dat de medewerker vervroegd het werk hervat

Onze belangrijkste klant heeft ons gevraagd een mail te sturen naar het kantoor
van een medewerker nadat een ziekteperiode vervroegd beeindigd wordt.        
     
!!! quote "Inhoud van deze mail"
    %MAIL_ENDDATE_INTRO%
    
    %KANTOOR%
    %SP_CODE_SERVICE% : (mkm_code_niveau2)
    %SP_NBR_CUSTOMER% :(mkm_vrij_veld1)
    			
    %SP_WERKNEMER%
    
    %NAME% : (display_name werknemer)
    %ADDRESS% : (adres van contact)
    %ZIPANDCITY% : (postcode en gemeente van de contact)
    	
    	
    %RESUME%
    
    
    %TILL% (mzp_spontane_werkhervatting)	

     