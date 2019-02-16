#!/usr/bin/python
# coding: utf8

import shutil
import argparse
import os
import codecs
import platform
from contextlib import closing
from zipfile import ZipFile, ZIP_DEFLATED
import time

def zipdir(basedir, archivename):
    assert os.path.isdir(basedir)
    with closing(ZipFile(archivename, "w", ZIP_DEFLATED)) as z:
        for root, dirs, files in os.walk(basedir):
            # NOTE: ignore empty directories
            for fn in files:
                absfn = os.path.join(root, fn)
                zfn = absfn[len(basedir) + len(os.sep):]  # XXX: relative path
                z.write(absfn, zfn)

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("-z", "--zip", help="Compress with zip", action="store_true")
    parser.add_argument("-d", "--delete", help="empty folder if exists", action="store_true")
    parser.add_argument('-t', '--target', action="store", dest="target", required=True, help="Target directory")
    args = parser.parse_args()
    target = os.path.expanduser(args.target)
    target = os.path.abspath(args.target)
    source_dir = os.path.dirname(__file__)

    ignore = ('.git', "doc-src", "press", "phpCB-1.0.1-linux", "*.py", "*.pyc",
              "Releases", "cms-config.php", "services", "update.php",
              ".gitignore", "cache", "*~", ".settings", ".project",
              ".buildpath", "tests", "run-tests.sh", "run-tests.bat",
              "run-tests.xampp.mac.sh", ".pydevproject", "CMSConfig.php", "log",
              "configurations", ".phpunit.result.cache", "nbproject")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)
    if args.delete and os.path.exists(target):
        print("Folder exists. Truncating.")
        shutil.rmtree(target)
    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)
    installer_aus_folder = os.path.join(target, "ulicms", "installer.aus")
    installer_folder = os.path.join(target, "ulicms", "installer")

    if os.path.exists(installer_aus_folder):
        os.rename(installer_aus_folder, installer_folder)

    main_dir = os.path.join(target, "ulicms")

    version_file = os.path.join(target, "ulicms", "UliCMSVersion.php")

    if os.path.exists(version_file):
        print("set build date...")
        with codecs.open(version_file, 'r+', "utf-8") as f:
            lines = f.readlines()
            f.seek(0)
            f.truncate()
            for line in lines:
                if "{InsertBuildDate}" in line:
                    timestamp = str(int(time.time()))
                    line = "            $this->buildDate = " + timestamp + "; // {InsertBuildDate}\r\n"
                print(line)
                f.write(line)

    # Composer packages zu Deploy hinzufügen
    os.system("php ulicms/composer install --working-dir=" + main_dir + "/ --no-dev")

    old_cwd = os.getcwd()

    # Install npm packages
    # TODO: is there are a way to specify a working dir like used for composer (code above)?
    os.chdir("ulicms")
    os.system("npm install")
    os.chdir(old_cwd)

    archive_name = os.path.join(target, "..", os.path.basename(target) + ".zip")
    if args.zip:
        print("zipping folder...")
        zipdir(target, archive_name)
        print("removing target folder...")
        shutil.rmtree(target)

try:
    main()
except KeyboardInterrupt:
    pass
