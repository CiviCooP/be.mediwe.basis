# Bijwerken documentatie

Deze documentatie is gemaakt met [mkdocs](http://www.mkdocs.org/) volgens de richtlijnen gebruikt voor [CiviCRM documentatie](https://docs.civicrm.org/dev/en/latest/documentation/#mkdocs). 

De documentatie kan bijgewerkt worden een teksteditor. De gebruikte notatie is Markdown zoals [hier](https://docs.civicrm.org/dev/en/latest/documentation/markdown/) beschreven.

Om de layout te genereren moet mkdocs lokaal geïnstalleerd zijn. Voor Ubuntu kan dit met: 

```
sudo apt-get install python-pip python-wheel
sudo pip install mkdocs mkdocs-material pygments pymdown-extensions
```

(Een uitgebreidere beschrijving met alternatieven kan [hier](https://docs.civicrm.org/dev/en/latest/documentation/#mkdocs) gevonden worden)

De documentatie in een leesbare lay-out kan lokaal bekeken worden door de mkdocs webserver op te starten.

```
mkdocs serve
```

Een html versie van de lay-out wordt gegeneerd met

```
mkdocs build
```

en is terug te vinden in de `../site` subdirectie van deze extensie.

 