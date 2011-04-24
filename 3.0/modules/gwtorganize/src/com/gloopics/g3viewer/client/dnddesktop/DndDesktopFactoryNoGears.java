package com.gloopics.g3viewer.client.dnddesktop;

public class DndDesktopFactoryNoGears extends DndDesktopFactory{
	public DesktopDrop getInstance(DesktopDroppableWidget a_Widget)
	{
		return new DesktopDrop(){};
	}
}
