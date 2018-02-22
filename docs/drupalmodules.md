|Module naam   |Omschrijving | Drush installatie |
|--------------|-------------|-------------------|
|civi_bartik |Drupal theme dat CiviCRM meer schermruimte geeft.| ```drush en civi_bartik -y ```

### Aangepaste drupal modules

De drupal module `mediwe_texfragments` bevat een aantal drupal standaard componenten die gebruikt worden om klant 
specifieke teksten aan te passen. Installatie (met git) gaat als volgt.

* Ga naar de drupal module directory `cd ../sites/all/modules`
* Installeer de module met Git `git clone https://github.com/CiviCooP/mediwe_textfragments.git`
* Maak aktief met `drush en mediwe_textfragments`


### Na de installatie

Maak ```civi_bartik``` actief voor CiviCRM:

```drush vset civicrmtheme_theme_admin civi_bartik```
