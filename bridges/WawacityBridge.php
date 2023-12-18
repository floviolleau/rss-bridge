<?php
class WawacityBridge extends BridgeAbstract {

	const NAME = "Wawacity";
	const URI = 'https://wawacity.homes';
	const DESCRIPTION = "Fetches the latest on wawacity";
	const MAINTAINER = 'floviolleau';
    const PARAMETERS = [
        'categorie' => [
            'group' => [
                'name' => 'Groupe',
                'type' => 'list',
                'title' => 'Catégories',
                'values' => [
                    'Exclus' => 0,
                    'Films' => 1,
                    'Blu-ray' => 2,
                    'Séries VOSTFR' => 3,
                    'Séries VF' => 4
                ]
            ]
        ]
    ];
	const CACHE_TIMEOUT = 18000;

	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	public function collectData() {
		$url = self::URI;

		$html = getSimpleHTMLDOM($url) or returnServerError('Could not request ' . $url);

		$containerDom = $html->find('#wa-mid-blocks', 0);
        $blocks = $containerDom->find('.wa-block');
        $block = $blocks[$this->getInput('group')];

		$elementsDom = $block->find('.thumbnail');
		foreach($elementsDom as $key => $elementDom) {
            $uri = $elementDom->href;
            if (substr($uri, 0, 1 ) !== "/") {
                $uri = $url . '/' . $uri;
            } else {
                $uri = $url . $uri;
            }

            $title = $elementDom->find('img', 0)->attr['alt'];
            $imgSrc = $elementDom->find('img', 0)->attr['src'];
            $content = '<a href="' . $uri . '">' . $title . '<br>' . '<img src="' . $url . $imgSrc .  '" /></a>';
    		$item['uri'] = $uri;

			$item['title'] = 'Wawacity : ' . $title;
			$item['author'] = 'floviolleau';
			$item['content'] = $content;
			$item['uid'] = hash('sha256', $title);

			$this->items[] = $item;
		}
	}
}
