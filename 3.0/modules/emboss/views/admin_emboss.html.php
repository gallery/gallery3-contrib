<?php defined('SYSPATH') or die('No direct script access.') ?>
/*************************************************************************
 * Copyright (C) 2012  Michel A. Mayer                                   *
 *                                                                       *
 * This program is free software: you can redistribute it and/or modify  *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation, either version 3 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * This program is distributed in the hope that it will be useful,       *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 * GNU General Public License for more details.                          *
 *                                                                       *
 * You should have received a copy of the GNU General Public License     *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 *************************************************************************/
<script language="JavaScript">
function checkAllOverlays()
{
  with(document.options) {
    for(var i=0; i<elements.length; i++) {
      if(elements[i].type=='checkbox' && elements[i].name=='active_overlays[]') {
        elements[i].checked = true;
      }
    }
  }
}

function uncheckAllOverlays()
{
  with(document.options) {
    for(var i=0; i<elements.length; i++) {
      if(elements[i].type=='checkbox' && elements[i].name=='active_overlays[]') {
        elements[i].checked = false;
      }
    }
  }
}
</script>
  
<hr>
<div>
<h2>Upload New Overlay</h2>
<?=form::open_multipart('admin/emboss/new_overlay')?>
<?=access::csrf_form_field()?>
<?=form::upload(array('name'=>'overlay','style'=>'margin: .5em 0 .5em 0'))?>
<?=form::submit(array('name'=>'Upload','style'=>'display:block; float:none'),'Upload')?>
</form>
</div>
<hr>
<div>
<?=form::open('admin/emboss/update',array('name'=>'options'))?>
<?=access::csrf_form_field()?>
<h2>Available Overlays</h2>
<table style="margin:.5em 0 .5em 0">
  <tr>
    <th style>Active</th>
    <th>Image</th>
    <th>Size</th>
    <th>Usage</th>
    <th></th>
  </tr>
  <?php foreach ($images as $image):
    $data['name'] = 'active_overlays[]';
    $data['value'] = $image->name;
    $data['checked'] = $image->active;
  ?>
  <tr class="<?=text::alternate('g-odd','g-even')?>">
    <td><?=form::checkbox($data,1)?></td>
    <td><?=$image->name?></td>
    <td><?=$image->width?> x <?=$image->height?></td>
    <td><?=emboss::usage_count($image->id)?></td>
    <td><?=html::anchor('admin/emboss/delete_overlay?name='.$image->name.'&csrf='.access::csrf_token(), 'delete')?></td></tr>
<? endforeach ?>
<tr class="<?=text::alternate('g-odd','g-even')?>"><td colspan=5>
<a href='javascript:checkAllOverlays()'>Check All</a> / 
<a href='javascript:uncheckAllOverlays()'>Uncheck All</a>
</td></tr>
</table>
<?=form::submit(array('name'=>'Update','style'=>'display:block; float:none'),'Update')?>
<div style="margin:.5cm 0 .5cm 0">
<h2>Embossing Parameters</h2>
<table style="margin:.5em 0 .5em 0">
  <tr><th></th><th></th></tr>
  <tr class="<?=text::alternate('g-odd','g-even')?>">
    <td style="width:15; text-align:right">Best Fit Method:</td>
      <td style="width:85%"><?= form::dropdown('method',array('area'  =>'Maximum Overlay Area',
                                                              'margin'=>'Minimize Borders',
                                                              'diag'  =>'Aspect Ratio Weighted'),
                                               module::get_var('emboss','method','area')) ?></td></tr>
  <tr class="<?=text::alternate('g-odd','g-even')?>">
    <td style="width:15; text-align:right">Location:</td>
      <td style="width:85%"><?=form::dropdown('gravity',array('northwest' => 'Northwest',
                                                              'north'     => 'North',
                                                              'northeast' => 'Northeast',
                                                              'east'      => 'East',
                                                              'southeast' => 'Southeast',
                                                              'south'     => 'South',
                                                              'southwest' => 'Southwest',
                                                              'south'     => 'South',
                                                              'center'    => 'Center'),
                                              module::get_var('emboss','gravity','Center')) ?></td></tr>
<?php for($i=100; $i>0; $i-=5)  { $sizes["$i"]="$i%"; } ?>
<?php for($i=0; $i<100; $i+=10) { $transparencies["$i"]="$i%"; } ?>
  <tr class="<?=text::alternate('g-odd','g-even')?>">
    <td style="width:15; text-align:right">Desired Size:</td>
      <td style="width:85%"><?=form::dropdown('size',$sizes,
                                              module::get_var('emboss','size',85)) ?></td></tr>
  <tr class="<?=text::alternate('g-odd','g-even')?>">
    <td style="width:15; text-align:right">Transparency:</td>
      <td style="width:85%"><?=form::dropdown('transparency',$transparencies,
                                              module::get_var('emboss','transparency',50)) ?></td></tr>
</table>
</div>
<?=form::submit(array('name'=>'Update','style'=>'display:block; float:none'),'Update')?> </form>
</div>
<hr>
<div style="margin:.5cm 0 0 0">
<a href="<?= url::site('admin/emboss/clear_log') ?>"
       title="<?= t('Clear Emboss Log Entries')->for_html_attr() ?>"
       class="g-button ui-icon-left ui-state-default ui-corner-all">
       <?= t('Clear Emboss Log Entries') ?>
</a>
</div>
