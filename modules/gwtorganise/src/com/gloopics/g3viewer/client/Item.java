package com.gloopics.g3viewer.client;

import java.util.Iterator;
import java.util.List;

import com.gloopics.g3viewer.client.dnddesktop.DesktopDropFile;
import com.gloopics.g3viewer.client.dnddesktop.DesktopDroppableWidget;
import com.gloopics.g3viewer.client.dnddesktop.DndDesktopFactory;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.dom.client.ContextMenuEvent;
import com.google.gwt.event.dom.client.ContextMenuHandler;
import com.google.gwt.event.dom.client.DoubleClickEvent;
import com.google.gwt.event.dom.client.DoubleClickHandler;
import com.google.gwt.event.dom.client.HasAllMouseHandlers;
import com.google.gwt.event.dom.client.MouseDownEvent;
import com.google.gwt.event.dom.client.MouseDownHandler;
import com.google.gwt.event.dom.client.MouseMoveEvent;
import com.google.gwt.event.dom.client.MouseMoveHandler;
import com.google.gwt.event.dom.client.MouseOutEvent;
import com.google.gwt.event.dom.client.MouseOutHandler;
import com.google.gwt.event.dom.client.MouseOverEvent;
import com.google.gwt.event.dom.client.MouseOverHandler;
import com.google.gwt.event.dom.client.MouseUpEvent;
import com.google.gwt.event.dom.client.MouseUpHandler;
import com.google.gwt.event.dom.client.MouseWheelEvent;
import com.google.gwt.event.dom.client.MouseWheelHandler;
import com.google.gwt.event.logical.shared.CloseEvent;
import com.google.gwt.event.logical.shared.CloseHandler;
import com.google.gwt.event.shared.HandlerRegistration;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONString;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.Command;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.Image;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.MenuBar;
import com.google.gwt.user.client.ui.MenuItem;
import com.google.gwt.user.client.ui.PopupPanel;
import com.google.gwt.user.client.ui.Widget;


public class Item extends Composite implements HasAllMouseHandlers, DesktopDroppableWidget{

	private final int m_ID;
	
	private String m_Title;
	
	private String m_Thumb;
	
	private String m_Resized;
	
	private final String m_Type;
	
	private final Album m_Parent;
	
	private Album m_LinkedAlbum = null;
	
	private final G3Viewer m_Container;
	
	private final boolean m_IsAlbum;

	private final boolean m_IsPhoto;
	
	private final View m_View;
	
	private AlbumItemDropContainer m_DropContainer = null;
	
	private final Image m_ThumbImage;
	private final Label m_TitleLabel;
	
	public Item(Album a_Parent, JSONObject a_Value, G3Viewer a_Container){
		m_Container = a_Container;
		m_View = a_Container.getView();
		m_Parent = a_Parent;
		m_ID = Utils.extractId(a_Value.get("id"));
		m_Title = ((JSONString)a_Value.get("title")).stringValue();
		m_Thumb = ((JSONString)a_Value.get("thumb")).stringValue();
		m_Type = ((JSONString)a_Value.get("type")).stringValue();
		m_Resized = ((JSONString)a_Value.get("resize")).stringValue();
		m_IsAlbum = m_Type.equals("album");
		m_IsPhoto = m_Type.equals("photo");
		FlowPanel dp = new FlowPanel();
		
		
		m_ThumbImage = new Image(m_Thumb); 
		dp.add(m_ThumbImage);
		
		m_TitleLabel = new Label(m_Title);
		dp.add(m_TitleLabel);
		
		initWidget(dp);
		
		this.setStylePrimaryName("item");
		this.addStyleName("i" + m_Type);
		
		addDomHandler(new ContextMenuHandler() {
			
			@Override
			public void onContextMenu(ContextMenuEvent event) {
				showPopupMenu(event);
				event.stopPropagation();
				event.preventDefault();
			}
		}, ContextMenuEvent.getType());
		
		addDomHandler(new DoubleClickHandler() {
			
			@Override
			public void onDoubleClick(DoubleClickEvent event) {
				if (isAlbum()){
					m_Parent.selectSubAlbum(m_ID);
				}
				else if (isPhoto()){
					m_Container.showImage(m_Resized);
				}
				
			}
		},DoubleClickEvent.getType());
		
		a_Container.getDragController().makeDraggable(this);

		if (m_IsAlbum)
		{
			if (m_Container.isUploadEnabled())
			{
				((DndDesktopFactory)GWT.create(DndDesktopFactory.class)).getInstance(this);
			}
		}
		
	}
	
