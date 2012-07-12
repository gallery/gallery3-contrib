<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-session-explorer" class="g-block">
  <h1> <?= t("Top user agents and ips") ?> </h1>
  <p>
    <?= t("Numbers are based on a sample size of %sample_size sessions", array("sample_size" => $sample_size)) ?>
  </p>

  <div class="g-block-content">
    <h2> <?= t("User agents") ?> </h2>
    <table>
      <tr>
        <th> <?= t("Rank") ?> </th>
        <th> <?= t("Count") ?> </th>
        <th> <?= t("User agent") ?> </th>
      </tr>

      <? $rank = 0; ?>
      <? foreach ($uas as $ua => $count): ?>
      <tr class="<?= text::alternate("g-odd", "g-even") ?>">
        <td> <?= ++$rank ?> </td>
        <td> <?= $count ?> </td>
        <td> <?= $ua ?> </td>
      </tr>
      <? endforeach ?>
    </table>
  </div>

  <div class="g-block-content">
    <h2> <?= t("Internet addresses") ?> </h2>
    <table>
      <tr>
        <th> <?= t("Rank") ?> </th>
        <th> <?= t("Count") ?> </th>
        <th> <?= t("Internet address") ?> </th>
      </tr>

      <? $rank = 0; ?>
      <? foreach ($ips as $ip => $count): ?>
      <tr class="<?= text::alternate("g-odd", "g-even") ?>">
        <td> <?= ++$rank ?> </td>
        <td> <?= $count ?> </td>
        <td> <?= $ip ?> </td>
      </tr>
      <? endforeach ?>
    </table>
  </div>
</div>
