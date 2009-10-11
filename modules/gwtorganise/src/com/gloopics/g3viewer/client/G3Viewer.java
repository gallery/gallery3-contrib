/*
 * Copyright 2007 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
package com.gloopics.g3viewer.client;


import com.allen_sauer.gwt.dnd.client.PickupDragController;
import com.google.gwt.dom.client.Document;
import com.google.gwt.dom.client.InputElement;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONString;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.DialogBox;
import com.google.gwt.user.client.ui.DockPanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.HorizontalSplitPanel;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.SimplePanel;

/**
 * Class that acts as a client to a JSON service. Currently, this client just
 * requests a text which contains a JSON encoding of a search result set from
 * yahoo. We use a text file to demonstrate how the pieces work without tripping
 * on cross-site scripting issues.
 * 
 * If you would like to make this a more dynamic example, you can associate a
 * servlet with this example and simply have it hit the yahoo service and return
 * the results.
 */
public class G3Viewer {
	
	private static String m_CSRF = null;

	private static class ErrorDialog extends DialogBox 
	  {

		  public ErrorDialog(String error) {
		      // Set the dialog box's caption.
		      setText("Error");
		      
		      DockPanel dp = new DockPanel();
		      dp.addStyleName("error");
		      dp.add(new HTML(error), DockPanel.CENTER);
		      

		      // DialogBox is a SimplePanel, so you have to set its widget property to
		      // whatever you want its contents to be.
		      Button ok = new Button("OK");
		      ok.addClickHandler(new ClickHandler() {
		        public void onClick(ClickEvent event) {
		        	ErrorDialog.this.hide();
		        }
		      });
		      
		      dp.add(ok, DockPanel.NORTH);
		      
		      setWidget(dp);
		    }
		  }	  
	
  /*
   * BASE url
   */
  private static String BASE_URL = 
		((InputElement)Document.get().getElementById 
				("baseurl")).getValue();

  /*
   * url to view album
   */
  public static final String VIEW_ALBUM_URL = BASE_URL + "index.php/json_album/albums/";

  /*
   * url to view album
   */
  public static final String MOVE_TO_ALBUM_URL = BASE_URL + "index.php/json_album/move_to/";
  
  /*
   * url to view album
   */
  public static final String VIEW_CHILDREN_URL = BASE_URL + "index.php/json_album/children/";

  /*
   * Load Item
   */
  public static final String VIEW_ITEM_URL = BASE_URL + "index.php/json_album/item/";
  
  /**
   * upload url
   */
  public static final String UPLOAD_URL = BASE_URL + "index.php/json_album/add_photo/";

  /**
   * upload url
   */
  public static final String REARRANGE_URL = BASE_URL + "index.php/json_album/rearrange/";

  /**
   * upload url
   */
  public static final String IS_ADMIN_URL = BASE_URL + "index.php/json_album/is_admin/";
  
  /**
   * upload url
   */
  public static final String MAKE_ALBUM_COVER_URL = BASE_URL + "index.php/json_album/make_album_cover/";
  
  /**
   * rotate url
   */
  public static final String ROTATE_URL = BASE_URL + "index.php/json_album/rotate/";
  
  /*
   * tree
   */
  private final AlbumTree m_Tree;
  
  /**
   * the only image dialog box
   */
  private final ImageDialogBox m_ImageDialogBox = new ImageDialogBox();

  /**
   * the only dialog box
   */
  private final HttpDialogBox m_HttpDialogBox= new HttpDialogBox();
  
  private class SimplePanelEx extends SimplePanel 
  {
	  public SimplePanelEx()
	  {
		  super();
		  sinkEvents(Event.ONMOUSEUP | Event.ONMOUSEDOWN | Event.ONCONTEXTMENU);

	  }
	  
		public void onBrowserEvent(Event event) {
			  
			  
			  switch (DOM.eventGetType(event)) {
			 
			  	case Event.ONMOUSEUP:
			      if (DOM.eventGetButton(event) == Event.BUTTON_RIGHT) {
			    	  event.preventDefault();
			    	  break;
			    	  
			      }
			      else
			      {
			    	  super.onBrowserEvent(event);
			      }
			      break;

			  	case Event.ONMOUSEDOWN:
				      if (DOM.eventGetButton(event) == Event.BUTTON_RIGHT) {
				    	  event.preventDefault();
				    	  break;
				    	  
				      }
				      else
				      {
				    	  super.onBrowserEvent(event);
				      }
				      break;
			      
			    case Event.ONCONTEXTMENU:
			      event.preventDefault();    
			      m_Tree.popupMenu(event);
			      //GWT.log("Event.ONCONTEXTMENU", null);
			      break;
			 
			    default:
			      super.onBrowserEvent(event);
			  }//end switch
			}
	  
  }
  
