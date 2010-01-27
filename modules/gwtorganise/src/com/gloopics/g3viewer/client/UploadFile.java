package com.gloopics.g3viewer.client;

import com.gloopics.g3viewer.client.canvas.Canvas;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.Image;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.SimplePanel;
import com.google.gwt.core.client.GWT;
import com.google.gwt.gears.client.Factory;
import com.google.gwt.gears.client.blob.Blob;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.gears.client.httprequest.HttpRequest;
import com.google.gwt.gears.client.httprequest.ProgressEvent;
import com.google.gwt.gears.client.httprequest.ProgressHandler;
import com.google.gwt.gears.client.httprequest.RequestCallback;
import com.google.gwt.gears.client.localserver.ResourceStore;
import com.google.gwt.json.client.JSONParser;
import com.google.gwt.json.client.JSONValue;

public class UploadFile extends Composite{

	private final static ResourceStore RS = Factory.getInstance().createLocalServer().createStore("temp");

	
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
	
	private final ResizeOptions m_ResizeOptions;
	
	private final String m_Name;
	
	private final Album m_Parent;
	
	private final Blob m_Blob;
	
	//private final Canvas m_UpThumb;
	
	private final Label m_Label = new Label("Pending..");
	private final ProgressBar m_ProgressBar = new ProgressBar();
	

	/**
	 * Loads an image into this Canvas, replacing the Canvas' current dimensions
	 * and contents.
	 * 
	 * @param blob The Blob to decode. The image should be in PNG or JPEG format.
	 */
	public final native void captureBlob(ResourceStore rs, Blob blob, String a_Url) /*-{
		rs.captureBlob(blob, a_Url, "image/JPEG");
	  }-*/;

	/**
	 * Loads an image into this Canvas, replacing the Canvas' current dimensions
	 * and contents.
	 * 
	 * @param blob The Blob to decode. The image should be in PNG or JPEG format.
	 */
	public final native void removeCapture(ResourceStore rs, String a_Url) /*-{
		rs.remove(a_Url);
	}-*/;
	
	public UploadFile(Album a_Parent, File a_File, ResizeOptions a_ResizeOptions){
		m_ResizeOptions = a_ResizeOptions;
		m_Parent = a_Parent;
		m_Name = a_File.getName();
		m_Blob = a_File.getBlob();
		captureBlob(RS, m_Blob , m_Name);
			
		Image img = new Image(m_Name);
		FlowPanel dp = new FlowPanel();
		
		dp.add(img);
		
		dp.add(m_ProgressBar);
		dp.add(m_Label);
		
		initWidget(dp);
		setStylePrimaryName("item");
		addStyleName("iUpload");
		
	}
	
	protected void uploadBlob(Blob a_Blob){
		m_Label.setText("Uploading..");
		HttpRequest request = Factory.getInstance().createHttpRequest();
		request.open("POST", G3Viewer.UPLOAD_URL + m_Parent.getId() + "?filename=" 
				+ m_Name + "&csrf=" + G3Viewer.getCSRF());
		
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
				removeCapture(RS, m_Name);
				
				try{
					JSONValue jv = JSONParser.parse(request.getResponseText());
					m_Parent.finishedUpload(UploadFile.this, jv);
				} 
				catch (Exception e){
					G3Viewer.displayError("Exception on Upload", e.toString() + " " + request.getResponseText());
				}
				
				
			}
		});
		
		request.send(a_Blob);
		
	}
	
	public ResizeOptions getResizeOptions(){
		return m_ResizeOptions;
	}
	
	public void startUpload(){
		
		if (m_ResizeOptions.isResize())
		{
			m_Label.setText("Resizing..");
			GWT.runAsync(new AsyncResizer(m_Blob, this));
		}
		else
		{
			uploadBlob(m_Blob);
		}
	}
	
}
