package com.gloopics.g3viewer.client;

import com.google.gwt.json.client.JSONBoolean;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONString;

public class ResizeOptions {
	
	private final boolean m_Resize;
	
	private final int m_MaxWidth;
	
	private final int m_MaxHeight;
	

	public ResizeOptions(JSONObject a_Value){
		JSONBoolean jbool = a_Value.get("resize").isBoolean();
		if (jbool == null)
		{
			throw new RuntimeException("JBool was null.");
		}
		m_Resize = jbool.booleanValue();
		if (m_Resize)
		{
			m_MaxWidth  = Integer.parseInt(a_Value.get("max_width").toString());
			m_MaxHeight = Integer.parseInt(a_Value.get("max_height").toString());
		}
		else
		{
			m_MaxWidth = 0;
			m_MaxHeight = 0;
		}
	}
	
	public boolean isResize()
	{
		return m_Resize;
	}
	
	public int getMaxHeight()
	{
		return m_MaxHeight;
	}
	
	public int getMaxWidth()
	{
		return m_MaxWidth;
	}
}
