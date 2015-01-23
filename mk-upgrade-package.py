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
                       "Releases", "cms-config.php", "content", "services",
                       ".gitignore", ".htaccess", "installer.aus", "installer",
              "modules", "templates", "fonts.php", "config.php", "contents.css",
              "config.js", "comments")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)
    if reformat:
        print("Refactoring Code...")
        execfile('reformat_code.py')
    print("copying files")
    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)
    update_script = os.path.join(target, "ulicms", "update.php")

    print("preparing update Script")
    if os.path.exists(update_script):
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
try:
    main()
except KeyboardInterrupt:
    pass
