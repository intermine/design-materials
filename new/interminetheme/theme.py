#!/usr/bin/python
# -*- coding: utf -*-

from trac.core import *

from themeengine.api import ThemeBase

class InterMineTheme(ThemeBase):
    """An InterMine 2.0 Trac site theme (end of 2011)"""

    template = htdocs = css = screenshot = True
    
