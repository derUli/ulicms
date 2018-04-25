#!/usr/bin/python
# coding: utf8

import shutil
import argparse
import os
import platform
import codecs
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
                       "Releases", "cms-config.php", "content", "services",
                       ".gitignore", ".htaccess", "installer.aus", "installer",
              "modules", "templates", "contents.css",
              "config.js", "comments", "*~", ".settings", ".project", ".buildpath",
              "tests", "run-tests.sh", "run-tests.bat",
              "run-tests.xampp.mac.sh", ".pydevproject", "CMSConfig.php")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)
    if args.delete and os.path.exists(target):
        print("Folder exists. Truncating.")
        shutil.rmtree(target)

    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)

    update_script = os.path.join(target, "ulicms", "update.php")

    content_dir_from = os.path.join(source_dir, "ulicms", "classes", "objects", "content")
    content_dir_to = os.path.join(target, "ulicms", "classes", "objects", "content")
    shutil.copytree(content_dir_from, content_dir_to, ignore=IGNORE_PATTERNS)
    modules_dir_from = os.path.join(source_dir, "ulicms", "classes", "objects", "modules")
    modules_dir_to = os.path.join(target, "ulicms", "classes", "objects", "modules")
    shutil.copytree(modules_dir_from, modules_dir_to, ignore=IGNORE_PATTERNS)

    modules_dir_from = os.path.join(source_dir, "ulicms", "content", "modules")
    modules_dir_to = os.path.join(target, "ulicms", "content", "modules")

    os.makedirs(modules_dir_to)
    prefixed = [filename for filename in os.listdir(modules_dir_from) if filename.startswith("core_")]
    for prefix in prefixed:
        shutil.copytree(os.path.join(modules_dir_from, prefix), os.path.join(modules_dir_to, prefix))

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
    
    archive_name = os.path.join(target, "..", os.path.basename(target) + ".zip")

    main_dir = os.path.join(target, "ulicms")

    # Composer packages zu Deploy hinzufügen
    os.system("php ulicms/composer install --working-dir=" + main_dir + "/ --no-dev")

    if args.zip:
        print("zipping folder...")
        zipdir(target, archive_name)
        print("removing target folder...")
        shutil.rmtree(target)
try:
    main()
except KeyboardInterrupt:
    pass
