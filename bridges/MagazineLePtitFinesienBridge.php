<?php
class MagazineLePtitFinesienBridge extends BridgeAbstract {

	const NAME = "Magazine Le P'tit Finésien";
	const URI = 'https://feins.fr/le-ptit-finesien';
	const DESCRIPTION = "Fetches the latest P'tit Finésien from Feins.fr";
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

		$containerDom = $html->find('.entry-content', 0);
		$yearlyContainerDom = $containerDom->find('.document-gallery');
		foreach($yearlyContainerDom as $key => $yearContainerDom) {
			$publicationNumberDom = $yearContainerDom->find('.document-icon');
			foreach($publicationNumberDom as $key => $value) {
				$publicationNumberUrl = $value->find('a', 0)->href;
				$publicationNumberTitle = $value->find('span', 0)->innerText();
				$publicationNumberTitle = trim(str_replace(' Le Ptit Finesien', '', $publicationNumberTitle));
				$publicationNumberTitle = trim(str_replace('-Ptit-finesien', '', $publicationNumberTitle));
				$publicationNumberTitle = trim(str_replace('-Ptit-Finesien', '', $publicationNumberTitle));
				$publicationNumberTitle = trim(str_replace(" Ptit finesien", '', $publicationNumberTitle));
				$publicationNumberTitle = trim(str_replace(" Le P’tit Finésien", '', $publicationNumberTitle));
				$message = "Nouveau numéro disponible du magazine Le P'tit Finésien: $publicationNumberTitle";
				$item['uri'] = $publicationNumberUrl;
				$item['title'] = $message;
				$item['author'] = 'floviolleau';
				$item['content'] = $message;
				$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));

				$this->items[] = $item;
			}
		}
	}
}
