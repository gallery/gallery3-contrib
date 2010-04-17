package com.gloopics.g3viewer.client;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.Set;

import com.google.gwt.event.logical.shared.CloseEvent;
import com.google.gwt.event.logical.shared.CloseHandler;
import com.google.gwt.gears.client.Factory;
import com.google.gwt.gears.client.desktop.Desktop;
import com.google.gwt.gears.client.desktop.File;
import com.google.gwt.gears.client.desktop.OpenFilesHandler;
import com.google.gwt.json.client.JSONArray;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONString;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.Command;
import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.MenuBar;
import com.google.gwt.user.client.ui.MenuItem;
import com.google.gwt.user.client.ui.PopupPanel;
import com.google.gwt.user.client.ui.TreeItem;
import com.google.gwt.user.client.ui.Widget;

/**
 * encapsulates an album
 * 
 * @author User
 * 
 */
public class Album extends TreeItem {

	private final int m_ID;

	private String m_Title;

	private final G3Viewer m_Container;

	private final View m_View;

	private final Label m_Label;

	private String m_Sort;

	private final List<Item> m_Items = new ArrayList<Item>();

	private final Map<Integer, Item> m_IDtoItem = new HashMap<Integer, Item>();

	private final Map<Integer, Album> m_IDtoAlbum = new HashMap<Integer, Album>();

	private final Set<UploadFile> m_AllUploads = new HashSet<UploadFile>();

	private final AlbumTreeDropController m_DropController;

	private final UploadControl m_UploadControl;

	public Album(JSONObject jsonObject, G3Viewer a_Container) {
		m_UploadControl = a_Container.getUploadControl();
		m_ID = Utils.extractId(jsonObject.get("id"));
		m_Title = ((JSONString) jsonObject.get("title")).stringValue();
		m_Sort = ((JSONString) jsonObject.get("sort")).stringValue();

		m_Container = a_Container;
		m_View = a_Container.getView();
		m_DropController = new AlbumTreeDropController(this);
		m_Label = initComponents();

	}

	public Album(G3Viewer a_Container) {
		m_UploadControl = a_Container.getUploadControl();
		m_ID = 1;
		m_Title = "Root";
		m_Container = a_Container;
		m_View = a_Container.getView();
		m_Sort = "Unknown";
		m_DropController = new AlbumTreeDropController(this);
		m_Label = initComponents();
		refresh();
	}

	public void updateValues(JSONValue a_Jso) {
		JSONObject jso = a_Jso.isObject();
		if (jso != null) {
			m_Title = ((JSONString) jso.get("title")).stringValue();
			String oldSort = m_Sort;
			m_Sort = ((JSONString) jso.get("sort")).stringValue();
			if (!oldSort.equals(m_Sort)) {
				if (m_View.getCurrentAlbum() == this) {
					select();
				}
			}
			m_Label.setText(m_Title);
		}
	}

	public void refresh() {
		m_Container.doJSONRequest(G3Viewer.VIEW_ITEM_URL + getId(),
				new HttpSuccessHandler() {

					@Override
					public void success(JSONValue aValue) {
						updateValues(aValue);
					}
				}, false, true);
	}

