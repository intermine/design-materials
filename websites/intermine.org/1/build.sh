#!/bin/sh
python setup.py bdist_egg
cd dist
cp TracInterMineTheme-2.0-py2.6.egg $1
cd ..
rm -rd TracInterMineTheme.egg-info/
rm -rd build/
rm -rd dist/