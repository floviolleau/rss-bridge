<?php
class RecettesBiocoopBridge extends BridgeAbstract {

	const NAME = "Recettes de Biocoop Combourg, Dol de Bretagne et Tinteniac";
	const URI = 'https://www.biocooplechatbiotte.com/recettes';
	const DESCRIPTION = "Fetches the latest Recipes of Le Chat Biotté Combourg, Dol-De-Bretagne, Tinteniac";
	const MAINTAINER = 'floviolleau';
	const PARAMETERS = [];
	const CACHE_TIMEOUT = 7200;

	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	public function collectData() {
		$uri = self::URI;

		$html = getSimpleHTMLDOM($uri) or returnServerError('Could not request ' . $uri);

		$containerDom = $html->find('.liste-page.recette', 0);
		$publicationNumberDom = $containerDom->find('.content');
		foreach($publicationNumberDom as $key => $value) {
			$publicationNumberTitle = $value->find('.side-content h2 a span', 1)->innertext;

			$beginningUrl = 'https://www.biocooplechatbiotte.com';
            $urlItem = $beginningUrl . $value->find('.side-content .lien-detail', 0)->href;

			$publicationNumberCategory = $value->find('.side-content .badge', 0)->innertext;
			$message = "Recette biocoop : [$publicationNumberCategory] $publicationNumberTitle";

			$item['uri'] = $urlItem;
			$item['title'] = $message;
			$item['author'] = 'floviolleau';
			$item['content'] = $message;
			$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));
			$this->items[] = $item;
		}
	}
}
