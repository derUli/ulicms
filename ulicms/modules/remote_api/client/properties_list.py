#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://uhost.kilu.de/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    properties = proxy.properties.list(name, password)

    if properties:
        sorted(properties)
        for m in properties:
            print(str(m) + "=" + str(properties[m]))
    else:
        print("Failed")
    
except KeyboardInterrupt:
    pass
