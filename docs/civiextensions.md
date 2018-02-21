|Extensie identifier   | Extensie waar deze afhankelijk van is | Drush installatie |
|----------------------|:-----------------------------:|-------------------------------------------------------------------|
|be.mediwe.basis       |geen|```drush cvapi Extension.download key="be.mediwe.basis" url="https://github.com/CiviCooP/be.mediwe.basis/archive/master.zip"```|
|be.mediwe.interneui   |be.mediwe.basis|``` drush cvapi Extension.download key="be.mediwe.interneui" url="https://github.com/CiviCooP/be.mediwe.interneui/archive/master.zip"```|
|be.mediwe.smartfragments   |Drupal module mediwe_textfragments|``` drush cvapi Extension.download key="be.mediwe.smartfragments" url="https://github.com/CiviCooP/be.mediwe.smartfragments/archive/master.zip"```|
|org.civicoop.civirules|geen|``` drush cvapi Extension.download key="org.civicoop.civirules" url="https://github.com/CiviCooP/org.civicoop.civirules/archive/1.17.zip" install="1"```|
|org.civicoop.emailapi |org.civicoop.civirules|``` drush cvapi Extension.download key="org.civicoop.emailapi" url="https://github.com/CiviCooP/org.civicoop.emailapi/archive/V1.12.zip" install="1"```|