	public void showPopupMenu(Event event) {
		m_Label.addStyleName("popped");
		final PopupPanel popupPanel = new PopupPanel(true);
		popupPanel.setAnimationEnabled(true);
		MenuBar popupMenuBar = new MenuBar(true);

		MenuItem editItem = new MenuItem("Edit Album", true, new Command() {

			@Override
			public void execute() {
				m_Container.doDialog("index.php/form/edit/albums/" + m_ID,
						new HttpDialogHandler() {
							@Override
							public void success(String aResult) {
								refresh();

							}
						});
				popupPanel.hide();

			}
		});

		MenuItem addAlbum = new MenuItem("Add Album", true, new Command() {

			@Override
			public void execute() {
				m_Container.doDialog("index.php/form/add/albums/" + m_ID
						+ "?type=album", new HttpDialogHandler() {
					@Override
					public void success(String aResult) {
						expand();
						m_View.getCurrentAlbum().select();
					}
				});

				popupPanel.hide();

			}
		});

		MenuItem userPermissions = new MenuItem("User Permissions", true,
				new Command() {
					@Override
					public void execute() {
						m_Container.doDialog("index.php/permissions/browse/"
								+ m_ID, new HttpDialogHandler() {
							@Override
							public void success(String aResult) {
							}
						});

						popupPanel.hide();

					}
				});

		popupPanel.setStyleName("popup");
		editItem.addStyleName("popup-item");
		addAlbum.addStyleName("popup-item");
		userPermissions.addStyleName("popup-item");

		if (m_Container.isUploadEnabled()) {
			MenuItem uploadPhotos = new MenuItem("Upload Photos", true,
					new Command() {

						@Override
						public void execute() {
							uploadFiles();
							popupPanel.hide();

						}
					});
			uploadPhotos.addStyleName("popup-item");
			popupMenuBar.addItem(uploadPhotos);
		}

		popupMenuBar.addItem(editItem);
		popupMenuBar.addItem(addAlbum);
		popupMenuBar.addItem(userPermissions);
		
		popupMenuBar.setVisible(true);
		popupPanel.add(popupMenuBar);

		int x = DOM.eventGetClientX(event);
		int y = DOM.eventGetClientY(event);
		popupPanel.setPopupPosition(x, y);
		popupPanel.addCloseHandler(new CloseHandler<PopupPanel>() {

			@Override
			public void onClose(CloseEvent<PopupPanel> event) {

				m_Label.removeStyleName("popped");
			}
		});

		popupPanel.show();
	}

	private Label initComponents() {
		Label toReturn = new Label(m_Title);
		toReturn.addStyleName("Tree-Album");
		setWidget(toReturn);
		m_Container.getDragController()
				.registerDropController(m_DropController);
		expand();

		return toReturn;
	}

	public int getId() {
		return m_ID;
	}

	/*
	 * Adds the albums in the json response TreeItem.
	 */
	private void addAlbums(JSONValue jsonValue) {
		JSONArray jsonArray = (JSONArray) jsonValue;
		Set<Integer> allAlbums = new HashSet<Integer>(m_IDtoAlbum.keySet());
		for (int i = 0; i < jsonArray.size(); ++i) {
			JSONObject jso = (JSONObject) jsonArray.get(i);

			int id = Utils.extractId(jso.get("id"));

			if (m_IDtoAlbum.containsKey(id)) {
				m_IDtoAlbum.get(id).updateValues(jso);
			} else {
				Album album = new Album(jso, m_Container);
				m_IDtoAlbum.put(id, album);
				addItem(album);
			}
			allAlbums.remove(id);
		}
		for (Integer id : allAlbums) {
			Album a = m_IDtoAlbum.remove(id);
			a.cleanup();

			removeItem(a);
		}
	}

	public void cleanup() {
		m_Container.getDragController().unregisterDropController(
				m_DropController);
		for (int i = 0; i < getChildCount(); i++) {
			((Album) getChild(i)).cleanup();
		}
	}

	/**
	 * moves the given array of ids to this album
	 */
	public void moveTo(JSONArray a_Ids) {
		Loading.getInstance().loading("Moving Items..");

		m_Container.doJSONRequest(G3Viewer.MOVE_TO_ALBUM_URL + getId()
				+ "?sourceids=" + a_Ids.toString(), new HttpSuccessHandler() {

			@Override
			public void success(JSONValue aValue) {
				expand();
				m_View.getCurrentAlbum().expand();
				m_View.getCurrentAlbum().select();
			}
		}, true, true);
	}

	/**
	 * rearranges the albums
	 */

	public void rearrangeTo(JSONArray a_Ids, Item m_CompareTo, boolean m_Before) {
		Loading.getInstance().loading("Re-arranging..");
		String bora = m_Before ? "before" : "after";

		m_Container.doJSONRequest(G3Viewer.REARRANGE_URL + m_CompareTo.getID()
				+ "/" + bora + "?sourceids=" + a_Ids.toString(),
				new HttpSuccessHandler() {

					@Override
					public void success(JSONValue aValue) {
						m_View.getCurrentAlbum().select();
					}
				}, true, true);
	}

