<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-user-search-form").ready(function() {
    var url = $("#g-user-search-results").attr("ref") + "/autocomplete";
    $("#g-user-search-form input:text").autocomplete(
      url, {
        max: 30,
        multiple: false,
        cacheLength: 1
      }
    );
  });
</script>
<div id="g-user-search-results" ref="<?= url::site("photoannotation") ?>">
  <h1><?= t("Search results") ?></h1>
  <?= $search_form ?>
  <? if (count($users)): ?>
  <div class="g-message photoannotation-user-search">
    <?= t("%count people found for <b>%term</b>", array("count" => $count, "term" => $q)) ?>
  </div>
  <? foreach ($users as $user): ?>
  <? $profile_link = "<a href=\"". user_profile::url($user->id) ."\">" ?>
  <div class="g-block">
    <h2><img src="<?= $user->avatar_url(40, $theme->url("images/avatar.jpg", true)) ?>"
       alt="<?= html::clean_attribute($user->display_name()) ?>"
       class="g-avatar" width="40" height="40" />
       <?= $profile_link . $user->name ?></a></h2>
    <div>
      <table class="g-message">
        <tbody>
          <tr>
            <th style="width: 20%"><?= t("Full name") ?></th>
            <td><?= $user->display_name() ?></td>
          </tr>
          <tr>
            <th style="width: 20%"><?= t("Tagged photos") ?></th>
            <td colspan="2"><?= photoannotation::annotation_count($user->id) ?></td>
          </tr>
          <? if (module::is_active("comment")): ?>
          <tr>
            <th style="width: 20%"><?= t("Comments") ?></th>
            <td colspan="2"><?= photoannotation::comment_count($user->id) ?></td>
          </tr>
          <? endif ?>
        </tbody></table>
    </div>
  </div>
  <? endforeach ?>
  <?= $paginator ?>
  <? else: ?>
  <div class="photoannotation-user-search">
    <?= t("No users found for <b>%term</b>", array("term" => $q)) ?>
  </div>
  <? endif; ?>
</div>
