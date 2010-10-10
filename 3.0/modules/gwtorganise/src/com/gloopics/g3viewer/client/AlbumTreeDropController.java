package com.gloopics.g3viewer.client;

import com.allen_sauer.gwt.dnd.client.DragContext;
import com.allen_sauer.gwt.dnd.client.VetoDragException;
import com.allen_sauer.gwt.dnd.client.drop.DropController;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.user.client.ui.Widget;

public class AlbumTreeDropController implements DropController{

	/**
	 * the tree
	 */
	private final Album m_Album;
	
	public AlbumTreeDropController(Album a_Album){
		m_Album = a_Album;
	}
	
	@Override
	public Widget getDropTarget() {
		// TODO Auto-generated method stub
		return m_Album.getWidget();
	}

	@Override
	public void onDrop(DragContext context) {
		JSONArray jsa = new JSONArray();
		
		int i = 0;
		for (Widget widget : context.selectedWidgets){
			if (widget instanceof Item){
				jsa.set(i, new JSONNumber(((Item)widget).getID()));
				i++;
			}
		}
		m_Album.moveTo(jsa);
		// context.
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onEnter(DragContext context) {
		m_Album.getWidget().addStyleName("drop-target");
		//m_Album.g
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onLeave(DragContext context) {
		// TODO Auto-generated method stub
		m_Album.getWidget().removeStyleName("drop-target");
	}

	@Override
	public void onMove(DragContext context) {
		// m_Album.
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onPreviewDrop(DragContext context) throws VetoDragException {
		// TODO Auto-generated method stub
		
	}
	

}
