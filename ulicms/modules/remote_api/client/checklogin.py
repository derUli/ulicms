#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://pc-uli-bs:80/ulicms/ulicms/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    if proxy.auth.login(name, password):
        print("Login erfolgreich!")
    else:
        print("Login fehlgeschlagen!")
except KeyboardInterrupt:
    pass