	/**
	 * returns the album with the given id
	 */
	public void selectSubAlbum(int a_Id) {
		for (int i = 0; i < getChildCount(); i++) {
			Album ab = ((Album) getChild(i));
			if (ab.m_ID == a_Id) {
				ab.select();
				m_Container.getTree().ensureSelected(ab);
			}
		}
	}

	/*
	 * Fetch the requested URL.
	 */
	public void expand() {

		m_Container.doJSONRequest(G3Viewer.VIEW_ALBUM_URL + getId(),
				new HttpSuccessHandler() {

					@Override
					public void success(JSONValue aValue) {
						addAlbums(aValue);
					}
				}, false, true);
	}

	public void select() {
		Loading.getInstance().loading("Loading Contents..");
		m_Container.doJSONRequest(G3Viewer.VIEW_CHILDREN_URL + getId(),
				new HttpSuccessHandler() {

					@Override
					public void success(JSONValue aValue) {
						viewAlbum(aValue);
					}
				}, false, true);

	}

	/*
	 * view Album contents
	 */
	private void viewAlbum(JSONValue a_Value) {

		JSONArray jsonArray = (JSONArray) a_Value;

		Item item = null;
		int id;
		JSONObject jso;

		m_Items.clear();

		for (int i = 0; i < jsonArray.size(); ++i) {
			jso = (JSONObject) jsonArray.get(i);
			id = Utils.extractId(jso.get("id"));

			if (m_IDtoItem.containsKey(id)) {
				item = m_IDtoItem.get(id);
				item.updateValues(jso);
			} else {
				item = new Item(this, jso, m_Container);
				m_IDtoItem.put(id, item);

				if (item.isAlbum()) {
					linkAlbum(item);
				}
			}
			m_Items.add(item);

		}

		m_View.setAlbum(this);
		addPendingDownloads();
	}

	public List<Item> getItems() {
		return m_Items;
	}

	public void linkAlbum(Item a_Item) {
		// link album
		int id = a_Item.getID();
		Album child;
		for (int j = 0; j < getChildCount(); j++) {
			child = (Album) getChild(j);
			if (child.m_ID == id) {
				a_Item.setLinkedAlbum(child);
				j = getChildCount();
			}

		}
	}

	public boolean isManualSort() {
		return m_Sort.equalsIgnoreCase("weight");
	}

	public void uploadFiles() {
		Desktop desktop = Factory.getInstance().createDesktop();

		desktop.openFiles(new OpenFilesHandler() {

			public void onOpenFiles(OpenFilesEvent event) {
				uploadFiles(event.getFiles());
			}
		}, false);

	}

	public void uploadFiles(final File[] files) {

		m_Container.doJSONRequest(G3Viewer.RESIZE_DETAILS_URL,
				new HttpSuccessHandler() {

					public void success(JSONValue a_Value) {
						JSONObject jso = a_Value.isObject();
						if (jso != null) {

							ResizeOptions ro = new ResizeOptions(jso);
							UploadFile uf;
							for (File file : files) {
								uf = m_UploadControl.createUploadFile(
										Album.this, file, ro);
								m_AllUploads.add(uf);
								m_View.addToView(uf);
							}
							m_Container.updateInformation();
						}
					}
				}, false, true);

	}

	public void removeUpload(UploadFile a_Uf) {
		m_AllUploads.remove(a_Uf);
	}

	public void replaceUpload(UploadFile a_Uf, JSONValue a_Return) {
		m_AllUploads.remove(a_Uf);

		JSONObject jo = a_Return.isObject();

		if (jo != null) {
			Item item = new Item(this, jo, m_Container);
			m_IDtoItem.put(item.getID(), item);
			m_Items.add(item);

			if (m_View.getCurrentAlbum() == this) {
				m_View.replaceInView(a_Uf, item);
			}
		} else {
			if (m_View.getCurrentAlbum() == this) {
				m_View.removeFromView(a_Uf);
			}
		}
	}

	public void addPendingDownloads() {
		for (UploadFile uf : m_AllUploads) {
			m_View.addToView(uf);
		}
	}

}
