package com.gloopics.g3viewer.client.dnddesktop;

import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.user.client.ui.Widget;

public interface DesktopDroppableWidget {

	Widget getActualWidget();
	
	void dropFiles(File[] a_File); 
}
