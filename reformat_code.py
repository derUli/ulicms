#!/usr/bin/env python

import os, sys, subprocess

def absoluteFilePaths(directory):
   for dirpath,_,filenames in os.walk(directory):
       for f in filenames:
           yield os.path.abspath(os.path.join(dirpath, f))

rootdir = os.path.join(os.getcwd(), "ulicms")

allFiles = absoluteFilePaths(rootdir)
for path in allFiles:
    if os.path.splitext(path)[1] == ".php":
        output = subprocess.Popen(["phpCB-1.0.1-linux/phpCB", path], stdout=subprocess.PIPE).communicate()[0]
        handle = open(path, "wb")
        handle.write(output)
        handle.close()

       
           