	public void showing(){
		if (isAlbum() && m_LinkedAlbum != null){
			m_DropContainer = new AlbumItemDropContainer(this, m_LinkedAlbum); 
			m_Container.getDragController().registerDropController(
					m_DropContainer);
		}
	}

	public void hidding(){
		if (m_DropContainer != null){
			m_Container.getDragController().unregisterDropController(m_DropContainer);
		}
	}

	public void updateValues(JSONValue aValue){
		JSONObject jso = aValue.isObject();
		
		if (jso != null) {
			m_Title = ((JSONString)jso.get("title")).stringValue();
			m_Thumb = ((JSONString)jso.get("thumb")).stringValue();
			m_Resized = ((JSONString)jso.get("resize")).stringValue();
			if (m_LinkedAlbum != null){
				m_LinkedAlbum.updateValues(jso);
			}
		
			m_TitleLabel.setText(m_Title);
			m_ThumbImage.setUrl(m_Thumb);
		}
	}
	
	public void updateImages(JSONValue a_Value){
		JSONObject jso = a_Value.isObject();
		
		if (jso != null) {
			m_Thumb = ((JSONString)jso.get("thumb")).stringValue();
			m_ThumbImage.setUrl(m_Thumb);
			m_Resized = ((JSONString)jso.get("resize")).stringValue();
		}
		
	}
	
	public void refresh(){
		
		m_Container.doJSONRequest(G3Viewer.VIEW_ITEM_URL + getID(), 
				new HttpSuccessHandler() {
					
					@Override
					public void success(JSONValue aValue) {
						updateValues(aValue);
					}
				},false,true);
		
	}
	
	public void setLinkedAlbum(Album a_Album){
		m_LinkedAlbum = a_Album;
	}
	
