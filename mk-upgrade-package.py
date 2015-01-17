import shutil
import argparse
import os
import codecs
def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--target', action ="store", dest="target", required = True)
    args = parser.parse_args()
    target = os.path.abspath(args.target)
    source_dir = os.path.dirname(__file__)

    ignore = ('.git', "doc-src", "phpCB-1.0.1-linux", "*.py", "*.pyc",
                       "Releases", "cms-config.php", "content", "services",
                       ".gitignore", ".htaccess", "installer.aus", "installer",
              "modules", "templates", "fonts.php", "config.php", "contents.css",
              "config.js")

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)

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
                if "unlink(\"update.php" in line and line.startswith("//"):
                    line = line = line[2:]
                print(line)
                f.write(line)
try:
    main()
except KeyboardInterrupt:
    pass
