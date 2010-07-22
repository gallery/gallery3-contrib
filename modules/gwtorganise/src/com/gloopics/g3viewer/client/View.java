package com.gloopics.g3viewer.client;

import java.util.ArrayList;
import java.util.List;

import com.allen_sauer.gwt.dnd.client.DragController;
import com.allen_sauer.gwt.dnd.client.PickupDragController;
import com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile;
import com.gloopics.g3viewer.client.dnddesktop.DesktopDroppableWidget;
import com.gloopics.g3viewer.client.dnddesktop.DndDesktopFactory;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.dom.client.ContextMenuEvent;
import com.google.gwt.event.logical.shared.CloseEvent;
import com.google.gwt.event.logical.shared.CloseHandler;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.Command;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.ui.DialogBox;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.MenuBar;
import com.google.gwt.user.client.ui.MenuItem;
import com.google.gwt.user.client.ui.PopupPanel;
import com.google.gwt.user.client.ui.Widget;

public class View extends FlowPanel implements DesktopDroppableWidget{

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
		if (m_Container.isUploadEnabled())
		{
			((DndDesktopFactory)GWT.create(DndDesktopFactory.class)).getInstance(this);
		}
	}
	
	
	private void clearView(){
		PickupDragController pdc = m_Container.getDragController();
		  if (m_DropZones.size() > 0){
			  for (DropZoneController dropController : m_DropZones){
				  pdc.unregisterDropController(dropController);
			  }
			  m_DropZones.clear();
		  }
		  
		  for (Widget widget : getChildren()){
			  if (widget instanceof Item){
				  ((Item)widget).hidding();
			  }
		  }
		  pdc.clearSelection();
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


	@Override
	public void dropFiles(File[] aFile) {
		if (m_Album != null){
			m_Album.uploadFiles(aFile);
		}
	}


	@Override
	public Widget getActualWidget() {
		return this;
	}
	
	public void showPopupMenu(ContextMenuEvent event){
		final PopupPanel popupPanel = new PopupPanel(true);		
		popupPanel.setAnimationEnabled(true);
		popupPanel.setStyleName("popup");
		MenuBar popupMenuBar = new MenuBar(true);
		
		MenuItem deleteItem = new MenuItem("Delete Selected Items", true, new Command() {
			
			@Override
			public void execute() {
				popupPanel.hide(); 
				
				m_Container.doConfirm("Are you sure you wish to delete selected items?", new ConfirmDialogBox.ConfirmCallBack() {
					
					public void ok() {
						JSONArray jsa = Utils.extractIds(m_Container.getDragController().getDragContext());
						
						m_Container.doJSONRequest(G3Viewer.DELETE_ALL_URL + "?sourceids=" + jsa.toString() , new HttpSuccessHandler() {
							public void success(JSONValue aValue) {
								final List<Widget> widgets = m_Container.getDragController().getSelectedWidgets();
								Item i;
								for (Widget widget: widgets){
									i = (Item)widget;
									removeFromView(i);
									i.removeLinkedAlbum();
								}
								
							}}, true, true);
					}
				});
				
			}
		});
		deleteItem.addStyleName("popup-item");
		popupMenuBar.addItem(deleteItem);
		
		popupMenuBar.setVisible(true);
		popupPanel.add(popupMenuBar);
		
			MenuItem rotateAllCW = new MenuItem("Rotate All Clockwise", true, new Command() {
				@Override
				public void execute() {
					// change all thumbs into loading
					final List<Widget> widgets = m_Container.getDragController().getSelectedWidgets();
					
					for (Widget widget: widgets){
						final Item i = ((Item)widget);
						if (i.isPhoto())
						{
							i.setLoadingThumb();
						}
					
						m_Container.doJSONRequest(G3Viewer.ROTATE_URL + i.getID() + "/cw", 
							new HttpSuccessHandler() {
						
							public void success(JSONValue aValue) {
								i.updateImages(aValue);
							}
						},false,true);
					}
					popupPanel.hide();
				}
			});
		
			rotateAllCW.addStyleName("popup-item");
			popupMenuBar.addItem(rotateAllCW);

			
			MenuItem rotateAllCCW = new MenuItem("Rotate All Counter-Clockwise", true, new Command() {
				@Override
				public void execute() {
					// change all thumbs into loading
					final List<Widget> widgets = m_Container.getDragController().getSelectedWidgets();
					
					for (Widget widget: widgets){
						final Item i = ((Item)widget);
						if (i.isPhoto())
						{
							i.setLoadingThumb();
						}
						m_Container.doJSONRequest(G3Viewer.ROTATE_URL + i.getID() + "/cw", 
							new HttpSuccessHandler() {
						
							public void success(JSONValue aValue) {
								i.updateImages(aValue);
							}
						},false,true);
					}
					popupPanel.hide();
				}
			});
		
			rotateAllCW.addStyleName("popup-item");
			popupMenuBar.addItem(rotateAllCCW);
			
		int x = DOM.eventGetClientX((Event)event.getNativeEvent());
		int y = DOM.eventGetClientY((Event)event.getNativeEvent());
		popupPanel.setPopupPosition(x, y);
		
		popupPanel.show();		

	}
}
