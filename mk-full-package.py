#!/usr/bin/env python3
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
    with closing(
        ZipFile(archivename, mode='w', compression=ZIP_DEFLATED, compresslevel=9)
    ) as z:
        for root, dirs, files in os.walk(basedir):
            # NOTE: ignore empty directories
            for fn in files:
                absfn = os.path.join(root, fn)
                zfn = absfn[len(basedir) + len(os.sep) :]  # XXX: relative path
                z.write(absfn, zfn)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('-z', '--zip', help='Compress with zip', action='store_true')
    parser.add_argument(
        '-d', '--delete', help='empty folder if exists', action='store_true'
    )
    parser.add_argument(
        '-t',
        '--target',
        action='store',
        dest='target',
        required=True,
        help='Target directory',
    )
    args = parser.parse_args()
    target = os.path.expanduser(args.target)
    target = os.path.abspath(args.target)
    source_dir = os.path.dirname(__file__)

    ignore = [
        '.gitignore',
        '*.py',
        '.env',
        '.env.foobar',
        '.env.test',
        '.git',
        '.gitignore',
        '.php-cs-fixer.cache',
        '.php-cs-fixer.php',
        '.php_cs.cache',
        '.php_cs.dist',
        '.phpunit.cache',
        '.phpunit.result.cache',
        '.project',
        '.settings',
        'audio',
        'avatars',
        'cache',
        'doc-src',
        'generated',
        'log',
        'phpunit_init.php',
        'report',
        'run-tests.sh',
        'tests',
        'update.php',
        'video',
        'phpunit.xml'
    ]

    # Prepare build
    os.chdir('dist')
    os.system('vendor/bin/robo build:prepare')
    os.chdir('..')

    IGNORE_PATTERNS = shutil.ignore_patterns(*ignore)

    if args.delete and os.path.exists(target):
        print('Folder exists. Truncating.')
        shutil.rmtree(target)
    print('copying files')

    shutil.copytree(source_dir, target, ignore=IGNORE_PATTERNS)
    installer_aus_folder = os.path.join(target, 'dist', 'installer.aus')
    installer_folder = os.path.join(target, 'dist', 'installer')

    if os.path.exists(installer_aus_folder):
        os.rename(installer_aus_folder, installer_folder)

    main_dir = os.path.join(target, 'dist')

    version_file = os.path.join(target, 'dist', 'App', 'UliCMS', 'UliCMSVersion.php')

    print('set build date...')
    with codecs.open(version_file, 'r+', 'utf-8') as f:
        lines = f.readlines()
        f.seek(0)
        f.truncate()
        for line in lines:
            if '{InsertBuildDate}' in line:
                timestamp = str(int(time.time()))
                line = (
                    '     public const BUILD_DATE = '
                    + timestamp
                    + '; // {InsertBuildDate}\r\n'
                )
            print(line)
            f.write(line)

    old_cwd = os.getcwd()

    os.chdir(main_dir)

    old_cwd = os.getcwd()

    # change dir to output dist dir
    os.chdir(main_dir)

    # Remove all non dev composer packages
    os.system('composer install --no-dev --prefer-dist  --optimize-autoloader')

    # Remove all non dev npm packages
    os.system('npm install --omit=dev')    

    # Clean up vendor dir
    os.system('vendor/bin/robo build:optimize-resources')

    # Change dir back
    os.chdir(old_cwd)

    archive_name = os.path.join(target, '..', os.path.basename(target) + '.zip')
    if args.zip:
        print('zipping folder...')
        zipdir(target, archive_name)
        print('removing target folder...')
        shutil.rmtree(target)

try:
    main()
except KeyboardInterrupt:
    pass
