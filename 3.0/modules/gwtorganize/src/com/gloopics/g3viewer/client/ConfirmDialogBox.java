package com.gloopics.g3viewer.client;

import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.DialogBox;
import com.google.gwt.user.client.ui.DockPanel;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.HTML;


public class ConfirmDialogBox extends DialogBox {
	
	public ConfirmCallBack m_Callback;
	private final HTML m_Dialog;

	public interface ConfirmCallBack{
		void ok();
	}
	
	public ConfirmDialogBox(G3Viewer a_Parent){
		m_Dialog = new HTML("Empty");
		initComponents();
	}
	
	public void initComponents(){
		setModal(true);
		addStyleName("dialog");
		setAnimationEnabled(true);
		setText("Confirm");
		
		Button close = new Button("Cancel", new ClickHandler() {
			
			public void onClick(ClickEvent event) {
				ConfirmDialogBox.this.hide();
				
			}
		});

		
		Button ok = new Button("ok", new ClickHandler() {
			
			public void onClick(ClickEvent event) {
				if (m_Callback!=null)
				{
					m_Callback.ok();
				}
				
				ConfirmDialogBox.this.hide();
			}
		});

		FlowPanel fp = new FlowPanel();
		fp.add(ok);
		fp.add(close);
		fp.addStyleName("dButtons");
		DockPanel dp = new DockPanel();
		dp.add(m_Dialog , DockPanel.CENTER);
		dp.add(fp, DockPanel.SOUTH);
		dp.addStyleName("dContents");
		add(dp);
		
	}
	
	public void doDialog(String a_Message, ConfirmCallBack a_Callback){
		m_Callback = a_Callback;
		m_Dialog.setHTML(a_Message);
		show();
	}

}
