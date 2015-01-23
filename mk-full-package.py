#!/usr/bin/python
import shutil
import argparse
import os
import codecs
def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("-n", "--no-reformat", help="Don't reformat code before creating patch", action="store_true")
    parser.add_argument('-t', '--target', action ="store", dest="target", required = True, help="Target directory")
    args = parser.parse_args()
    target = os.path.abspath(args.target)
    reformat = not args.no_reformat
    source_dir = os.path.dirname(__file__)

    ignore = ('.git', "doc-src", "phpCB-1.0.1-linux", "*.py", "*.pyc",
              "Releases", "cms-config.php", "services", "update.php",
              ".gitignore", "cache")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)
    if reformat:
        print("Refactoring Code...")
        execfile('reformat_code.py')
    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)
    installer_aus_folder = os.path.join(target, "ulicms", "installer.aus")
    installer_folder = os.path.join(target, "ulicms", "installer")

    if os.path.exists(installer_aus_folder):
        os.rename(installer_aus_folder, installer_folder)
        
try:
    main()
except KeyboardInterrupt:
    pass
