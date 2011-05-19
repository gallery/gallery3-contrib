/**
 * Set up autocomplete on the server path list
 *
 */
/**
 * rWatcher Edit:  This file used to be admin.js from server_add module.
 * All occurences of server_add have been replaced with videos
 *
 */
$("document").ready(function() {
  $("#g-path").autocomplete(
    base_url.replace("__ARGS__", "admin/videos/autocomplete"), {max: 256});
});
