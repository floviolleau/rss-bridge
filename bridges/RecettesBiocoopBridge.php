<?php
class RecettesBiocoopBridge extends BridgeAbstract {

	const NAME = "Recettes de Biocoop Combourg, Dol de Bretagne et Tinteniac";
	const URI = 'https://www.biocooplechatbiotte.com/recettes-le-chat-biotte-combourg/1.html';
	const DESCRIPTION = "Fetches the latest Recipes of Le Chat Biotté Combourg, Dol-De-Bretagne, Tinteniac";
	const MAINTAINER = 'floviolleau';
	const PARAMETERS = array();
	const CACHE_TIMEOUT = 0; // 7200;

	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	public function collectData() {
		$uri = self::URI;

		$html = getSimpleHTMLDOM($uri) or returnServerError('Could not request ' . $uri);

		$containerDom = $html->find('.listeRecette', 0);
		$publicationNumberDom = $containerDom->find('.weInnerItem');
		foreach($publicationNumberDom as $key => $value) {
			$publicationNumberTitle = $value->find('a.h3.titre', 0)->innerText();
			$beginningUrl = 'https://www.biocooplechatbiotte.com';
			$publicationNumberUrl = $beginningUrl . $value->find('a.h3.titre', 0)->href;
			$publicationNumberCategory = $value->find('a.btn.btn-default.btnListe.categorie', 0)->innerText();
			$message = "Recette biocoop : [$publicationNumberCategory] $publicationNumberTitle";
			$item['uri'] = $publicationNumberUrl;
			$item['title'] = $message;
			$item['author'] = 'floviolleau';
			$item['content'] = $message;
			$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));
			$this->items[] = $item;
		}
	}
}
