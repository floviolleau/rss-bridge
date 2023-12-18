<?php
class BoisDiversBretagneBridge extends BridgeAbstract {

	const NAME = 'Bois Divers Bretagne';
	const URI = 'https://www.boisdiversbretagne.com/granules-pellets-bois-divers-bretagne-vente-negoce-distribution-livraison-bretagne-normandie-bois-chauffage-plaquettes-forestieres-paillage-granules-bois-pellets-bain-de-bretagne-redon-rennes/';
	const DESCRIPTION = 'Fetches the latest prices for pellets';
	const MAINTAINER = 'floviolleau';
	const PARAMETERS = [];
	const CACHE_TIMEOUT = 0; //18000;

	private function clean($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	public function collectData() {
		$url = self::URI;

		$html = getSimpleHTMLDOM($url) or returnServerError('Could not request ' . $url);

		$blocksDom = $html->find('.wp-block-stackable-column.stk--container-small.stk-block-accordion__heading.stk-block-column.stk-column.stk-block');

		foreach($blocksDom as $key => $blockDom) {
	        $titleDom = $blockDom->find('h4', 0)->innertext;

            $contentDom = $blockDom->parent()->find('div figure', 0) ?: $blockDom->parent()->find('.wp-block-stackable-column.stk-block-accordion__content.stk-block-column.stk-column.stk-block .stk-block-content.stk-inner-blocks', 0);

            foreach($contentDom->find('table tr td') as $cell){
                $cells[] = trim(strip_tags($cell->innertext));
            }

			$message = 'Nouveau tarifs pellets : ' . $titleDom . ' : b' . implode(', ', $cells) . 'b';
			$item['uri'] = $url;
			$item['title'] = $message;
			$item['author'] = 'floviolleau';
			$item['content'] = $contentDom;
			$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));

			$this->items[] = $item;
		}
	}
}
