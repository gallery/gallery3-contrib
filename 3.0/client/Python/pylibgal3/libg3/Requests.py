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

__all__ = ['BaseRequest' , 'GetRequest' , 'PostRequest' , 'PutRequest' , 
    'DeleteRequest']

from urllib2 import Request
from urllib import quote
import os , json , types

class BaseRequest(Request):
    def __init__(self , url , apiKey , data=None , headers={} , 
            origin_req_host=None , unverifiable=False):
        url = url.encode('utf-8')
        if apiKey is not None:
            headers['X-Gallery-Request-Key'] = apiKey
        if data is not None:
            if isinstance(data , dict):
                data = 'entity=%s' % quote(json.dumps(data , 
                    separators=(',' , ':')))
            elif type(data) not in types.StringTypes:
                raise TypeError('Invalid type for data.  It should be '
                    'a "dict" or "str", not %s' % type(data))
            headers['Content-Length'] = str(len(data))
        Request.__init__(self , url , data , headers , origin_req_host ,
            unverifiable)

class GetRequest(BaseRequest):
    def __init__(self , url , apiKey , data=None , headers={} , 
            origin_req_host=None , unverifiable=False):
        headers['X-Gallery-Request-Method'] = 'get'
        BaseRequest.__init__(self , url , apiKey , data , headers , 
            origin_req_host , unverifiable)

class PostRequest(BaseRequest):
    def __init__(self , url , apiKey , data , headers={} , 
            origin_req_host=None , unverifiable=False):
        headers['X-Gallery-Request-Method'] = 'post'
        if 'Content-Type' not in headers:
            headers['Content-Type'] = 'application/x-www-form-urlencoded'
        BaseRequest.__init__(self , url , apiKey , data , headers , 
            origin_req_host , unverifiable)

class PutRequest(BaseRequest):
    def __init__(self , url , apiKey , data=None , headers={} , 
            origin_req_host=None , unverifiable=False):
        headers['X-Gallery-Request-Method'] = 'put'
        BaseRequest.__init__(self , url , apiKey , data , headers , 
            origin_req_host , unverifiable)

class DeleteRequest(BaseRequest):
    def __init__(self , url , apiKey , data=None , headers={} , 
            origin_req_host=None , unverifiable=False):
        headers['X-Gallery-Request-Method'] = 'delete'
        BaseRequest.__init__(self , url , apiKey , data , headers , 
            origin_req_host , unverifiable)
