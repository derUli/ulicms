#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://pc-uli-bs:80/ulicms/ulicms/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    print proxy.cache.clear(name, password)
    
except KeyboardInterrupt:
    pass
