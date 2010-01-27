package com.gloopics.g3viewer.client;

import java.util.HashSet;
import java.util.Set;

import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.user.client.ui.Anchor;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.Label;

public class InformationBar extends FlowPanel{
	
	private final G3Viewer m_Container;
	
	private final Label m_Label = new Label();
	
	private final Set<UploadFile> m_Uploads = new HashSet<UploadFile>();

	public InformationBar(G3Viewer a_Container){
		m_Container = a_Container;
		setStylePrimaryName("infobar");
	}
	
	public void initializeForm(){
		Anchor button = new Anchor("Upload Options");
		button.addClickHandler(new ClickHandler() {
			
			@Override
			public void onClick(ClickEvent event) {
				m_Container.doDialog("index.php/admin/upload_configure", new HttpDialogHandler() {
					@Override
					public void success(String aResult) {
					}
				});

				
			}
		});
		
		add(button);
		
		updateInformation();
		add(m_Label);
	}
	
	private void updateInformation()
	{
		int size = m_Uploads.size(); 
		if (size == 0){
			m_Label.setText("");
		}
		else
		{
			if (size == 1){
				m_Label.setText("Uploading file.");
			}
			else{
				m_Label.setText("Uploading " + size + " files.");
			}
			
		}
	}
	
	public void addUpload(UploadFile a_Upload)
	{
		m_Uploads.add(a_Upload);
		updateInformation();
	}
	
	public void removeUpload(UploadFile a_Upload)
	{
		m_Uploads.remove(a_Upload);
		updateInformation();
	}
}
