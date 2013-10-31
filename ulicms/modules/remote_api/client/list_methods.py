#!/usr/bin/env python
import xmlrpclib
import datetime

proxy = xmlrpclib.ServerProxy("http://pc-uli-bs:80/ulicms/ulicms/?remote")

methods = proxy.system.listMethods()
for m in methods:
    print(m)



