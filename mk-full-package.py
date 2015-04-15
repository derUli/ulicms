#!/usr/bin/python
import shutil
import argparse
import os
import codecs
import platform
from contextlib import closing
from zipfile import ZipFile, ZIP_DEFLATED

def zipdir(basedir, archivename):
    assert os.path.isdir(basedir)
    with closing(ZipFile(archivename, "w", ZIP_DEFLATED)) as z:
        for root, dirs, files in os.walk(basedir):
            #NOTE: ignore empty directories
            for fn in files:
                absfn = os.path.join(root, fn)
                zfn = absfn[len(basedir)+len(os.sep):] #XXX: relative path
                z.write(absfn, zfn)

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("-n", "--no-reformat", help="Don't reformat code before creating patch", action="store_true")
    parser.add_argument("-z", "--zip", help="Compress with zip", action="store_true")
    parser.add_argument('-t', '--target', action ="store", dest="target", required = True, help="Target directory")
    args = parser.parse_args()
    target = os.path.expanduser(args.target)
    target = os.path.abspath(args.target)
    reformat = not args.no_reformat
    source_dir = os.path.dirname(__file__)

    ignore = ('.git', "doc-src", "press", "phpCB-1.0.1-linux", "*.py", "*.pyc",
              "Releases", "cms-config.php", "services", "update.php",
              ".gitignore", "cache", "*~", ".settings", ".project", ".buildpath")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)

    operating_system = platform.system()
    supported_os = ["Windows", "Linux"]
    if not operating_system in supported_os and reformat:
        print("Sorry Code refactoring is not supported on your operating system.")
        reformat = False
    if reformat:
        print("Refactoring Code...")
        execfile('reformat_code.py')
    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)
    installer_aus_folder = os.path.join(target, "ulicms", "installer.aus")
    installer_folder = os.path.join(target, "ulicms", "installer")

    if os.path.exists(installer_aus_folder):
        os.rename(installer_aus_folder, installer_folder)
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
