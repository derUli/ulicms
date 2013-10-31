#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://uhost.kilu.de/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    pages = proxy.pages.list(name, password)

    if pages:
        for m in pages:
            print(m)
    else:
        print("Failed")
    
except KeyboardInterrupt:
    pass
