package com.gloopics.g3viewer.client;

import java.util.ArrayList;
import java.util.List;

import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.Widget;

public class View extends FlowPanel{

	/**
	 * the current album being viewed
	 */
	private Album m_Album;
	
	/**
	 * the list of drop zones
	 */
	private final List<DropZoneController> m_DropZones 
		= new ArrayList<DropZoneController>();
	
	private final G3Viewer m_Container;
	
	public View(G3Viewer a_Container){
		m_Container = a_Container;
	}
	
	private void clearView(){
		  if (m_DropZones.size() > 0){
			  for (DropZoneController dropController : m_DropZones){
				  m_Container.getDragController().unregisterDropController(dropController);
			  }
			  m_DropZones.clear();
		  }
		  
		  for (Widget widget : getChildren()){
			  if (widget instanceof Item){
				  ((Item)widget).hidding();
			  }
		  }
		  clear();
	  }
	
	public void setAlbum(Album a_Album){
		
		  clearView();
		  m_Album = a_Album;		  
		  Item last = null;
		  for (Item item : a_Album.getItems()) 
		  {
			  if (a_Album.isManualSort()){
				  addDropZone(a_Album, item, true);
			  }
			  
			  addToView(item);
			  
			  item.showing();
			  
			  last = item;
		  }
		  if (a_Album.isManualSort() && (last != null)){
			  addDropZone(a_Album, last, false);
		  }
		  
		  Loading.getInstance().endLoading();

	}
	  
	  private void addDropZone(Album a_Parent,Item a_CompareTo, boolean a_Before){
		  HTML drop = new HTML();
		  drop.addStyleName("DropZone");
		  DropZoneController dzp = new DropZoneController(a_Parent, drop, a_CompareTo, a_Before);
		  m_Container.getDragController().registerDropController(dzp);
		  m_DropZones.add(dzp);
		  addToView(drop);
	  }

	  public void addToView(Widget a_Widget){
		  add(a_Widget);
	  }
	  
	  public void replaceInView(UploadFile a_Remove, Item a_Insert){
		  int index = getWidgetIndex(a_Remove);
		  insert(a_Insert, index);
		  remove(a_Remove);
	  }
	  
	  public void removeFromView(Widget a_Widget){
		  remove(a_Widget);
	  }
	  
	  public Album getCurrentAlbum(){
		  return m_Album;
	  }
	  

}
