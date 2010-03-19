package com.gloopics.g3viewer.client.dnddesktop;

import com.google.gwt.dom.client.Element;

public class DesktopDropFileIE extends DesktopDropBase{
	
	public DesktopDropFileIE(DesktopDroppableWidget a_Widget){
		super(a_Widget);
		addDropEvents(m_Widget.getElement());
	}
	

	public native void addDropEvents(Element e) /*-{
	var t = this;
	e.attachEvent('ondragenter',
	 	function(e){
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDragEnter(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
			return false;
			
	 	});

    e.attachEvent('ondragover',  
	 	function(e){
    		t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::finishDrag(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
    		return false;
	 	});

    e.attachEvent('ondragleave',
     	function(e){
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDragLeave(Lcom/google/gwt/core/client/JavaScriptObject;)(e)
			return false;
	 	});

    e.attachEvent('ondrop',
     	function(e){
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDrop(Lcom/google/gwt/core/client/JavaScriptObject;)(e)
			return false;
	 	});
	
  }-*/;
	

}
