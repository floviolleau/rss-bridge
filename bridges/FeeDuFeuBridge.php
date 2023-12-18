<?php
class FeeDuFeuBridge extends BridgeAbstract {

	const NAME = 'Fee du feu 35';
	const URI = 'https://www.feedufeu.com/vente-en-ligne-granules-pellets.php';
	const DESCRIPTION = 'Fetches the latest prices for pellets';
	const MAINTAINER = 'floviolleau';
	const PARAMETERS = [[                                                                                                                                                                                         
        'quantity' => [
            'name' => 'Quantite',
            'required' => true,
            'exampleValue'  => '1'
        ]                                                                                                                                                                                                         
    ]];
	const CACHE_TIMEOUT = 0; //18000;

    private function clean($string) {                                                                                                                                                                             
        $string = str_replace(' ', '-', $string);                                                                                                                                                                 
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);                                                                                                                                                     
    }

	public function collectData() {
		$url = 'https://www.feedufeu.com/fdf-bo/includes/getPrice.php?dep=35&qte=' . $this->getInput('quantity');

		$html = getSimpleHTMLDOM($url) or returnServerError('Could not request ' . $url);
		$html = preg_replace('/<br>/', ' ', $html);
		$message = 'Nouveau tarifs pellets Palette de 65 sacs (avec livraison) : ' . $html;
		$item['uri'] = self::URI;
		$item['title'] = $message;
		$item['author'] = 'floviolleau';
		$item['content'] = $message;
		$item['uid'] = hash('sha256', strtolower($this->clean($item['title'])));

		$this->items[] = $item;
	}
}
