#!/usr/bin/env python
import xmlrpclib
import datetime

proxy = xmlrpclib.ServerProxy("http://pc-uli-bs:80/ulicms/ulicms/?remote")


print(proxy.demo.fortune().strip())
