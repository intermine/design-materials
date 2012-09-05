#!/usr/bin/python
# -*- coding: utf -*-

from setuptools import setup

setup(
    name = 'TracInterMineTheme',
    version = '2.0.1',
    packages = ['interminetheme'],
    package_data = { 'interminetheme': ['templates/*.html', 'htdocs/*.css', 'htdocs/favicon.ico', 'htdocs/img/*.png', 'htdocs/img/*.gif', 'htdocs/img/*.jpg' ] },

    author = 'Radek Stepan',
    author_email = 'radek@intermine.org',
    description = 'An InterMine 2.0 Trac site theme (end of 2011).',
    license = 'BSD',
    keywords = 'trac plugin theme',
    url = 'https://github.com/intermine/design-materials/tree/master/websites/intermine.org',
    classifiers = [
        'Framework :: Trac',
    ],
    
    install_requires = ['Trac', 'TracThemeEngine>=2.0'],

    entry_points = {
        'trac.plugins': [
            'interminetheme.theme = interminetheme.theme',
        ]
    },
)
