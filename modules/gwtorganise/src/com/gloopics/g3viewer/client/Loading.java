package com.gloopics.g3viewer.client;

import com.google.gwt.user.client.ui.Image;
import com.google.gwt.user.client.ui.AbsolutePanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.RootPanel;

public class Loading extends AbsolutePanel{
	
	private static final Loading INSTANCE = new Loading();
	
	public static final String URL = "images/loading.gif";
	
	  /**
	   * the image widget
	   */
	  private Image m_Image = new Image("images/loading.gif");

	  private Loading(){
		  HTML back = new HTML();
		  back.addStyleName("loading");
		  add(back, 0, 0);
		  Image.prefetch(URL);
	  }
	  
	  /**
	   * get instance
	   */
	  public static Loading getInstance(){
		  return INSTANCE;
	  }
	
	  public void loading(){
		  int width = RootPanel.get().getOffsetWidth();
		  int height = RootPanel.get().getOffsetHeight();
		  height = height / 2 - 25;
		  width = width / 2 - 25;
		  add(m_Image);
		  this.setWidgetPosition(m_Image, width, height);
		  RootPanel.get().add(this); 
	  }
	  
	  public void endLoading(){
		  if (m_Image.isAttached()){
			  remove(m_Image);
		  }
		  RootPanel.get().remove(this); 
	  }
	  
	  public void hideAnimation(){
		  remove(m_Image);
	  }
	  	  
}
