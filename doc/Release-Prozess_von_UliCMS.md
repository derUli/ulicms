	Bevor ein neues Release von UliCMS veröffentlicht werden kann, müssen folgende Punkte sichergestellt werden.
* Die Versionsnummer wurde angepasst.
* Es existiert ein Branch in der Paketquelle für das neue Release.
* Es wurden alle Features, Bugfixes und sonstige Änderungen im Changelog und sofern notwendig auch detailreicher in den Release Notes ("Neue Features in UliCMS X.X").
* Wenn API-Änderungen durchgeführt wurden, die die Kompatiblität mit bestehendem Code brechen müssen alle offiziellen Module die davon betroffen sind angepasst werden, so dass diese wieder lauffähig sind.
* Es müssen für alle neu gebauten Features Unit-Testfälle vorhanden sein.
* Es dürfen keine Unit-Tests fehlschlagen.
* Es müssen Ankündigungstexte zum neuen Release in Deutsch und Englisch vorhanden sein.
* Bei einem Major-Release muss es einen neuen Codenamen geben.

## Erstellung eines Deploys:
* Mit Composer dafür sorgen dass alle benötigten Pakete installiert sind. Development Pakete dürfen nicht installiert sein, um Speicherplatz zu sparen.
* Sicher stellen, dass sich kein Müll im "content" Ordner befindet
* Mit den Python-Scripts Full Package und Upgrade Package als zip erstellen.
* Mit dem Full Package eine frische Installation auf einem Linux-System (LAMP) durchführen, die Ausgabe von Fehlermeldungen muss aktiviert sein.
* Das Upgrade Script nutzen um zuerst eine Testinstanz der vorherigen Version und dann eigene Projekte zu aktualisieren
* Wenn dabei kein Fehler aufgetreten ist dann kann die Veröffentlichung erfolgen.
* git tag erstellen

## Veröffentlichung des Releases
* Full Package, Upgrade Package und Release Notes auf ulicms.de hochladen.
* Ankündigung der neuen Version inklusive Downloadlinks als Newsbeitrag veröffentlichen
* Neue Version in Versionsnummerntabellen auf deutscher und englischer Webseite eintragen.
* Aktuelle Versionsnummer auf Startseite ergänzen
* Anküdigung der neuen Version unter "Downloads" auf der Webseite verlinken.
* Neues Release auf Sourceforge hochladen
* Neuen Ordner in der Paketquelle für die neue Version erstellen und Pakete hochladen
* Neue Version per oneclick_upgrade verfügbar machen.
