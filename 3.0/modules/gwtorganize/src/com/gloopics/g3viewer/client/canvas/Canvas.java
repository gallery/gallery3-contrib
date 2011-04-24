/*
 * Copyright 2009 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
package com.gloopics.g3viewer.client.canvas;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.gears.client.blob.Blob;

/**
 * The Canvas module is a graphics API that is inspired by the <a href="http://www.whatwg.org/specs/web-apps/current-work/multipage/the-canvas-element.html"
 * >HTML5 canvas</a>, with additional methods to decode from and encode to
 * binary formats (such as PNG and JPEG), represented by Blobs. A Gears Canvas
 * is not yet a complete implementation of the HTML5 canvas specification, and
 * there are two significant differences:
 * <ul>
 * <li>A Gears Canvas is off-screen, in that creating a Canvas object doesn't
 * directly paint any pixels on the screen. Furthermore, for technical reasons,
 * a Gears Canvas is not a DOM Element. On the other hand, you can create a
 * Gears Canvas in a Worker.
 * <li>A Gears Canvas does not implement <code>getContext</code>, and in
 * particular does not provide a 2D context.
 * </ul>
 * <p>
 * <b>Permission</b>
 * <p>
 * This API requires user permission. If you would like to customize the default
 * dialog, you can explicitly call
 * {@link com.google.gwt.gears.client.Factory#getPermission()}.
 */
public class Canvas extends JavaScriptObject {

  public static final String MIMETYPE_JPEG = "image/jpeg";
  public static final String MIMETYPE_PNG = "image/png";
  
  protected Canvas() {
    // required for overlay types
  }

  /**
   * Crops the Canvas. The crop happens "in-place", as opposed to returning a
   * new Canvas.
   * 
   * @param x The left co-ordinate of the crop rectangle.
   * @param y The top co-ordinate of the crop rectangle.
   * @param w The width of the crop rectangle.
   * @param h The height of the crop rectangle.
   */
  public final native void crop(int x, int y, int w, int h) /*-{
    this.crop(x, y, w, h);
  }-*/;

  /**
   * Loads an image into this Canvas, replacing the Canvas' current dimensions
   * and contents.
   * 
   * @param blob The Blob to decode. The image should be in PNG or JPEG format.
   */
  public final native void decode(Blob blob) /*-{
    this.decode(blob);
  }-*/;

  /**
   * Saves the Canvas' contents to PNG format.
   * 
   * @return A new Blob encoding the Canvas' image data.
   */
  public final native Blob encode() /*-{
    return this.encode();
  }-*/;

  /**
   * Saves the Canvas' contents to a binary format, such as PNG or JPEG.
   * 
   * @param mimeType The image format to encode to. Valid values include
   *          "image/png" and "image/jpeg".
   * @return A new Blob encoding the Canvas' image data.
   */
  public final native Blob encode(String mimeType) /*-{
    return this.encode(mimeType);
  }-*/;

  /**
   * Saves the Canvas' contents to JPEG format.
   * 
   * @param quality the JPEG quality as a number between 0.0 and 1.0 inclusive.
   * @return A new Blob encoding the Canvas' image data.
   */
  public final native Blob encodeJpeg(float quality) /*-{
    return this.encode("image/jpeg", { quality: quality });
  }-*/;

  /**
   * Returns the height of the Canvas. The default value is 150.
   * 
   * @return the canvas height.
   */
  public final native int getHeight()/*-{
    return this.height;
  }-*/;

  /**
   * Returns the width of the Canvas. The default value is 300.
   * 
   * @return the canvas width.
   */
  public final native int getWidth() /*-{
    return this.width;
  }-*/;

  /**
   * Resizes the Canvas. The resize happens "in-place", as opposed to returning
   * a new Canvas.
   * <p>
   * Uses bi-linear filtering.
   * 
   * @param w The new width.
   * @param h The new height.
   */
  public final native void resize(int w, int h) /*-{
    this.resize(w, h);
  }-*/;

  /**
   * Resizes the Canvas. The resize happens "in-place", as opposed to returning
   * a new Canvas.
   * 
   * @param w The new width.
   * @param h The new height.
   * @param filter The image filter.
   */
  public final void resize(int w, int h, ResizeFilter filter) {
    resize(w, h, filter.getFilter());
  }

  /**
   * Resizes the Canvas. The resize happens "in-place", as opposed to returning
   * a new Canvas.
   * 
   * @param w The new width.
   * @param h The new height.
   * @param filter A string specifying the image filter. There are two options:
   *          "nearest" for nearest-neighbor filtering, and "bilinear" for
   *          bi-linear filtering.
   */
  public final native void resize(int w, int h, String filter) /*-{
    this.resize(w, h, filter);
  }-*/;

  /**
   * Sets the height of the Canvas. The default value is 150.
   * 
   * @param height the canvas height.
   */
  public final native void setHeight(int height) /*-{
    this.height = height;
  }-*/;

  /**
   * Sets the width of the Canvas. The default value is 300.
   * 
   * @param width the canvas width.
   */
  public final native void setWidth(int width) /*-{
    this.width = width;
  }-*/;
}
