package com.gloopics.g3viewer.client.dnddesktop;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.dom.client.Element;
import com.google.gwt.gears.client.Factory;
import com.google.gwt.gears.client.desktop.Desktop;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.user.client.Window.Navigator;
import com.google.gwt.user.client.ui.Widget;

public class DesktopDropFile  extends DesktopDropBase{
	
	public DesktopDropFile(DesktopDroppableWidget a_Widget){
		super(a_Widget);
		String nav = Navigator.getUserAgent().toLowerCase();
		
		addDropEvents(m_Widget.getElement());
	}
	
	
	public native void addDropEvents(Element e) /*-{
	var t = this;

	e.addEventListener('dragenter',
	 	function(e)
	 	{
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDragEnter(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 	}
		, false);


    e.addEventListener('dragleave', 
	 	function(e)
	 	{
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDragLeave(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 	}
		, false);
		
    e.addEventListener('dragexit', 
	 	function(e)
	 	{
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDragLeave(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 	}
		, false);
		
    e.addEventListener('dragover',  
	 	function(e)
	 	{
			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::finishDrag(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 	}
		, false);
		
	e.addEventListener('dragdrop',
	 		function(e)
	 		{
    			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDrop(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 		}
    		, false);

	e.addEventListener('drop',
	 		function(e)
	 		{
    			t.@com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile::onDrop(Lcom/google/gwt/core/client/JavaScriptObject;)(e);
	 		}
    		, false);

  }-*/;	

}
