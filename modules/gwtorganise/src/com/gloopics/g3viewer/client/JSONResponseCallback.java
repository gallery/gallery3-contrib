package com.gloopics.g3viewer.client;

import com.google.gwt.json.client.JSONValue;

public interface JSONResponseCallback {
	void onError(Throwable a_Throwable);

	void onResponse(JSONValue a_Value);
}
