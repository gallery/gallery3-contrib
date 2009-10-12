package com.gloopics.g3viewer.client;

import com.google.gwt.event.logical.shared.SelectionEvent;
import com.google.gwt.event.logical.shared.SelectionHandler;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.ui.Tree;
import com.google.gwt.user.client.ui.TreeItem;

public class AlbumTree extends Tree{
	
	private final G3Viewer m_Container;
	
	
	public AlbumTree(G3Viewer a_Container){
		super();
		
		sinkEvents(Event.ONMOUSEUP | Event.ONMOUSEDOWN | Event.ONCONTEXTMENU);
		
		m_Container = a_Container;
		
	    addSelectionHandler(new SelectionHandler<TreeItem>() {
			
			@Override
			public void onSelection(SelectionEvent<TreeItem> event) {
				((Album) event.getSelectedItem()).select();
				
			}
		});
	    
	   
	}
	
	public void fetchTree()
	{
		// fetch top album
	    Album tree = new Album(m_Container);
	    addItem(tree);
		setSelectedItem(tree);
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
		      
		      popupMenu(event);
		      //GWT.log("Event.ONCONTEXTMENU", null);
		      break;
		 
		    default:
		      super.onBrowserEvent(event);
		  }//end switch
		}
	
	public void refresh()
	{
		((Album)getSelectedItem()).select();
	}
	
	public void ensureSelected(Album album)
	{
		setSelectedItem(album);
		ensureSelectedItemVisible();
	}
	

	/* do popup
	 */
	public void popupMenu(Event event){
		((Album)getSelectedItem()).showPopupMenu(event);
	}

}
