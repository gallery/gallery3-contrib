package com.gloopics.g3viewer.client;


import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.ui.Image;
import com.google.gwt.user.client.ui.AbsolutePanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.RootPanel;

public class Loading{
		
	public static final String URL = GWT.getModuleBaseURL() + "loading.gif";

	private static final Loading INSTANCE = new Loading();
	
		  /**
		   * the image widget
		   */
		  private final Image m_Image = new Image(URL);
		  
		  private final Label m_Label = new Label();
		  
		  private final HTML m_Back = new HTML();

		  private Loading(){
			  m_Back.addStyleName("loading");
			  m_Label.setStyleName("loading-label");
			  m_Image.setStyleName("loading-image");
		  }
		  
		  /**
		   * get instance
		   */
		  public static Loading getInstance(){
			  return INSTANCE;
		  }
		
		  public void loading(String message){
			  RootPanel.get().add(m_Back);
			  
			  int width = RootPanel.get().getOffsetWidth();
			  int height = RootPanel.get().getOffsetHeight();
			  height = height / 2 - 20;
			  width = width / 2 - 40;
			  RootPanel.get().add(m_Image, width, height);
			  
			  if (message != null)
			  {
				  m_Label.setText(message);
				  RootPanel.get().add(m_Label, 0, height + 45);
			  }
			  
			  //RootPanel.get().add(this); 
		  }
		  
		  public void endLoading(){
			  if (m_Image.isAttached()){
				  RootPanel.get().remove(m_Image);
			  }
			  if (m_Label.isAttached()){
				  RootPanel.get().remove(m_Label);
			  }
			  if (m_Back.isAttached()){
				  RootPanel.get().remove(m_Back);
			  }
		  }
		  
		  public void hideAnimation(){
			  if (m_Image.isAttached()){
				  RootPanel.get().remove(m_Image);
			  }
			  if (m_Label.isAttached()){
				  RootPanel.get().remove(m_Label);
			  }
		  }
		  	  
}