  /**
   * central split panel
   */
  private final HorizontalSplitPanel m_SplitPanel = new HorizontalSplitPanel();
  
  
  /**
   * Grid View
   */
  private final View m_View = new View(this);
  
  /**
   * the drag controller
   */
  private final PickupDragController m_DragController;
  
  /**
   * constructor
   */
  
  public G3Viewer(){
	  m_DragController = new PickupDragController(RootPanel.get(),false);
	  m_DragController.setBehaviorMultipleSelection(true);
	  m_DragController.setBehaviorDragStartSensitivity(5);
	  m_DragController.setBehaviorDragProxy(true);
	  
	  m_Tree  = new AlbumTree(this);

	  checkAdmin();
  }

  public static String getCSRF()
  {
	  return m_CSRF;
  }
  
  private void checkAdmin(){
	  
	  doJSONRequest(IS_ADMIN_URL, new HttpSuccessHandler() {
			
			@Override
			public void success(JSONValue aValue) {
				JSONObject jso = aValue.isObject();
				if (jso != null){
					JSONString jss = jso.get("result").isString();
					if (jss != null){
						if (jss.stringValue().equals("success"))
						{
							
							m_CSRF = (jso.get("csrf").isString()).stringValue();
							m_Tree.fetchTree();
							return;
						}
					}
				}
				
				doDialog("index.php/login/ajax", new HttpDialogHandler() {
					
					@Override
					public void success(String aResult) {
						// recheck admin
						checkAdmin();
						
					}
				});
			}
		},false);
  }
  
  
  
  /**
   * Entry point for this simple application. Currently, we build the
   * application's form and wait for events.
   */
  public void onModuleLoad() {
    initializeMainForm();
  }
  
  public static void displayError(String errorType, String errorMessage) {
	  new ErrorDialog(errorType + "\n" + errorMessage).show();
  }
  
  /**
   * returns the drag controller
   */
  public PickupDragController getDragController(){
	  return m_DragController;
  }

  public AlbumTree getTree(){
	  return m_Tree;
  }
  
  public View getView(){
	  return m_View;
  }
  
  
  
  public void doDialog(String a_Url, HttpDialogHandler a_Handler)
  {
	  m_HttpDialogBox.doDialog(BASE_URL  + a_Url, a_Handler); 
  }
  
  public void showImage(String a_Url)
  {
	  m_ImageDialogBox.doDialog( a_Url); 
  }
  
  public void doJSONRequest(final String a_URL, final HttpSuccessHandler a_Handler, final boolean a_hasParams){
	  try {
		  String url;
		  if (m_CSRF != null)
		  {
			  url = a_URL + (a_hasParams?"&csrf=":"?csrf=") + m_CSRF;
		  }
		  else
		  {
			  url = a_URL;
		  }
		 RequestBuilder requestBuilder = new RequestBuilder(
				 RequestBuilder.GET, url);
		 requestBuilder.setCallback(new JSONResponseTextHandler(
				new JSONResponseCallback() {
						
				@Override
				public void onResponse(JSONValue aValue) {
					a_Handler.success(aValue);
				}
						
				@Override
				public void onError(Throwable aThrowable) {
					displayError("Unexpected Error", 
								aThrowable.toString() + " - " + a_URL);
		    	}}
			));
		      
		 requestBuilder.send();
	  } catch (RequestException ex) {
		displayError("Request Exception", ex.toString() + " - " + a_URL);
	  }
  }

  /**
   * Initialize the main form's layout and content.
   */
  private void initializeMainForm() {

	 m_View.addStyleName("view");
	 
	 m_Tree.setVisible(true);

	 m_SplitPanel.setSplitPosition("20%");
	 
	 m_SplitPanel.setLeftWidget(m_Tree);
    
	 m_SplitPanel.setRightWidget(m_View);

	 SimplePanel sp = new SimplePanelEx();
     sp.add(m_SplitPanel);
    
    RootPanel.get("main").add(sp);
  }
  
}
