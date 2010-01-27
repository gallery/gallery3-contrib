package com.gloopics.g3viewer.client.dnddesktop;

public class DndDesktopFactory {

	public DesktopDropBase getInstance(DesktopDroppableWidget a_Widget)
	{
		return new DesktopDropFile(a_Widget);
	}
}
