package com.gloopics.g3viewer.client.dnddesktop;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.gears.client.Factory;
import com.google.gwt.gears.client.desktop.Desktop;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.user.client.ui.Widget;

public abstract class DesktopDropBase implements DesktopDrop{
	private final DesktopDroppableWidget m_DropFile;
	protected final Widget m_Widget;
	private final Desktop m_Desktop;

	public DesktopDropBase (DesktopDroppableWidget a_Widget){
		m_DropFile = a_Widget;
		m_Widget = a_Widget.getActualWidget();
		
		m_Desktop = Factory.getInstance().createDesktop();
	}
	
	public void onDragEnter(JavaScriptObject e)
	{
		m_Widget.addStyleName("drop-target");
		setDropEffect(m_Desktop, e);
		finishDrag(e);
	}

	public final native void finishDrag(JavaScriptObject e) /*-{
	if (e.stopPropagation) e.stopPropagation();
	else e.cancelBubble = true;
	if (e.preventDefault) e.preventDefault(); 
	else e.returnValue = false;     
	}-*/;

	public void onDragLeave(JavaScriptObject e)
	{
		m_Widget.removeStyleName("drop-target");
		finishDrag(e);
	
	}
	
	public void onDrop(JavaScriptObject e)
	{
		File[] files = getDragData(m_Desktop, e);
		if (files != null)
		{
			m_DropFile.dropFiles(files);
		}
		
		onDragLeave(e);
	}

	public final native File[] getDragData(Desktop d, JavaScriptObject e) /*-{
		var data = d.getDragData(e, 'application/x-gears-files');
		return data && data.files;
	  }-*/;
	
	public final native File[] setDropEffect(Desktop d, JavaScriptObject e) /*-{
	var data = d.setDropEffect(e, 'copy');
  }-*/;
}
