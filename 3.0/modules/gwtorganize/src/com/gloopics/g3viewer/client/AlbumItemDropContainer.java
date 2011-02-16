package com.gloopics.g3viewer.client;

import com.allen_sauer.gwt.dnd.client.DragContext;
import com.allen_sauer.gwt.dnd.client.VetoDragException;
import com.allen_sauer.gwt.dnd.client.drop.DropController;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.user.client.ui.Widget;

public class AlbumItemDropContainer implements DropController{
	/**
	 * the tree
	 */
	private final Album m_Album;
	
	private final Item m_Item;
	
	public AlbumItemDropContainer(Item a_Item, Album a_Album){
		m_Album = a_Album;
		m_Item = a_Item;
	}
	
	@Override
	public Widget getDropTarget() {
		// TODO Auto-generated method stub
		return m_Item;
	}

	@Override
	public void onDrop(DragContext context) {
		m_Album.moveTo(Utils.extractIds(context));
	}

	@Override
	public void onEnter(DragContext context) {
		m_Item.addStyleName("drop-target");
		//m_Album.g
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onLeave(DragContext context) {
		// TODO Auto-generated method stub
		m_Item.removeStyleName("drop-target");
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
