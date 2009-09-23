Do not display tags beginning with "map." into tag cloud sidebar

static function popular_tags($count) {

->notregex("name","map\.")
