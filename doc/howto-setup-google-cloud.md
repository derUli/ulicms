**Benötige Google Cloud Komponenten:**
* Google App Engine PHP
* SQL Datenbank (MySQL oder MariaDB)
* Google Cloud Storage

**Zum Aufsetzen einer Google Cloud Instanz:**
1. Normale Installation lokal durchführen (z.B. auf XAMPP)
2. Datenbank als SQL Dump exportieren
3. CMS-Config anpassen
3.1 Datenbank-Verbindung (per db_socket)
3.2 $data_storage_root setzen (z.B. gs://my_bucket) (ohne abschließendes Slash
3.3 $data_storage_url setzen(z.B. http://my_bucket.appspot.com)
4. SQL Dump bei Google importieren
5. "content" Ordner in das Google Cloud Storage hochladen, alle Dateien außer *.php öffentlich machen (public-read)
7. alle anderen Dateien und Ordner des UliCMS in die App Engine deployen ("$ gcloud app deploy")
gebenen
8. Konfiguration anpassen (php.ini, Abhängigkeiten in composer.json, etc.)
```
# auf jeden Fall notwendig in der php.ini
google_app_engine.allow_include_gs_buckets = On
```
--


