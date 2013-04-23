#!/usr/bin/env python

#
#    Author: Jay Deiman
#    Email: admin@splitstreams.com
# 
#    This file is part of pylibgal3.
#
#    pylibgal3 is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    pylibgal3 is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with pylibgal3.  If not, see <http://www.gnu.org/licenses/>.
#


from distutils.core import setup

setup(name='pylibgal3' ,
    version='0.1.6' ,
    author='Jay Deiman' ,
    author_email='admin@splitstreams.com' ,
    url='http://stuffivelearned.org' ,
    description='A library for accessing/manipulating a Gallery 3 install' ,
    packages=['libg3'] ,
    package_dir={'libg3': 'libg3'} ,
    classifiers=[
        'Development Status :: 4 - Beta' ,
        'Intended Audience :: System Administrators' ,
        'Intended Audience :: Information Technology' ,
        'License :: OSI Approved :: GNU General Public License (GPL)' ,
        'Natural Language :: English' ,
        'Operating System :: POSIX' ,
        'Programming Language :: Python' ,
        'Topic :: System :: Systems Administration' ,
        'Topic :: Internet :: WWW/HTTP' ,
        'Topic :: Software Development :: Libraries :: Python Modules' ,
        'Topic :: Software Development :: Libraries' ,
        'Topic :: System' ,
    ]

)
