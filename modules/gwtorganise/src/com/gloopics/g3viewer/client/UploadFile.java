package com.gloopics.g3viewer.client;

import com.google.gwt.user.client.ui.AbsolutePanel;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.SimplePanel;
import com.google.gwt.gears.client.Factory;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.gears.client.httprequest.HttpRequest;
import com.google.gwt.gears.client.httprequest.ProgressEvent;
import com.google.gwt.gears.client.httprequest.ProgressHandler;
import com.google.gwt.gears.client.httprequest.RequestCallback;
import com.google.gwt.json.client.JSONParser;
import com.google.gwt.json.client.JSONValue;

public class UploadFile extends AbsolutePanel{

	private class ProgressBar extends SimplePanel{
		private final SimplePanel m_ProgressInner;
		public ProgressBar(){
			addStyleName("progressBar");
			m_ProgressInner = new SimplePanel(); 
			m_ProgressInner.addStyleName("progessInner");
			add(m_ProgressInner);
		}
		
		public void setProgress(int a_Percent){
			m_ProgressInner.setWidth( a_Percent + "%");
			
		}
	}
	
	private final File m_LocalFile;
	
	private final String m_Name;
	
	private final Album m_Parent;
	
	//private final Label m_PendingLabel = new Label("Upload Pending");
	private final ProgressBar m_ProgressBar = new ProgressBar();
	
	public UploadFile(Album a_Parent, File a_File){
		m_Parent = a_Parent;
		m_LocalFile = a_File;
		m_Name = a_File.getName();
		Label name = new Label(m_Name);
		name.addStyleName("label");
		add(name,5,20);
		
		add(m_ProgressBar,0,80);
		setStylePrimaryName("item");
		addStyleName("iUpload");
		
	}
	
	public void startUpload(){
		HttpRequest request = Factory.getInstance().createHttpRequest();
		request.open("POST", G3Viewer.UPLOAD_URL + m_Parent.getId() + "?filename=" 
				+ m_Name + "&csrf=" + G3Viewer.getCSRF());
		//request.setRequestHeader("Content-Type", "image/jpg");
		//request.setRequestHeader("Content-Type", "image/jpg");
		
		request.getUpload().setProgressHandler(new ProgressHandler() {
			
			@Override
			public void onProgress(ProgressEvent event) {
				  double pcnt = ((double) event.getLoaded() / event.getTotal());
				  m_ProgressBar.setProgress((int) Math.floor(pcnt * 100));
				
			}
		});
		
		request.setCallback(new RequestCallback() {
			
			@Override
			public void onResponseReceived(HttpRequest request) {
				
				if (request.getStatus() != 200)
				{
					G3Viewer.displayError("Upload Error", request.getResponseText() + request.getStatus() + request.getStatusText());
				}
				try{
				JSONValue jv = JSONParser.parse(request.getResponseText());
				m_Parent.finishedUpload(UploadFile.this, jv);
				} catch (Exception e)
				{
					G3Viewer.displayError("Exception on Upload", e.toString() + " " + request.getResponseText());
				}
			}
		});
		
		request.send(m_LocalFile.getBlob());
	}
	
}