	public void removeLinkedAlbum()
	{
		if (m_LinkedAlbum != null){
			m_LinkedAlbum.remove();
		}
	}
	
	
	public void showPopupMenu(ContextMenuEvent event){
		// show views popup menu if items are selected
		if (m_Container.getDragController().getSelectedWidgetcount() > 1)
		{
			m_View.showPopupMenu(event);
			return;
		}
		
		this.addStyleName("popped");
		final PopupPanel popupPanel = new PopupPanel(true);		
		popupPanel.setAnimationEnabled(true);
		popupPanel.setStyleName("popup");
		MenuBar popupMenuBar = new MenuBar(true);
		
		MenuItem deleteItem = new MenuItem("Delete " + m_Type, true, new Command() {
			
			@Override
			public void execute() {
				m_Container.doDialog("index.php/quick/form_delete/" + m_ID, new HttpDialogHandler() {
					public void success(String aResult) {
						m_View.removeFromView(Item.this);
						if (m_LinkedAlbum != null){
							m_LinkedAlbum.remove();
						}
					}
				});
				popupPanel.hide(); 
				
			}
		});
		deleteItem.addStyleName("popup-item");
		popupMenuBar.addItem(deleteItem);
		
		MenuItem editItem = new MenuItem("Edit " + m_Type, true, new Command() {
			
			@Override
			public void execute() {
				m_Container.doDialog("index.php/form/edit/" + m_Type + "s/" + m_ID,
					new HttpDialogHandler() {
						public void success(String aResult) {
							refresh();
						}
					});
				popupPanel.hide();
				
			}
		});
		editItem.addStyleName("popup-item");
		popupMenuBar.addItem(editItem);
		 
		MenuItem albumCover = new MenuItem("Make Album Cover", true, new Command() {
			
			@Override
			public void execute() {
				m_Container.doJSONRequest(G3Viewer.MAKE_ALBUM_COVER_URL + m_ID, new HttpSuccessHandler() {
					
					public void success(JSONValue aValue) {
						// nothing to do
					}
				},false,true);
				popupPanel.hide();
			}
		});
		albumCover.addStyleName("popup-item");
		popupMenuBar.addItem(albumCover);

		if (isPhoto())
		{
			MenuItem rotateCW = new MenuItem("Rotate Clockwise", true, new Command() {
				@Override
				public void execute() {
					setLoadingThumb();
					m_Container.doJSONRequest(G3Viewer.ROTATE_URL + m_ID + "/cw", new HttpSuccessHandler() {
						
						public void success(JSONValue aValue) {
							updateImages(aValue);
						}
					},false,true);
					popupPanel.hide();
				}
			});
			rotateCW.addStyleName("popup-item");
			popupMenuBar.addItem(rotateCW);
	
			MenuItem rotateCCW = new MenuItem("Rotate Couter-Clockwise", true, new Command() {
				@Override
				public void execute() {
					setLoadingThumb();
					m_Container.doJSONRequest(G3Viewer.ROTATE_URL + m_ID + "/ccw", new HttpSuccessHandler() {
						
						public void success(JSONValue aValue) {
							updateImages(aValue);
						}
					},false,true);
					popupPanel.hide();
				}
			});
			rotateCCW.addStyleName("popup-item");
			popupMenuBar.addItem(rotateCCW);
		}
		
		
		 
		 
		popupMenuBar.setVisible(true);
		popupPanel.add(popupMenuBar);
		
		int x = DOM.eventGetClientX((Event)event.getNativeEvent());
		int y = DOM.eventGetClientY((Event)event.getNativeEvent());
		popupPanel.setPopupPosition(x, y);
		popupPanel.addCloseHandler(new CloseHandler<PopupPanel>() {
			
			@Override
			public void onClose(CloseEvent<PopupPanel> event) {
				
				Item.this.removeStyleName("popped");
			}
		});
		
		popupPanel.show();		
	}
	
	public void setLoadingThumb()
	{
		m_ThumbImage.setUrl(Loading.URL);
	}
	
	public boolean isAlbum(){
		return m_IsAlbum;
	}

	public boolean isPhoto(){
		return m_IsPhoto;
	}
	
	public int getID()
	{
		return m_ID;
	}

	@Override
	public HandlerRegistration addMouseDownHandler(MouseDownHandler handler) {
		return addDomHandler(handler, MouseDownEvent.getType());
	}

	@Override
	public HandlerRegistration addMouseUpHandler(MouseUpHandler handler) {
		return addDomHandler(handler, MouseUpEvent.getType());
	}

	@Override
	public HandlerRegistration addMouseOutHandler(MouseOutHandler handler) {
		return addDomHandler(handler, MouseOutEvent.getType());
	}

	@Override
	public HandlerRegistration addMouseOverHandler(MouseOverHandler handler) {
		return addDomHandler(handler, MouseOverEvent.getType());
	}

	@Override
	public HandlerRegistration addMouseMoveHandler(MouseMoveHandler handler) {
		return addDomHandler(handler, MouseMoveEvent.getType());
	}

	@Override
	public HandlerRegistration addMouseWheelHandler(MouseWheelHandler handler) {
		return addDomHandler(handler, MouseWheelEvent.getType());
	}

	@Override
	public void dropFiles(File[] aFile) {
		if (m_IsAlbum)
		{
			m_LinkedAlbum.uploadFiles(aFile);
		}
		
	}

	@Override
	public Widget getActualWidget() {
		return this;
	}
	
	

}
