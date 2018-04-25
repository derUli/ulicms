**Benötige Google Cloud Komponenten:**
* Google App Engine PHP
* SQL Datenbank (MySQL oder MariaDB)
* Google Cloud Storage

**Zum Aufsetzen einer Google Cloud Instanz:**
1. UliCMS Installation lokal durchführen (z.B. auf XAMPP)

2. Datenbank als SQL Dump exportieren

3. CMS-Config anpassen
    * Datenbank-Verbindung (per $db_socket)
    * $data\_storage\_root setzen (z.B. gs://my\_bucket) (ohne abschließendes Slash)
    * $data\_storage\_url setzen(z.B. http://my\_bucket.appspot.com) (ohne abschließendes Slash)

4. SQL Dump bei Google importieren

5. "content" Ordner in das Google Cloud Storage hochladen, alle Dateien außer *.php öffentlich machen (ACL: public-read)

6. Konfiguration erstellen bzw. anpassen (php.ini, Abhängigkeiten in composer.json, etc.)

```
# auf jeden Fall notwendig in der php.ini
google_app_engine.allow_include_gs_buckets = On
```

7. alle anderen Dateien und Ordner des UliCMS in die App Engine deployen ("$ gcloud app deploy")
