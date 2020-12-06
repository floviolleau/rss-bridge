<?php
class MagazineBiocoopBridge extends BridgeAbstract {

	const NAME = "Magazine de Biocoop Combourg, Dol de Bretagne et Tinteniac";
	const URI = 'https://www.biocooplechatbiotte.com/magazines-le-chat-biotte-combourg.html';
	const DESCRIPTION = "Fetches the latest Magazines of Le Chat Biotté Combourg, Dol-De-Bretagne, Tinteniac";
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

		$containerDom = $html->find('.weMainContent .row.weItemList', 0);
		$publicationNumberDom = $containerDom->find('.weInnerItem');
		foreach($publicationNumberDom as $key => $value) {
			$publicationNumberTitle = $value->find('.h2', 0)->innerText();
			$beginningUrl = 'https://www.biocooplechatbiotte.com';
			$publicationNumberUrl = $beginningUrl . $value->find('a.btn.btn-default.btnDetail', 0)->href;
			$publicationNumberDate = $value->find('.date', 0)->innerText();
			$publicationNumberDate = trim(str_replace('À partir ', '', $publicationNumberDate));
			$message = "Nouveau numéro disponible de biocoop : $publicationNumberTitle $publicationNumberDate";
			$item['uri'] = $publicationNumberUrl;
			$item['title'] = $message;
			$item['author'] = 'floviolleau';
			$item['content'] = $message;
			$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));
			$this->items[] = $item;
		}
	}
}
