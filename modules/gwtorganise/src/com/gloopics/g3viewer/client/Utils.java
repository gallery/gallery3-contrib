package com.gloopics.g3viewer.client;

import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONValue;

public class Utils {
	public static int extractId(JSONValue a_Value){
		JSONNumber jn = a_Value.isNumber();
		if (jn != null){
			return (int) jn.doubleValue();
		}
		else{
			String val = a_Value.isString().stringValue();
			return Integer.parseInt(val);
		}
	}

}
