package com.gloopics.g3viewer.client;

import com.google.gwt.dom.client.Element;
import com.google.gwt.dom.client.NodeList;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.event.dom.client.ClickHandler;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Timer;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.DialogBox;
import com.google.gwt.user.client.ui.DockPanel;
import com.google.gwt.user.client.ui.FlowPanel;
import com.google.gwt.user.client.ui.FormPanel;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.FormPanel.SubmitEvent;

public class HttpDialogBox extends DialogBox {

	private FormPanel m_FormPanel = null;

	private HttpDialogHandler m_Callback;

	private final HTML m_Dialog;
	private final G3Viewer m_Parent;

	public HttpDialogBox(G3Viewer a_Parent) {
		m_Parent = a_Parent;
		m_Dialog = new HTML("Empty");
		initComponents();
	}

	public void initComponents() {
		setModal(true);
		addStyleName("dialog");
		setAnimationEnabled(true);
		setText("Dialog");

		Button close = new Button("Cancel", new ClickHandler() {

			public void onClick(ClickEvent event) {
				HttpDialogBox.this.hide();
				Loading.getInstance().endLoading();

			}
		});

		Button ok = new Button("ok", new ClickHandler() {

			public void onClick(ClickEvent event) {
				submitForm();
			}
		});

		FlowPanel fp = new FlowPanel();
		fp.add(ok);
		fp.add(close);
		fp.addStyleName("dButtons");
		DockPanel dp = new DockPanel();
		dp.add(m_Dialog, DockPanel.CENTER);
		dp.add(fp, DockPanel.SOUTH);
		dp.addStyleName("dContents");
		add(dp);

	}

	private class RequestCallbackImpl implements RequestCallback {

		private static final int STATUS_CODE_OK = 200;

		private final String m_URL;

		public RequestCallbackImpl(String a_URL) {
			m_URL = a_URL;
		}

		public void onError(Request request, Throwable exception) {
			showDialog("Could not get " + m_URL + " Exception thrown "
					+ exception.toString());
		}

		public void onResponseReceived(Request request, Response response) {
			if (STATUS_CODE_OK == response.getStatusCode()) {
				showDialog(response.getText());
			} else {
				showDialog(m_URL + response.getText());
			}
		}
	}

	public native static String createData(Element form) /*-{
		var fieldValue = function(el, successful) {
			var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
			if (typeof successful == 'undefined') successful = true;

			if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
					(t == 'checkbox' || t == 'radio') && !el.checked ||
					(t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
					tag == 'select' && el.selectedIndex == -1))
		    	return null;

			if (tag == 'select') {
				var index = el.selectedIndex;
				if (index < 0) return null;
					var a = [], ops = el.options;
				var one = (t == 'select-one');
				var max = (one ? index+1 : ops.length);
				for(var i=(one ? index : 0); i < max; i++) {
		    		var op = ops[i];
		    		if (op.selected) {
						var v = op.value;
						if (!v) // extra pain for IE...
		        			v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
		        		if (one) return v;
		        		a.push(v);
		    		}
				}
				return a;
			}
			return el.value;
		};


		var a = "";
		var added = false;
		var appendA = function(str)
		{
			if(added)
			{
		    	a = a+"&"+str;
			}
			else
		    {	
		    	a = a+str;
		    	added = true;
			}
		}

		var els = form.getElementsByTagName('*'); //: form.elements;
		if (!els) return a;
		for(var i=0, max=els.length; i < max; i++) {
		var el = els[i];
		var n = el.name;
		if (!n) continue;

		var v = fieldValue(el, true);
		if (v && v.constructor == Array) {
		    for(var j=0, jmax=v.length; j < jmax; j++)
		    	appendA(n+"="+escape(v[j]));
		}
		else if (v !== null && typeof v != 'undefined')
			appendA(n+"="+escape(v));
		}

		return a;
	}-*/;

	private void submitForm() {
		if (m_FormPanel != null) {
			String url = m_FormPanel.getAction();

			String data = createData(m_FormPanel.getElement());

			m_Parent.doJSONRequest(url, new HttpSuccessHandler() {

				@Override
				public void success(JSONValue aValue) {
					JSONObject object = aValue.isObject();
					if (object != null) {
						JSONValue result = object.get("result");
						if (result != null) {
							if (result.isString().stringValue().equals(
									"success")) {
								m_Callback.success(aValue.toString());
								Loading.getInstance().endLoading();
							} else {
								JSONValue resul = object.get("form");
								showDialog(resul.isString().stringValue());
							}
						} else {
							G3Viewer.displayError("result was null ", aValue
									.toString());
						}
					} else {
						G3Viewer.displayError("Only JSON Value Returned ",
								aValue.toString());
					}
				}
			}, false, false, data);
		}

		HttpDialogBox.this.hide();
		Loading.getInstance().loading("Please Wait..");

	}

	private void showDialog(String a_Text) {

		m_Dialog.setHTML(a_Text);

		// hide all submits
		NodeList<Element> inputs = this.getElement().getElementsByTagName(
				"input");
		Element input;
		for (int i = 0; i < inputs.getLength(); i++) {
			input = inputs.getItem(i);

			if (input.getAttribute("type").equals("submit")) {
				input.setAttribute("style", "display:none");
			}
		}

		Loading.getInstance().hideAnimation();
		show();

		// find forms if it exists
		NodeList<Element> forms = this.getElement()
				.getElementsByTagName("form");
		if (forms.getLength() > 0) {
			Element element = this.getElement().getElementsByTagName("form")
					.getItem(0);
			setText(element.getElementsByTagName("legend").getItem(0)
					.getInnerText());

			m_FormPanel = FormPanel.wrap(element, true);
			m_FormPanel.addSubmitHandler(new FormPanel.SubmitHandler() {

				@Override
				public void onSubmit(SubmitEvent event) {
					event.cancel();
					submitForm();
				}
			});
		} else {
			setText(this.getElement().getElementsByTagName("legend").getItem(0)
					.getInnerText());
			m_FormPanel = null;
		}

		setPopupPosition(Window.getClientWidth() / 2 - this.getOffsetWidth()
				/ 2, Window.getClientHeight() / 2 - this.getOffsetHeight() / 2);

		Timer t = new Timer() {
			public void run() {

				// find any scripts if they exist
				NodeList<Element> scripts = HttpDialogBox.this.getElement()
						.getElementsByTagName("script");
				for (int i = 0; i < scripts.getLength(); i++) {
					Element script = scripts.getItem(i);
					// script.removeFromParent();
					Element nscript = DOM.createElement("script");
					nscript.setAttribute("type", script.getAttribute("type"));
					nscript.setAttribute("src", script.getAttribute("src"));

					getElementByTagName("head").appendChild(nscript);
				}

			}
		};
		t.schedule(10);
	}

	/**
	 * Gets an element by its tag name; handy for single elements like HTML,
	 * HEAD, BODY.
	 * 
	 * @param tagName
	 *            The name of the tag.
	 * @return The element with that tag name.
	 */
	public native static Element getElementByTagName(String tagName) /*-{
		var elem = $doc.getElementsByTagName(tagName);
		return elem ? elem[0] : null;
	}-*/;

	public void doDialog(String url, HttpDialogHandler a_Callback) {
		m_Callback = a_Callback;
		Loading.getInstance().loading("Please Wait");
		RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

		try {
			builder.sendRequest(null, new RequestCallbackImpl(url));
		} catch (RequestException e) {
			showDialog("Could not call " + url + " Exception thrown "
					+ e.toString());
		}
	}

}
