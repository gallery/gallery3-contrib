/*
 * Copyright 2008 Google Inc.
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

/**
 * Factory class used to create all other Gears objects.
 */
public final class Factory extends JavaScriptObject {
  /**
   * String used to request a BlobBuilder instance from Gears.
   */
  public static final String BLOBBUILDER = "beta.blobbuilder";

  /**
   * String used to request a Canvas instance from Gears.
   */
  public static final String CANVAS = "beta.canvas";


  /**
   * Returns the singleton instance of the Factory class or <code>null</code>
   * if Gears is not installed or accessible.
   * 
   * @return singleton instance of the Factory class or <code>null</code> if
   *         Gears is not installed or accessible
   */
  public static native Factory getInstance() /*-{
    return $wnd.google && $wnd.google.gears && $wnd.google.gears.factory;
  }-*/;

  protected Factory() {
    // Required for overlay types
  }

  /**
   * Creates a new {@link Canvas} instance.
   *
   * @return a new {@link Canvas} instance
   */
  public Canvas createCanvas() {
    return create(CANVAS);
  }

  /**
   * Creates an instance of the specified Gears object.
   * 
   * @param <T> Gears object type to return
   * @param className name of the object to create
   * @return an instance of the specified Gears object
   */
  private native <T extends JavaScriptObject> T create(String className) /*-{
    return this.create(className);
  }-*/;
}