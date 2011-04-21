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

__all__ = ['G3Error' , 'G3RequestError' , 'G3InvalidRespError' , 
    'G3UnknownTypeError' , 'G3AuthError' , 'G3UnknownError']

class G3Error(Exception):
    pass

class G3RequestError(G3Error):
    def __init__(self , errDict):
        self.errors = errDict
        self._message = self._getMessage()

    def _getMessage(self):
        ret = ''
        for e in self.errors.items():
            ret += '%s: %r\n' % e
        return ret

    def __str__(self):
        return self._message

class G3InvalidRespError(G3Error):
    pass

class G3UnknownTypeError(G3InvalidRespError):
    pass

class G3AuthError(G3Error):
    pass

class G3UnknownError(G3Error):
    pass
