package com.gloopics.g3viewer.client;

import java.util.List;

import com.allen_sauer.gwt.dnd.client.DragContext;
import com.allen_sauer.gwt.dnd.client.PickupDragController;
import com.google.gwt.user.client.ui.AbsolutePanel;
import com.google.gwt.user.client.ui.Widget;

public class MyPickupDragController extends PickupDragController{
	
	  public MyPickupDragController(AbsolutePanel boundaryPanel, boolean allowDroppingOnBoundaryPanel) {
		  super(boundaryPanel, allowDroppingOnBoundaryPanel);
	  }
	  
	  public DragContext getDragContext()
	  {
		  return context;
	  }

	  public List<Widget> getSelectedWidgets()
	  {
		  return context.selectedWidgets;
	  }

	  public int getSelectedWidgetcount()
	  {
		  return context.selectedWidgets.size();
	  }
	  
}
