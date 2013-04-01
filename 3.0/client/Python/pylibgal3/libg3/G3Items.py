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

__all__ = ['Album' , 'Image' , 'LocalImage' , 'RemoteImage' , 'LocalMovie' , 
    'RemoteMovie' , 'getItemFromResp' , 'getItemsFromResp']

from datetime import datetime
import weakref , types , os , mimetypes , re
try:
    import json
except:
    try:
        import simplejson
    except ImportError , e:
        raise ImportError('You must have either the "json" or "simplejson"'
            'library installed!')


class BaseRemote(object):
    def __init__(self , respObj , weakGalObj , weakParent=None):
        self._setAttrItems(respObj.items())
        if 'entity' in respObj:
            self._setAttrItems(respObj['entity'].items())
        self._weakParent = None
        if weakParent is not None:
            self._weakParent = weakParent
        self._weakGal = weakGalObj
        self.fh = None
        self._postInit()

    def __str__(self):
        try:
            return self.title
        except:
            pass
        return self.name

    def __getattr__(self , name):
        """
        A bit of magic to make the retrieval of member objects lazy
        """
        # Process the specials
        if name == 'members':
            self.members = self._getMemberObjects()
            return self.members
        if name == 'tags':
            self.tags = self._getTags()
            return self.tags
        if name == 'comments':
            self.comments = self._getComments()
            return self.comments
        # Process the weak reference calls
        if name == '_gal':
            return self._weakGal()
        if name == 'parent' and self._weakParent is not None:
            return self._weakParent()
        # Process the generic items
        urlAttr = '_%s' % name
        # Call __getattribute__ to prevent loops
        attr = object.__getattribute__(self , urlAttr)
        if attr is not None and attr.startswith('http'):
            obj = self._getUrlObject(attr)
            setattr(self , name , obj)
            return obj
        raise AttributeError(name)

    def _postInit(self):
        """
        This can be overridden in subclasses to do any special initialization
        at the end of the __init__ call
        """
        pass

    def _setAttrItems(self , d):
        for k , v in d:
            if k == 'entity':
                # Skip it
                continue
            if (type(v) in types.StringTypes and v.startswith('http') and 
                    'url' not in k) or k == 'members':
                setattr(self , '_%s' % k , v)
            else:
                setattr(self , k , v)

    def _getMemberObjects(self):
        """
        This returns the appropriate objects for each child of this object.
        The default "members" attribute only contains the URLs for the 
        children of this object.  This returns a list of the actual objects.
        """
        memObjs = self._gal.getItemsForUrls(self._members , self)
        return memObjs

    def _getTags(self):
        """
        Returns the list of tag objects

        returns(list[Tag])
        """
        # First, I want just the actual tag itself, not the RESTy "tag_item",
        # so I'm going to modify the urls to save a step
        ret = []
        urls = []
        for url in self.relationships['tags']['members']:
            m = re.match('^(.*?/tag)_item(/\d+),\d+$' , url)
            urls.append('%s%s' % tuple(m.groups()))
        if urls:
            for url in urls:
                resp = self._gal.getRespFromUrl(url)
                ret.append(getItemFromResp(resp , self._gal , self))
        return ret

    def _getComments(self):
        """
        Returns a list of the Comment items for this item
        
        returns(list[Comment])  : Returns a list of Comment objects
        """
        ret = []
        # I can't use the shortcut I did for tags so I need to get the list
        # of comments in the first call and then create the objects with
        # the calls thereafter
        commListUrl = self.relationships['comments']['url']
        resp = self._gal.getRespFromUrl(commListUrl)
        tmpObj = json.loads(resp.read())
        for url in tmpObj['members']:
            resp = self._gal.getRespFromUrl(url)
            ret.append(getItemFromResp(resp , self._gal , self))
        return ret
        
    def _getUrlObject(self , url):
        """
        This returns the album cover image
        """
        resp = self._gal.getRespFromUrl(url)
        return getItemFromResp(resp , self._gal , self)

    def getCrDT(self):
        """
        Returns a datetime object for the time this item was created
        """
        if hasattr(self , 'created'):
            return datetime.fromtimestamp(int(self.created))
        return None

    def getUpdDT(self):
        """
        Returns a datetime object for the time this item was last updated
        """
        if hasattr(self , 'updated'):
            return datetime.fromtimestamp(int(self.updated))
        return None

    def delete(self):
        """
        Deletes this

        returns(tuple(status , msg))    : Returns a tuple of a boolean status
                                          and a message if there is an error
        """
        return self._gal.deleteItem(self)

    def update(self , title=None , description=None):
        """
        Update either the title, the description or both
        
        title(str)                      : The new item title
        description(str)                : The new item description

        returns(tuple(status , msg))    : Returns a tuple of a boolean status
                                          and a message if there is an error
        """
        if title is not None:
            self.title = title
        if description is not None:
            self.description = description
        return self._gal.updateItem(self)

    def tag(self , tagName):
        """
        Tag this item with the string "tagName"

        tagName(str)        : The actual tag name

        returns(Tag)        : The tag that was created
        """
        return self._gal.tagItem(self , tagName)

