package com.gloopics.g3viewer.client;

import com.google.gwt.core.client.RunAsyncCallback;

public class AsyncRunner implements RunAsyncCallback{
	
	private final Runnable m_Runnable;
	
	public AsyncRunner(Runnable a_Runnable){
		m_Runnable = a_Runnable;
	}

	@Override
	public void onFailure(Throwable reason) {
		G3Viewer.displayError("Error Running Async", reason.toString());
	}

	
	@Override
	public void onSuccess() {
		m_Runnable.run();
	}
}
