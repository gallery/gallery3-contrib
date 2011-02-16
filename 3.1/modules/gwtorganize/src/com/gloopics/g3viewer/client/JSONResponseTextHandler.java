package com.gloopics.g3viewer.client;

import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import com.google.gwt.json.client.JSONException;
import com.google.gwt.json.client.JSONParser;
import com.google.gwt.json.client.JSONValue;

public class JSONResponseTextHandler implements RequestCallback 
{
	private final JSONResponseCallback m_Callback; 
		
	public JSONResponseTextHandler(JSONResponseCallback a_Callback){
		m_Callback = a_Callback;
	}
	
	public void onError(Request request, Throwable exception) {
		m_Callback.onError(exception);
	}

	public void onResponseReceived(Request request, Response response) {
		//response.
		String responseText = response.getText();
	    try {
	    	JSONValue jsonValue = JSONParser.parse(responseText);
	        m_Callback.onResponse(jsonValue);
	    } catch (JSONException e) {
	    	m_Callback.onError(new Throwable(response.getText(), e));
	    }catch (Exception e) {
			m_Callback.onError(new Throwable(response.getText() + e.toString(), e));
		}
	}
}
