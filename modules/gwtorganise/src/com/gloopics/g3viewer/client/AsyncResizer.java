package com.gloopics.g3viewer.client;

import com.gloopics.g3viewer.client.canvas.Canvas;

import com.google.gwt.core.client.RunAsyncCallback;
import com.google.gwt.gears.client.blob.Blob;

public class AsyncResizer implements RunAsyncCallback{
	
	private final Blob m_Blob;
	
	private final UploadFile m_UploadFile;
	
	private final ResizeOptions m_ResizeOptions;
	
	public AsyncResizer(Blob a_Blob, UploadFile a_UploadFile){
		m_Blob = a_Blob;
		m_UploadFile = a_UploadFile;
		m_ResizeOptions = a_UploadFile.getResizeOptions();
	}

	@Override
	public void onFailure(Throwable reason) {
		G3Viewer.displayError("Error Resizing image", reason.toString());
	}

	@Override
	public void onSuccess() {
		// resize file
		Canvas upThumb = com.gloopics.g3viewer.client.canvas.Factory.getInstance().createCanvas();
		upThumb.decode(m_Blob);
		
		
		int imageWidth = upThumb.getWidth();
		int imageHeight = upThumb.getHeight();
		
		int widthRatio = imageWidth/m_ResizeOptions.getMaxWidth();
		int heightRatio = imageHeight/m_ResizeOptions.getMaxHeight();
		
		if (widthRatio > heightRatio){
			if (widthRatio > 1) {
				upThumb.resize(m_ResizeOptions.getMaxWidth(), imageHeight / widthRatio );
				m_UploadFile.uploadBlob(upThumb.encode());
			}
		}
		else
		{
			if (heightRatio > 1){
				upThumb.resize(imageWidth / heightRatio, m_ResizeOptions.getMaxHeight());
				m_UploadFile.uploadBlob(upThumb.encode());
			}
		}
		

	}
	

}
