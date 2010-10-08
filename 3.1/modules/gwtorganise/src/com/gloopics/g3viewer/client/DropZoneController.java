package com.gloopics.g3viewer.client;

import com.allen_sauer.gwt.dnd.client.DragContext;
import com.allen_sauer.gwt.dnd.client.VetoDragException;
import com.allen_sauer.gwt.dnd.client.drop.DropController;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.Widget;

public class DropZoneController implements DropController{

	/**
	 * the album
	 */
	private final Album m_Album;

	/**
	 * the album
	 */
	private final HTML m_DropZone;
	
	/**
	 * compare to
	 */
	private final Item m_CompareTo;
	
	/**
	 * before 
	 */
	private final boolean m_Before;
	
	public DropZoneController(Album a_Album, HTML a_DropZone, Item a_CompareTo, boolean a_Before){
		m_Album = a_Album;
		m_DropZone = a_DropZone;
		m_CompareTo = a_CompareTo;
		m_Before = a_Before;
	}
	
	@Override
	public Widget getDropTarget() {
		// TODO Auto-generated method stub
		return m_DropZone;
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
		m_Album.rearrangeTo(jsa, m_CompareTo, m_Before);
		
	}

	@Override
	public void onEnter(DragContext context) {
		m_DropZone.addStyleName("drop-target");
		
	}

	@Override
	public void onLeave(DragContext context) {
		// TODO Auto-generated method stub
		m_DropZone.removeStyleName("drop-target");
	}

	@Override
	public void onMove(DragContext context) {
		
	}

	@Override
	public void onPreviewDrop(DragContext context) throws VetoDragException {
		
	}
	

}