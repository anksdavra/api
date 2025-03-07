<?php

$container = new Pimple\Container();
global $main_container;

$container['wp_query'] = function() {
	return new \WP_Query();
};

$container['post_service'] = $main_container['post_service'];
$container['post_service']->set_wp_query($container['wp_query']);
$main_container['post_helper']->set_wp_query($container['wp_query']);

$container['image_helper'] = $main_container['image_helper'];
$container['post_helper'] = $main_container['post_helper'];
$container['seo_schema_generator'] = $main_container['seo_schema_generator'];

$container['preview_hash_generator'] = function() {
	return new WpTapestryPlugin\Service\PreviewHashGenerator();
};

$container['post_api'] = function($c) {
	return new CroissantApi\Endpoint\PostApi($c['post_service']);
};

$container['categories_api'] = function($c) {
	return new CroissantApi\Endpoint\CategoriesApi($c['seo_schema_generator']);
};

$container['category_hub'] = function($c) {
	return new CroissantApi\Endpoint\CategoryHubApi($c['post_list_api']);
};

$container['trends_api'] = function($c) {
	return new CroissantApi\Endpoint\TrendsApi(
		$c['image_helper'],
		$c['post_service']
	);
};
$container['emails_api'] = function($c) {
	return new CroissantApi\Endpoint\EmailsApi(
		$c['post_service'],
		$c['preview_hash_generator'],
		$c['image_helper']
	);
};

$container['unsubscribe_api'] = function($c) {
	return new CroissantApi\Endpoint\UnsubscribeApi();
};

$container['membership_api'] = function($c) {
	return new CroissantApi\Endpoint\MembershipApi(
		$c['image_helper'],
		$c['post_helper'],
		$c['post_service']
	);
};

$container['pages_api'] = function($c) {
	$class = new CroissantApi\Endpoint\PagesApi();
	$class->set_wp_query($c['wp_query']);
	return $class;
};

$container['post_list_api'] = function($c) {
	return new CroissantApi\Endpoint\PostListApi($c['post_service']);
};

$container['series_api'] = function($c) {
	return new CroissantApi\Endpoint\SeriesApi(
		$c['post_service'],
		$c['image_helper'],
		$c['seo_schema_generator']
	);
};

$container['tagbot_categories'] = function() {
	return new CroissantApi\Endpoint\TagbotCategoriesApi();
};

$container['tags_api'] = function($c) {
	return new CroissantApi\Endpoint\TagsApi($c['seo_schema_generator']);
};

$container['printissue_api'] = function($c) {
	return new CroissantApi\Endpoint\PrintIssueApi();
};

$container['verticals_api'] = function($c) {
	return new CroissantApi\Endpoint\VerticalsApi($c['post_service'], $c['wp_query']);
};

$container['settings_api'] = function($c) {
	return new CroissantApi\Endpoint\SettingsApi($c['image_helper']);
};

$container['users_api'] = function($c) {
	return new CroissantApi\Endpoint\UsersApi($c['seo_schema_generator']);
};

$container['search_api'] = function($c) {
	return new CroissantApi\Endpoint\SearchApi($c['post_service']);
};

$container['popular_posts_endpoint'] = function() {
	return getenv('POPULAR_POSTS_DOMAIN');
};

$container['popular_api'] = function($c) {
	return new CroissantApi\Endpoint\PopularApi($c['post_service'], $c['popular_posts_endpoint']);
};

$container['campaign_api'] = function($c) {
	return new CroissantApi\Endpoint\CampaignApi($c['wp_query']);
};

$container['signing_secret'] = function() {
	return getenv('SIGNING_SECRET');
};

$container['slack_team'] = function() {
	return getenv('SLACK_TEAM_ID');
};

$container['slack_channel'] = function() {
	return getenv('SLACK_BOT_CHANNEL');
};

$container['slackbot_create_user_api']= function($c) {
	return new CroissantApi\Slackbot\CreateUserApi($c['signing_secret'], $c['slack_team'], $c['slack_channel']);
};

$container['slackbot_user_lookup_api'] = function($c) {
	return new CroissantApi\Slackbot\UserLookupApi($c['signing_secret'], $c['slack_team'], $c['slack_channel']);
};

$container['slackbot_interactive_api']= function($c) {
	return new CroissantApi\Slackbot\InteractiveApi($c['signing_secret'], $c['slack_team'], $c['slack_channel']);
};

$container['slackbot_post_lookup_api'] = function($c) {
	return new CroissantApi\Slackbot\PostLookupApi($c['signing_secret'], $c['slack_team'], $c['slack_channel'], $c['wp_query']);
};


// v2 endpoints
$container['post_list_api_v2'] = function($c) {
	return new CroissantApi\Endpoint\V2\PostListApiV2($c['post_service']);
};

$container['how_to_posts_api'] = function($c) {
	return new CroissantApi\Endpoint\V2\HowToPostsApi(
		$c['post_list_api_v2'],
		$c['image_helper']
	);
};

$container['hub_page'] = function($c) {
	return new CroissantApi\Endpoint\V2\HubPageApi($c['post_list_api_v2']);
};

$container['popup_decorator'] = function($c) {
	return new CroissantApi\PostTypeDecorator\Popup(
		$c['image_helper'],
		$c['post_helper']
	);
};

$container['popup'] = function($c) {
	return new CroissantApi\Endpoint\V2\PopupApi(
		$c['post_service'],
		$c['popup_decorator']
	);
};

$container['course_categories_api'] = function($c) {
	return new CroissantApi\Endpoint\V2\CourseCategoriesApi();
};

$container['homepage_api'] = function($c) {
	return new CroissantApi\Endpoint\V2\HomepageApi(
		$c['post_service']
	);
};

$container['homepage_api_v3'] = function($c) {
	return new CroissantApi\Endpoint\V3\HomepageApi(
		$c['post_service']
	);
};

$container['swagger_api'] = function($c) {
	return new CroissantApi\Endpoint\V3\SwaggerEndpoint();
};

$container['category_page'] = function($c) {
	return new CroissantApi\Endpoint\V2\CategoryPageApi($c['post_service'], $c['image_helper']);
};



return $container;
