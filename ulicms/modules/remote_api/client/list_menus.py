#!/usr/bin/env python
import xmlrpclib
import datetime
import getpass

proxy = xmlrpclib.ServerProxy("http://uhost.kilu.de/?remote")

try:
    name = raw_input("Name: ")
    password = getpass.getpass("Passwort: ")
    
    menus = proxy.menus.list(name, password)

    if menus:
        for m in menus:
            print(m)
    else:
        print("Failed")
    
except KeyboardInterrupt:
    pass
