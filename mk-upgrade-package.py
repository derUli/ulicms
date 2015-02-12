#!/usr/bin/python
import shutil
import argparse
import os
import platform
import codecs
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
    target = os.path.abspath(args.target)
    reformat = not args.no_reformat
    source_dir = os.path.dirname(__file__)

    ignore = ('.git', "doc-src", "phpCB-1.0.1-linux", "*.py", "*.pyc",
                       "Releases", "cms-config.php", "content", "services",
                       ".gitignore", ".htaccess", "installer.aus", "installer",
              "modules", "templates", "fonts.php", "config.php", "contents.css",
              "config.js", "comments")

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
    update_script = os.path.join(target, "ulicms", "update.php")


    if os.path.exists(update_script):
        print("preparing update Script")
        with codecs.open(update_script, 'r+', "utf-8") as f:
            lines = f.readlines()
            f.seek(0)
            f.truncate()
            for line in lines:
                if "unlink" in line and line.startswith("//"):
                    line = line.replace("//", "")
                    line = line.lstrip()
                    
                print(line)
                f.write(line)
    else:
        print("No update.php found")
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
