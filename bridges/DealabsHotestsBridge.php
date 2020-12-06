<?php
class DealabsHotestsBridge extends BridgeAbstract {

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
		$uri = self::URI . '/widget/hottest?selectedRange=' . $this->getInput('group') . '&context=listing';

		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json'
		);

		$json = getContents($uri, $headers)
				or returnServerError('Could not request ' . $uri);
		$json = json_decode($json);
		$threads = $json->data->threads;
		foreach($threads as $thread) {
			$price = number_format(floatval($thread->temperature), 0, ',', ' ');
			$item['uri'] = $thread->url;
			$today = date('d/m/Y');
			$item['title'] = "[$price&deg;] | $thread->price | $thread->title";
			$item['author'] = 'floviolleau';
			$item['uid'] = hash('sha256', $item['title']);
			$this->items[] = $item;
		}
	}

}
