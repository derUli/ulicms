#!/usr/bin/env python
import xmlrpclib
import datetime

proxy = xmlrpclib.ServerProxy("http://uhost.kilu.de/?remote")


print(proxy.demo.xmlrpc_calls_hook())
