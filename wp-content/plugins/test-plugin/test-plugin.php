<?php
/*
Plugin Name: Test Plugin
*/


function test_plugin_the_content( $content ) {

$info = '<p>＼ 先頭に追記されるよ！ ／</p>';
return $info . $content;
}
add_filter( 'the_content', 'test_plugin_the_content' );

