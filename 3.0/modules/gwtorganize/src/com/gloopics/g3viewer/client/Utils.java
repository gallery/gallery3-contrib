package com.gloopics.g3viewer.client;

import com.allen_sauer.gwt.dnd.client.DragContext;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.ui.Widget;

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
	
	public static JSONArray extractIds(DragContext context) {
		JSONArray jsa = new JSONArray();
		
		int i = 0;
		for (Widget widget : context.selectedWidgets){
			if (widget instanceof Item){
				jsa.set(i, new JSONNumber(((Item)widget).getID()));
				i++;
			}
		}
		return jsa;
	}

}
