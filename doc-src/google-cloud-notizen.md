# geeignete Google Cloud Komponenten
* App Engine & Cloud SQL (Vorteil: minimaler Wartungsaufwand, Nachteil: Upload von Dateien schwierig)
* LAMP Stack (Falls die App Engine zu eingeschränkt ist, das wäre dann ein richtiger VServer, wo liegen die Vorteile gegenüber einem VServer z.B. bei all-inkl.com?)

# Deploy
* Bereitstellung von UliCMS Core Code
* Eine Config je Site (yaml Datei)
* Shellscript um ein Deploy aller Sites durchzuführen.
* Wie update.php Script automatisch beim Deploy ausführen?

# Datenbanken

* Damit das Socket für die Verbindung mit der Datenbank angelegt wird, muss die Datenbankverbindung wie folgt in der app.yaml eingetragen werden.

```
env_variables:
  # Replace USER, PASSWORD, DATABASE, and CONNECTION_NAME with the
  # values obtained when configuring your Cloud SQL instance.
  MYSQL_DSN: mysql:unix_socket=/cloudsql/[PROJECT_ID]
  MYSQL_USER: [User]
  MYSQL_PASSWORD: [Password]

beta_settings:
  cloud_sql_instances: "[PROJECT_ID]"
```

# Dateisystem-Zugriffe

File System Zugriff Google Cloud App Engine / PHP
* Direkter Schreibzugriff aufs Filesystem ist von Google nicht vorgesehen.
* Selbst wenn man es schafft, Dateien direkt im Filesystem zu speichern, werden die Dateien beim nächsten Deploy des Docker Containers gelöscht.
* Es gibt jedoch ein Storage, in das man Dateien über ein Pseudo-Protokoll und eine API speichern kann. Diese bleiben auch nach einem Neustart des Docker Containers erhalten.
* Damit Funktionen wie der Upload von Medien und Installation von Modulen durch den Anwender durchgeführt werden können, müssen alle Stellen im Code, an denen Schreibzugriffe auf das Dateisystem erfolgen, angepasst werden, die Pfade müssen durch Storage URLs ersetzt werden.
* Ich halte es für sinnvoll, statt einfach nur die Pfade anzupassen, eine Abstraktions-Klasse für Dateizugriffe zu machen, so dass zukünftige Anpassungen schneller durchführbar sind.
Ein "FileSystemDriver" Interface und auf Basis dessen Klassen, die verschiedene Zugriffsmethoden implementieren. z.B. Dateizugriff per FTP, per NFS oder für andere Clouds, ....

# Sprachcode in URL statt im Filesystem

* Über URL Rewriting ermöglichen die Sprache über einen Pseudo-Ordner zu übergeben
z.B. steht in der Browseradresszeile
/de/meine_seite.html

* Daraus wird per URL Rewriting dann folgende Query
/?seite=meine_seite&format=html&language=de

* Über eine Rewrite-Regel dafür sorgen, dass alle statischen Ressourcen (Bilder, CSS, Javascript, usw.) weiterhin auch über relative Pfade aufrufbar sind.
d.H. in einem <img> Tag steht z.B. content/modules/my_module/my_image.png, es ist im Browser derzeit die URL http://domain.de/de/meine_seite.html geöffnet.
Durch die Regel soll intern aus der Pseudo-Ordner mit der Sprache entfernt werden.
Aus
http://domain.de/de/content/modules/my_module/my_image.png
Macht der Server also intern
http://domain.de/content/modules/my_module/my_image.png
Somit funktionieren relative Pfade weiterhin.

* Alle Funktionen anpassen, die URLs zu Frontend Seiten ausgeben, so dass diese mit einem Sprachprefix beginnen
* Auswahlliste "Lokale Seite" im "Link erstellen" Dialog des Editors anpasse (Sprachprefix vorstellen).

* Wenn in der URL keine Sprache enthalten ist, wird zur Startseite der Standardsprache weitergeleitet.
* optional: Wenn Sprache nicht gesetzt, bevorzugte Sprache des Browsers ermitteln mit browser_default_language? (https://extend.ulicms.de/browser_default_language.html)
