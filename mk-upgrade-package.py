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
    with closing(ZipFile(archivename, mode="w", compression=ZIP_DEFLATED, compresslevel=9)) as z:
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
    parser.add_argument("-c", "--with-config-js", help="include config.js", action="store_true")
    parser.add_argument('-t', '--target', action="store", dest="target", required=True, help="Target directory")

    args = parser.parse_args()
    target = os.path.expanduser(args.target)
    target = os.path.abspath(args.target)
    source_dir = os.path.dirname(__file__)

    ignore = ['.git', "doc-src", "press", "phpCB-1.0.1-linux", "*.py", "*.pyc",
                       "Releases", "cms-config.php", "content", "services",
                       ".gitignore", ".htaccess", "installer.aus", "installer",
              "modules", "templates", "contents.css",
              "comments", "*~", ".settings", ".project", ".buildpath",
              "tests", "run-tests.sh", "run-tests.bat",
              "run-tests.xampp.mac.sh", ".pydevproject", "CMSConfig.php", "log",
              "configurations", ".phpunit.result.cache", "nbproject", "report", 
              "avatars", ".php_cs.cache", ".php_cs.dist", ".phplint-cache", ".php-cs-fixer.cache"]
    if not args.with_config_js:
        ignore.append("config.js")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)
    if args.delete and os.path.exists(target):
        print("Folder exists. Truncating.")
        shutil.rmtree(target)

    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)

    update_script = os.path.join(target, "dist", "update.php")

    
    content_dir_from = os.path.join(source_dir, "dist", "App", "Models", "Content")
    content_dir_to = os.path.join(target, "dist", "App", "Models", "Content")
    
    shutil.copytree(content_dir_from, content_dir_to, ignore=IGNORE_PATTERNS)

    modules_dir_from = os.path.join(source_dir, "dist", "content", "modules")
    modules_dir_to = os.path.join(target, "dist", "content", "modules")

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

    version_file = os.path.join(target, "dist", "App", "non_namespaced", "UliCMSVersion.php")

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

    main_dir = os.path.join(target, "dist")

    # Install all non-dev composer packages
    os.system("composer install --working-dir=" + main_dir + "/ --no-dev")

    old_cwd = os.getcwd()

    # Install npm packages
    # TODO: is there are a way to specify a working dir like used for composer (code above)?
    os.chdir("dist")
    os.system("npm install --omit=dev")

    os.system("php-legal-licenses generate --hide-version")
    os.system("license-report --only=prod --output=json > licenses.json")
    shutil.copy("../doc/changelog.txt", "content/modules/core_info")
    
    os.chdir(old_cwd)

    if args.zip:
        print("zipping folder...")
        zipdir(target, archive_name)
        print("removing target folder...")
        shutil.rmtree(target)
try:
    main()
except KeyboardInterrupt:
    pass
