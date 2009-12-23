<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div id="left">
   <ul id="album_tree"><?= $album_tree ?></ul>
</div>
<div id="center">
  <div class="wc-toolbar ui-widget-header ui-corner-all ui-helper-clearfix">
    <div class="wc-buttonset wc-buttonset-single ui-helper-clearfix">
      <a href="#" ref="first" class="wc-button ui-state-default ui-state-disabled wc-button-icon-solo ui-corner-left" title="First"><span class="ui-icon ui-icon-seek-first"></span>First</a>
      <a href="#" ref="previous" class="wc-button ui-state-default ui-state-disabled wc-button-icon-solo" title="Previous"><span class="ui-icon ui-icon-seek-prev"></span>Previous</a>
      <a href="#" ref="next" class="wc-button ui-state-default ui-state-disabled wc-button-icon-solo" title="Next"><span class="ui-icon ui-icon-seek-next"></span>Next</a>
      <a href="#" ref="last" class="wc-button ui-state-default ui-state-disabled wc-button-icon-solo ui-corner-right" title="Last"><span class="ui-icon ui-icon-seek-end"></span>Last</a>
    </div>
    <div class="wc-buttonset ui-helper-clearfix">
      <a href="#" ref="parent" class="wc-button ui-state-default ui-state-disabled wc-button-icon-solo ui-corner-all" title="Parent"><span class="ui-icon ui-icon-eject"></span>Parent</a>
    </div>

    <div class="wc-buttonset ui-helper-clearfix">
      <a href="#" ref="edit" class="wc-button ui-state-default wc-button-icon-solo ui-corner-all" title="Edit"><span class="ui-icon ui-icon-pencil"></span>Edit</a>
      <a href="#" ref="delete" class="wc-button ui-state-default wc-button-icon-solo  ui-corner-all" title="Delete"><span class="ui-icon ui-icon-trash"></span>Delete</a>
    </div>

    <div class="wc-buttonset wc-buttonset-single ui-helper-clearfix">
      <a href="#" id="wc-add-resource" ref="add_album" class="wc-button ui-state-active ui-corner-left" ><span>Add Album</span></a>
      <a href="#" id="wc-choose-resource" class="wc-button ui-state-active wc-button-icon-solo ui-corner-right" title="Resources">
        <span class="ui-icon ui-icon-triangle-1-s"></span>
        &nbsp;
      </a>
      <div id="wc-resource-list">
        <ul>
          <li ref="add_album">Add Album</li>
          <li ref="add_photo">Add Photo</li>
          <li ref="add_movie">Add Movie</li>
        </ul>
      </div>
    </div>
  </div>
  <div id="wc-detail">
    <?= $detail ?>
  </div>
</div>
<div id="right">
  <div id="image_block">
    <?= $image_block ?>
  </div>
  <div id="tag_block">
    <?= $tag_block ?>
  </div>
</div>

