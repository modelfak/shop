server_tokens off;
client_max_body_size 100m;

# BEGIN W3TC Page Cache core
set $w3tc_rewrite 1;
if ($request_method = POST) {
    set $w3tc_rewrite 0;
}
if ($query_string != "") {
    set $w3tc_rewrite 0;
}
if ($request_uri !~ \/$) {
    set $w3tc_rewrite 0;
}
if ($http_cookie ~* "(comment_author|wp\-postpass|w3tc_logged_out|wordpress_logged_in|wptouch_switch_toggle)") {
    set $w3tc_rewrite 0;
}
set $w3tc_preview "";
if ($http_cookie ~* "(w3tc_preview)") {
    set $w3tc_preview _preview;
}
set $w3tc_ssl "";
if ($scheme = https) {
    set $w3tc_ssl _ssl;
}
if ($http_x_forwarded_proto = 'https') {
    set $w3tc_ssl _ssl;
}
set $w3tc_enc "";
if ($http_accept_encoding ~ gzip) {
    set $w3tc_enc _gzip;
}
if (!-f "$document_root/wp-content/cache/page_enhanced/$http_host/$request_uri/_index$w3tc_ssl$w3tc_preview.html$w3tc_enc") {
  set $w3tc_rewrite 0;
}
if ($w3tc_rewrite = 1) {
    rewrite .* "/wp-content/cache/page_enhanced/$http_host/$request_uri/_index$w3tc_ssl$w3tc_preview.html$w3tc_enc" last;
}
# END W3TC Page Cache core