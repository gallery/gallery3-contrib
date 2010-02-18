package com.gloopics.g3viewer.client;

import com.google.gwt.dom.client.Element;
import com.google.gwt.dom.client.NodeList;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.RequestTimeoutException;
import com.google.gwt.http.client.Response;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.DialogBox;
import com.google.gwt.user.client.ui.DockPanel;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.FormPanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.FormPanel.SubmitCompleteEvent;
import com.google.gwt.user.client.ui.FormPanel.SubmitCompleteHandler;

public class HttpDialogBox extends DialogBox{

	private FormPanel m_FormPanel = null;
	
	private HttpDialogHandler m_Callback;
	
	private final HTML m_Dialog;
	
	public HttpDialogBox(){
		m_Dialog = new HTML("Empty");
		initComponents();
	}
	
	public void initComponents(){
		setModal(true);
		addStyleName("dialog");
		setAnimationEnabled(true);
		setText("Dialog");
		
		Button close = new Button("Cancel", new ClickHandler() {
			
			public void onClick(ClickEvent event) {
				HttpDialogBox.this.hide();
				Loading.getInstance().endLoading();
				
			}
		});

		
		Button ok = new Button("ok", new ClickHandler() {
			
			public void onClick(ClickEvent event) {
				if (m_FormPanel!=null)
				{
					m_FormPanel.submit();
				}
				else
				{
					
				}
				HttpDialogBox.this.hide();
				Loading.getInstance().loading("Please Wait..");
			}
		});

		FlowPanel fp = new FlowPanel();
		fp.add(ok);
		fp.add(close);
		fp.addStyleName("dButtons");
		DockPanel dp = new DockPanel();
		dp.add(m_Dialog , DockPanel.CENTER);
		dp.add(fp, DockPanel.SOUTH);
		dp.addStyleName("dContents");
		add(dp);
		
	}
	
	private class RequestCallbackImpl implements RequestCallback {

		  private static final int STATUS_CODE_OK = 200;
		  
		  private final String m_URL;
		  
		  public RequestCallbackImpl(String a_URL){
			  m_URL = a_URL;
		  }

		  public void onError(Request request, Throwable exception) {
		    if (exception instanceof RequestTimeoutException) {
		      // handle a request timeout
		    } else {
		      // handle other request errors
		    }
	    	showDialog("Could not get " + m_URL + " Exception thrown " + exception.toString());
		  }

		  public void onResponseReceived(Request request, Response response) {
		    if (STATUS_CODE_OK == response.getStatusCode()) {
		      showDialog(response.getText());
		    } else {
		      showDialog(m_URL + response.getText());
		      // handle non-OK response from the server
		    }
		  }
		}

	private void showDialog(String a_Text){
		
		m_Dialog.setHTML(a_Text);
		
		// hide all submits
		NodeList<Element> inputs = this.getElement().getElementsByTagName("input");
		Element input;
		for (int i = 0; i < inputs.getLength(); i++){
			input = inputs.getItem(i);
			
			if (input.getAttribute("type").equals("submit"))
			{
				input.setAttribute("style", "display:none");
			}
		}

		Loading.getInstance().hideAnimation();
		show();
		
		// find forms if it exists
		NodeList<Element> forms = this.getElement().getElementsByTagName("form");
		if (forms.getLength() > 0)
		{
			Element element = this.getElement().getElementsByTagName("form").getItem(0);
			setText(element.getElementsByTagName("legend").getItem(0).getInnerText());
		
		
			m_FormPanel = FormPanel.wrap(element, true);
			m_FormPanel.addSubmitCompleteHandler(new SubmitCompleteHandler() {
			
			@Override
			public void onSubmitComplete(SubmitCompleteEvent event) {
				m_Callback.success(event.getResults());
				Loading.getInstance().endLoading();
			}
			});
		
		}
		else
		{
			setText(this.getElement().getElementsByTagName("legend").getItem(0).getInnerText());
		}
		
		setPopupPosition(Window.getClientWidth() / 2 - this.getOffsetWidth() / 2,
				Window.getClientHeight() / 2 - this.getOffsetHeight() / 2);
	}
	
	public void doDialog(String url, HttpDialogHandler a_Callback){
		m_Callback = a_Callback;
		Loading.getInstance().loading("Please Wait");
		RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

		try {
			builder.sendRequest(null, new RequestCallbackImpl(url));
		} catch (RequestException e) {
	    	showDialog("Could not call " + url + " Exception thrown " + e.toString());
		}
	}

	
}
