package com.gloopics.g3viewer.client.dnddesktop;

public class DndDesktopFactory {

	public DesktopDrop getInstance(DesktopDroppableWidget a_Widget)
	{
		return new DesktopDropFile(a_Widget);
	}
}
