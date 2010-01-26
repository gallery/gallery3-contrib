package com.gloopics.g3viewer.client;

import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.event.dom.client.ErrorEvent;
import com.google.gwt.event.dom.client.ErrorHandler;
import com.google.gwt.event.dom.client.LoadEvent;
import com.google.gwt.event.dom.client.LoadHandler;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Image;
import com.google.gwt.user.client.ui.PopupPanel;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.SimplePanel;

public class ImageDialogBox extends PopupPanel{

	private Image m_Image = null;

	public ImageDialogBox(){
		initComponents();
	}
	
	
	private void initComponents()
	{
		setModal(true);
		addStyleName("dialog");
		setAnimationEnabled(true);
		
		addDomHandler(new ClickHandler() {
			
			@Override
			public void onClick(ClickEvent event) {
				ImageDialogBox.this.hide();
				
				Loading.getInstance().endLoading();
				}
				
			} ,ClickEvent.getType());
		
	}
	
	public void doDialog(String a_Image){
		
		Loading.getInstance().loading("Loading Image..");

		if (m_Image != null){
			m_Image.removeFromParent();
		}
		
		m_Image = new Image();
		final SimplePanel sp = new SimplePanel();
		m_Image.addLoadHandler(new LoadHandler() {
			
			@Override
			public void onLoad(LoadEvent event) {
				sp.removeFromParent();
				
				Loading.getInstance().hideAnimation();
				
				add(m_Image);
				show();
				
				setPopupPosition(Window.getClientWidth() / 2 - getOffsetWidth() / 2,
						Window.getClientHeight() / 2 - getOffsetHeight() / 2);
						
				

				
				
			}
		});
		m_Image.addErrorHandler(new ErrorHandler() {
			
			@Override
			public void onError(ErrorEvent event) {
				sp.removeFromParent();
				G3Viewer.displayError("Error Loading Image", "It could be that the resized version of the image has not been built correctly.");
				Loading.getInstance().endLoading();
			}
		});
		
		sp.setSize("0px", "0px");
		sp.setStylePrimaryName("hideme");
		sp.setWidget(m_Image);
		RootPanel.get().add(sp);
		
		m_Image.setUrl(a_Image);
		
	}
	
	

}
