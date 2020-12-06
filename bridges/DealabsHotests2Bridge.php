<?php
class DealabsHotests2Bridge extends BridgeAbstract {

	const NAME = 'Dealabs Hotests Bridge';
	const URI = 'https://www.dealabs.com';
	const DESCRIPTION = 'Affiche les Deals les + hots de Dealabs';
	const MAINTAINER = 'flovioleau';

	const PARAMETERS = array(
		'Tri les + hot' => array(
			'group' => array(
				'name' => 'Groupe',
				'type' => 'list',
				'title' => 'Groupe dont il faut afficher les deals',
				'values' => array(
					'Jour' => 'day',
					'Semaine' => 'week',
					'Mois' => 'month',
					'Tout' => 'overall'
				)
			)
		)
	);

	const CACHE_TIMEOUT = 86400; //every 24h 28800;// every 8hr or 7200 is every 2hr;

	public function collectData() {
		$uri = self::URI . '/graphql';
		$staticPepperUri = 'https://static-pepper.dealabs.com';

		// get cookie from root
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::URI);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0');
		curl_setopt($ch, CURLOPT_HEADER  ,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$content = curl_exec($ch);

		// get cookies
		$cookies = array();
		preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $content, $cookies);

		$cfduidCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][0] : '';
		$matchArray = [];
		preg_match_all('/(.*?;)/im', $cfduidCookie, $matchArray);
		$cfduidCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$pepperSessionCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][1] : '';
		preg_match_all('/(.*?;)/im', $pepperSessionCookie, $matchArray);
		$pepperSessionCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$ulCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][2] : '';
		preg_match_all('/(.*?;)/im', $ulCookie, $matchArray);
		$ulCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$xsrfCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][3] : '';
		preg_match_all('/(.*?;)/im', $xsrfCookie, $matchArray);
		$xsrfCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$fvCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][4] : '';
		preg_match_all('/(.*?;)/im', $fvCookie, $matchArray);
		$fvCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$cfBmCookie = sizeof($cookies['cookie']) > 0 ? $cookies['cookie'][5] : '';
                preg_match_all('/(.*?;)/im', $cfBmCookie, $matchArray);
                $cfBmCookie = sizeof($matchArray) > 0 ? $matchArray[1][0] : '';

		$naviCookie = 'navi=%7B%22hottest-widget-time%22%3A%22' . $this->getInput('group') . '%22%7D;';
		$cookiePolicy = 'cookie_policy_agreement=3;';
		$dontTrackCookie = 'dont-track=0;';
		$fcCookie = 'f_c=1;';
		$gpCookie = 'g_p=1;';
		$viewLayoutCookie = 'view_layout_horizontal=%221-1%22;';
		$showTabCookie = 'show_my_tab=0;';

		$dealabsCookie = $cfduidCookie.$pepperSessionCookie.$ulCookie.$xsrfCookie.$fvCookie.$cfBmCookie.$cookiePolicy.$dontTrackCookie.$fcCookie.$gpCookie.$viewLayoutCookie.$showTabCookie.$naviCookie;

		$xsrfCookieArray = [];
		preg_match_all('/\%22(.*)\%22/im', $xsrfCookie, $xsrfCookieArray);
		$xsrfToken = sizeof($xsrfCookieArray) > 0 ? $xsrfCookieArray[1][0] : '';

		$query_json = '{"query":"\n    query HottestWidget($filter: ThreadFilter!, $threadImageSlot: String!, $threadImageVariations: [String!]!, $merchantImageSlot: String!, $merchantImageVariations: [String!]!) {\n  hottestWidget(filter: $filter) {\n    options {\n      text\n      value\n    }\n    selected {\n      text\n      value\n    }\n    scope {\n      scope\n      id\n    }\n    layout {\n      display\n      long\n      position {\n        current\n        card\n        list\n      }\n      order\n      view\n    }\n    threads {\n      threadId\n      threadTypeId\n      title\n      titleSlug\n      price\n      displayPrice\n      discountType\n      temperature\n      imageUrls(slot: $threadImageSlot, variations: $threadImageVariations)\n      url\n      nsfw\n      merchant {\n        merchantId\n        imageUrls(slot: $merchantImageSlot, variations: $merchantImageVariations)\n      }\n    }\n    nofollow\n  }\n}\n    ","variables":{"filter":{"range":{"eq":"' . $this->getInput('group') . '"}},"threadImageSlot":"default","threadImageVariations":["thread_list_big"],"merchantImageSlot":"avatar_32","merchantImageVariations":["avatar_app_square_32_2x"]}}';
                $ch = curl_init($uri);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIE, $dealabsCookie);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json, text/plain, */*',
				'X-Pepper-Txn: index',
				'X-Request-Type: application/vnd.pepper.v1+json',
				'X-Requested-With: XMLHttpRequest',
				"X-XSRF-TOKEN: $xsrfToken"
                        )
                );

                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		// force HTTP/1.1
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

		// activate debug
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//$verbose = fopen('php://temp', 'w+');
		//curl_setopt($ch, CURLOPT_STDERR, $verbose);
                $response = curl_exec($ch);

		// read debug
		//rewind($verbose);
		//$verboseLog = stream_get_contents($verbose);
		//echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

                curl_close($ch);

		$json = json_decode($response);

		$threads = $json->data->hottestWidget->threads;
		foreach($threads as $thread) {
			$price = number_format(floatval($thread->temperature), 0, ',', ' ');
			$item['uri'] = $thread->url;
			$today = date('d/m/Y');
			$item['title'] = "[$price&deg;] | $thread->displayPrice | $thread->title";
			$item['author'] = 'floviolleau';
			$item['uid'] = hash('sha256', $item['title']);
			$item['content'] = $thread->imageUrls ? '<div style="text-align:center"><a href="' . $thread->url . '"><img src="' . $staticPepperUri.$thread->imageUrls->{'default.thread_list_big'} . '"></a></div>' : '';
			$this->items[] = $item;
		}
	}
}
