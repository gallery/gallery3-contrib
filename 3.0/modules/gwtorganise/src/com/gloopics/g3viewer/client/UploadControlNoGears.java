package com.gloopics.g3viewer.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.RootPanel;

public class UploadControlNoGears extends UploadControl{
	public UploadFile createUploadFile(Album a_Album, File a_File, ResizeOptions a_ResizeOptions)
	{
		return null;
	}
	
	public void init(G3Viewer a_Container)
	{
		
	}
	
	private void cleanupUpload(UploadFile uf)
	{
		
	}
	
	public int size()
	{
		return 0;
	}
	
	public void finishedUploadWithError(UploadFile uf)
	{
	}
	
	public void finishedUpload(UploadFile uf)
	{
	}
	
	private void next()
	{
	}

	private void prepareNext()
	{
	}
	
	public void finishedPrepare(UploadFile a_UploadFile)
	{
		
	}
	
	public boolean isUploadEnabled(){
		return false;
	}
}
