<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<style>
  #gExifData { width: 100%; height: 100%; }
  .gOdd  {background-color: #484848; color: #e8e8e8; height:35px; padding:5px; font-family:arial; font-size: 10px;}
  .gEven {background-color: #333333; color: #e8e8e8; height:35px; padding:5px; font-family:arial; font-size: 10px;}
  .gExifInfoTable {	width: 100%; height: 100%; background-color: #e8e8e8; border-spacing: 1px; text-align: left;}
</style>
</head>
<body>
<div id="gExifData">
  <center>
  <table class="gExifInfoTable">
    <tbody>
      <? for ($i = 0; $i < count($details); $i++): ?>
      <tr>
         <td class="gEven">
         <?= $details[$i]["caption"] ?>
         </td>
         <td class="gOdd">
         <?= html::clean($details[$i]["value"]) ?>
         </td>
         <? if (!empty($details[++$i])): ?>
           <td class="gEven">
           <?= $details[$i]["caption"] ?>
           </td>
           <td class="gOdd">
           <?= html::clean($details[$i]["value"]) ?>
           </td>
         <? else: ?>
           <td class="gEven"></td><td class="gOdd"></td>
         <? endif ?>
       </tr>
       <? endfor ?>
    </tbody>
  </table>
 </center>
</div>
</body>
</html>
