package com.gloopics.g3viewer.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.RootPanel;

public class NoGears implements EntryPoint {
	public void onModuleLoad() {
		RootPanel rootPanel = RootPanel.get("main");
		rootPanel.add(new HTML(
		        "<font color=\"red\">This application requires Google Gears.  To install please visit <a href=\"http://gears.google.com/\">gears.google.com</a> and follow the installation instructions.</font>"));
	}
}
