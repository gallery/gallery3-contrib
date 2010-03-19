package com.gloopics.g3viewer.client;

import java.util.HashSet;
import java.util.LinkedList;
import java.util.Set;

import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONValue;

public class UploadControl {
	
	private final LinkedList<UploadFile> m_PrepareUploadQueue = new LinkedList<UploadFile>();
	private final LinkedList<UploadFile> m_UploadQueue = new LinkedList<UploadFile>();
	private final Set<UploadFile> m_Uploads = new HashSet<UploadFile>();
	
	private boolean m_Running = false; 
	private boolean m_PrepareRunning = false;
	
	private G3Viewer m_Container;
	
	public void init(G3Viewer a_Container)
	{
		m_Container = a_Container;
	}

	public UploadFile createUploadFile(Album a_Album, File a_File, ResizeOptions a_ResizeOptions)
	{ 
		
		UploadFile uf = new UploadFile(this, a_Album, a_File, a_ResizeOptions);

		m_Uploads.add(uf);
    	m_PrepareUploadQueue.addLast(uf);
    	prepareNext();
		
		return uf;
	}
	
	private void cleanupUpload(UploadFile uf)
	{
		m_Uploads.remove(uf);
		prepareNext();
		next();
		m_Container.updateInformation();
		
	}
	
	public int size()
	{
		return m_Uploads.size();
	}
	
	public void finishedUploadWithError(UploadFile uf)
	{
		cleanupUpload(uf);
	}
	
	public void finishedUpload(UploadFile uf)
	{
		cleanupUpload(uf);
	}
	
	private void next()
	{
		if (m_UploadQueue.size() > 0)
		{
			UploadFile uf = m_UploadQueue.removeFirst();
			uf.startUpload();
		}
		else
		{
			m_Running = false;
		}
	}

	private void prepareNext()
	{
		if (!m_PrepareRunning)
		{
			if ((m_PrepareUploadQueue.size() > 0) && (m_UploadQueue.size() < 10))
			{
				UploadFile uf = m_PrepareUploadQueue.removeFirst();
				m_PrepareRunning = true;
				uf.prepareUpload();
			}
			else
			{
				m_PrepareRunning = false;
			}
		}
	}
	
	public void finishedPrepare(UploadFile a_UploadFile)
	{
		m_UploadQueue.addLast(a_UploadFile);
		
		if (!m_Running)
		{
			m_Running = true;
			next();
		}
		
		m_PrepareRunning = false;
		prepareNext();
		
	}
	
	public boolean isUploadEnabled(){
		return true;
	}
	
}