class Album(BaseRemote):
    def addImage(self , image , title='' , description='' , name=''):
        """
        Add a LocalImage object to the album

        image(LocalImage)       : The image to upload

        returns(RemoteImage)    : The RemoteImage object that was created
        """
        if not isinstance(image , LocalImage):
            raise TypeError('%r is not of type LocalImage' % image)
        return self._gal.addImage(self , image , title , description , name)

    def addMovie(self , movie , name='' , title='' , description=''):
        """
        Adds a LocalMovie object to the album
        
        movie(LocalMovie)       : The movie to upload

        returns(RemoteMovie)    : The RemoteMovie object that was created
        """
        return self._gal.addMovie(self , movie , title , description , name)

    def addAlbum(self , albumName , title , description=''):
        """
        Add a subalbum to this album

        albumName(str)  : The name of the new album
        title(str)      : The album title
        description(str): The album description

        returns(Album)  : The Album object that was created
        """
        return self._gal.addAlbum(self , albumName , title , description)

    def setCover(self , image):
        """
        Sets the album cover to the RemoteImage

        image(RemoteImage)  : The image to set as the album cover
        
        returns(tuple(status , msg))    : Returns a tuple of a boolean status
                                          and a message if there is an error
        """
        return self._gal.setAlbumCover(self , image)

    def getAlbums(self):
        """
        Return a list of the sub-albums in this album

        returns(list[Album])  : A list of Album objects
        """
        return self._getByType('album')
    Albums = property(getAlbums)

    def getImages(self):
        """
        Return a list of the images in this album

        returns(list[RemoteImage])  : A list of RemoteImages
        """
        return self._getByType('photo')
    Images = property(getImages)

    def getMovies(self):
        """
        Return a list of the movies in this album

        returns(list[RemoteMovie])  : A list of RemoteMovie objects
        """
        return self._getByType('movie')
    Movies = property(getMovies)

    def getRandomImage(self , direct=True):
        """
        Returns a random RemoteImage object for the album.  If "direct" is
        False, a random image can be pulled from nested albums.

        direct(bool)        : If set to False, the image may be pulled from
                              a sub-album

        returns(RemoteImage)    : Returns a RemoteImage instance
        """
        return self._gal.getRandomImage(self , direct)

    def _getByType(self , t):
        ret = []
        for m in self.members:
            if m.type == t:
                ret.append(m)
        return ret

class Image(object):
    contentType = ''

class LocalImage(Image):
    def __init__(self , path , replaceSpaces=True):
        if not os.path.isfile(path):
            raise IOError('%s is not a file' % path)
        self.path = path
        self.replaceSpaces = replaceSpaces
        self.Filename = os.path.basename(self.path)
        self.fh = None
        self.type = 'photo'

    def __del__(self):
        if self.fh:
            try:
                self.fh.close()
            except:
                pass

    def setContentType(self , ctype=None):
        if ctype is not None:
            self.contentType = ctype
        self.contentType = mimetypes.guess_type(self.getFileContents())[0] or \
            'application/octet-stream'
    def getContentType(self):
        if not self.contentType:
            self.setContentType()
        return self.contentType
    ContentType = property(getContentType , setContentType)

    def setFilename(self , name):
        self.filename = name
        if self.replaceSpaces:
            self.filename = self.filename.replace(' ' , '_')
    def getFilename(self):
        return self.filename
    Filename = property(getFilename , setFilename)

    def getFileContents(self):
        """
        Gets the entire contents of the file
        
        returns(str)    : File contents
        """
        if self.fh is None:
            self.fh = open(self.path , 'rb')
        self.fh.seek(0)
        return self.fh.read()

    def getUploadContent(self):
        """
        This will return a string containing the MIME headers and the actual
        binary content to be uploaded
        """
        ret = 'Content-Disposition: form-data; name="file"; '
        ret += 'filename="%s"\r\n' % self.filename
        ret += 'Content-Type: %s\r\n' % self.ContentType
        ret += 'Content-Transfer-Encoding: binary\r\n'
        ret += '\r\n'
        ret += self.getFileContents() + '\r\n'
        return ret

    def close(self):
        try:
            self.fh.close()
        except:
            pass

