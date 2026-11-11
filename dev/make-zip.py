#!/usr/bin/env python3
"""The theme as a zip to upload in WordPress (Aspetto > Temi > Aggiungi > Carica tema).

    python3 dev/make-zip.py [out.zip]

Takes the files git knows, under the folder tasche-piene-divi-child/ (the folder the theme has always
had, so the upload replaces the version already installed). dev/ stays out: it only serves to write
content/ again.
"""
import os
import re
import subprocess
import sys
import zipfile

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FOLDER = 'tasche-piene-divi-child'
SKIP = ('dev/', '.gitignore')

version = re.search(r'^Version:\s*(\S+)', open(os.path.join(ROOT, 'style.css'), encoding='utf8').read(), re.M).group(1)
out = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, '..', '%s-%s.zip' % (FOLDER, version))
files = sorted(
    name for name in subprocess.check_output(['git', '-C', ROOT, 'ls-files', '--cached'], text=True).splitlines()
    if not name.startswith(SKIP) and name not in SKIP and os.path.isfile(os.path.join(ROOT, name))
)
folders = set()
for name in files:
    folder = os.path.dirname(name)
    while folder:
        folders.add(folder)
        folder = os.path.dirname(folder)

with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as archive:
    archive.writestr(FOLDER + '/', '')
    for folder in sorted(folders):
        archive.writestr('%s/%s/' % (FOLDER, folder), '')
    for name in files:
        archive.write(os.path.join(ROOT, name), '%s/%s' % (FOLDER, name))

print('%s: %d files, %d bytes' % (os.path.abspath(out), len(files), os.path.getsize(out)))
