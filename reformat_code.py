#!/usr/bin/env python

import os, sys

def absoluteFilePaths(directory):
   for dirpath,_,filenames in os.walk(directory):
       for f in filenames:
           yield os.path.abspath(os.path.join(dirpath, f))

rootdir = os.path.join(os.getcwd(), "ulicms")

allFiles = absoluteFilePaths(rootdir)
for path in allFiles:
    if os.path.splitext(path)[1] == ".php":
       os.system("phpCB-1.0.1-linux/phpCB " + path + " > " + path)
       
           
