<?php
class MagazineValIlleAubigneBridge extends BridgeAbstract {

	const NAME = "Magazine Val d'Ille d'Aubigné";
	const URI = 'https://www.valdille-aubigne.fr/magazine/';
	const DESCRIPTION = "Fetches the latest Magazine of Val d'Ille d'Aubigné from valdille-aubigne.fr";
	const MAINTAINER = 'floviolleau';
	const PARAMETERS = array();
	const CACHE_TIMEOUT = 7200;

	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	public function collectData() {
		$uri = self::URI;

		$html = getSimpleHTMLDOM($uri) or returnServerError('Could not request ' . $uri);

		$containerDom = $html->find('.grid-wrapper', 0);
		$publicationNumberDom = $containerDom->find('.content-box');
		foreach($publicationNumberDom as $key => $value) {
			$publicationNumberUrl = $value->find('a', 0)->href;
			$publicationNumberTitle = $value->find('h4', 0)->innerText();
			$message = "Nouveau numéro disponible du magazine Val d'Ille d'Aubigné : $publicationNumberTitle";
			$item['uri'] = $publicationNumberUrl;
			$item['title'] = $message;
			$item['author'] = 'floviolleau';
			$item['content'] = $message;
			$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));
			$this->items[] = $item;
		}
	}
}
