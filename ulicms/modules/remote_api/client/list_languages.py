#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://pc-uli-bs:80/ulicms/ulicms/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    languages = proxy.languages.list(name, password)

    if languages:
        for m in languages:
            print(m)
    else:
        print("Failed")
    
except KeyboardInterrupt:
    pass