class RemoteImage(BaseRemote , Image):
    def addComment(self , comment):
        """
        Comment on this item with the string "comment"

        comment(str)        : The comment

        returns(Comment)        : The comment that was created
        """
        return self._gal.addComment(self , comment)

    def read(self , length=None):
        if not self.fh:
            resp = self._gal.getRespFromUrl(self.file_url)
            self.fh = resp
        if length is None:
            return self.fh.read()
        return self.fh.read(int(length))

    def close(self):
        try:
            self.fh.close()
        except:
            pass

    def getResizeHandle(self):
        """
        Returns a file-like object (specifically a urllib2.addinfourl) handle 
        to the "resize" version of the image
        
        returns(urllib2.addinfourl) : A file-like object handle for retrieving
                                      the resized image
        """
        if hasattr(self , 'resize_url'):
            resp = self._gal.getRespFromUrl(self.resize_url)
            return resp
        return None

    def getThumbHandle(self):
        """
        Returns a file-like object (specifically a urllib2.addinfourl) handle 
        to the "thumbnail" version of the image
        
        returns(urllib2.addinfourl) : A file-like object handle for retrieving
                                      the thumbnail image
        """
        if hasattr(self , 'thumb_url'):
            resp = self._gal.getRespFromUrl(self.thumb_url)
            return resp
        return None

class LocalMovie(LocalImage):
    def __init__(self , path , replaceSpaces=True):
        LocalImage.__init__(self , path , replaceSpaces)
        self.type = 'movie'

class RemoteMovie(RemoteImage):
    pass

class Tag(BaseRemote):
    """
    A simple class to represent a tag
    """
    def __str__(self):
        return self.name

    def _postInit(self):
        if hasattr(self , 'count'):
            self.count = int(self.count)
        self.type = 'tag'

    def tag(self , tagName):
        raise G3Error('You cannot tag a tag')

class Comment(BaseRemote):
    """
    A class to represent a comment
    """
    def __str__(self):
        return self.text

    def _postInit(self):
        # Change the "item" attribute to "parent" since that's what it is
        # I'm doing this to address overall consistency
        self._parent = None
        if hasattr(self , '_item'):
            self._parent = getattr(self , '_item')

    def tag(self , tagName):
        raise G3Error('You cannot tag a comment')

def getItemFromResp(response , galObj , parent=None):
    """
    Returns the appropriate item given the "addinfourl" response object from
    the urllib2 request

    response(addinfourl|dict)   : The response object from the urllib2 request 
                                  or a dict that has already been converted
                                  (usually when called from getItemsFromResp)
    galObj(Gallery3)            : The gallery object this is associated with
    parent(Album)               : The parent object for this item 

    returns(BaseRemote)         : Returns an implemenation of BaseRemote
    """
    galObj = weakref.ref(galObj)
    if parent is not None:
        parent = weakref.ref(parent)
    if isinstance(response , dict):
        respObj = response
    else:
        respObj = json.loads(response.read())
    if 'count' in respObj['entity']:
        # This is a tag.  It doesn't have the same items as regular objects
        return Tag(respObj , galObj , parent)
    if 'text' in respObj['entity']:
        # This is a comment.  It also does not have the same items as
        # regular objects
        return Comment(respObj , galObj , parent)
    try:
        t = respObj['entity']['type']
    except:
        raise G3InvalidRespError('Response contains no "entity type": %r' % 
            response)
    if t == 'album':
        return Album(respObj , galObj , parent)
    elif t == 'photo':
        return RemoteImage(respObj , galObj , parent)
    elif t == 'movie':
        return RemoteMovie(respObj , galObj , parent)
    else:
        raise G3UnknownTypeError('Unknown entity type: %s' % t)

def getItemsFromResp(response , galObj , parent=None):
    """
    This takes the raw response with a list of items and returns a list of
    the corresponding objects

    response(addinfourl|dict)   : The response object from the urllib2 request 
                                  or a dict that has already been converted
                                  (usually when called from getItemsFromResp)
    galObj(Gallery3)            : The gallery object this is associated with
    parent(Album)               : The parent object for this item 

    returns(list[BaseRemote])   : Returns a list of BaseRemote objects
    """
    ret = []
    lResp = json.loads(response.read())
    if not isinstance(lResp , list):
        lResp = list(lResp)
    for resp in lResp:
        ret.append(getItemFromResp(resp , galObj , parent))
    return ret
